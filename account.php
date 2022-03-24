<?php

function transaction_revert($database_values, $old_balance){
	$database_values["TransactionType"] = -1;
	$database_values["balance"] = $old_balance;
	$database_values["changeCredit"] *= -1;
	
	$result = $db->insert("fh_ledger", $database_values);
	
	if(!$result){
		//if the revert fails then it's really bad
		echo '<h1 class="announcement">Something has gone terribly wrong with the database. Please speak to an administrator.</h1>';
		return false;
	}
	
	return true;
}

function transaction($database_values, $old_balance){
	$result = $db->insert("fh_ledger", $database_values);
	
	if(!$result){
		echo '<h1 class="announcement">There has been an error with the transaction. Try again or speak to an administrator.</h1>';
		return false;
	}

	//making sure there hasn't been an error with the original query
	$latest_transaction = $db->query("fh_ledger", ["balance"], "userID = '" . $database_values["userID"] . "' LIMIT 1", orderBy : ["id" => "DESC"]);
	
	if(!$latest_transaction || $latest_transaction[0]["balance"] != $database_values["balance"]){
		//if there was, then the transaction is not correct and needs to be reverted
		if(!transaction_revert($database_values, $old_balance)){
			return false;
		}

		echo '<h1 class="announcement">There has been an error with the transaction. Try again or speak to an administrator.</h1>';
		return false;
	}

	return true;
}

echo "<h1>Account Details</h1>";

if (isset($uid)){

    if (isset($_POST['submit'])){

    	foreach ($_POST as $key => $value){

    		if ($key != "submit"){

    		    if ($key == "dob"){

    		        if ($value != NULL){

    		            $value = trim(strtotime($value));

    		        }else{

    		            $value = 0;

    		        }

	    	    }

    			$database_values[$key] = $value;	

    		}

	    }

    	$saved = $db->update("fh_users", $database_values, "id='".$uid . "'");	

    	if ($saved){

    	    echo '<h1 class="announcement">Account Information Updated</h1>';

    	}

    }
	elseif(isset($_POST['top up submit']) && isset($_POST['top up credits'])){
		if($_POST['top up credits'] > 0){
			$latest_transaction = $db->query("fh_ledger", ["balance"], "userID = '" . $uid . "' LIMIT 1", orderBy : ["id" => "DESC"]);
			$old_balance = $latest_transaction? $latest_transaction[0]["balance"] : 0;
			$new_balance = $old_balance + $_POST['top up credits'];
			$database_values = ["userID" => $uid, "advertID" => -1, "TransactionType" => 0, "ip_address" => $_SERVER['REMOTE_ADDR'],
			"timestamp" => time(), "viewed" => 0, "balance" => $new_balance, "changeCredit" => $_POST['top up credits']];
			
			$result = transaction($database_values, $old_balance);
			
			if($result){
				echo '<h1 class="announcement">Transaction Successful!</h1>';
			}
		}
		else{
			echo '<h1 class="announcement">Error, cannot top up 0 credits.</h1>';
		}
	}
	elseif(isset($_POST['return item']) && isset($_POST['loan_id'])){
		$item = $db->query("fh_items_loaned", ["itemID", "loaner_id"], "id = '" . $_POST['loan_id'] . "'");
		
		//making sure the user who owns the item is the one making the request to return it
		if(!$item || $item[0]["loanerID"] != $uid){
			echo '<h1 class="announcement">There has been an error while trying to return the item. Try again or speak to an administrator.</h1>';
		}
		elseif(count($item) > 1){
			echo '<h1 class="announcement">Something has gone terribly wrong with the database. Please notify an administrator.</h1>'; 
		}
		else{
			$advert = $db->query("fh_adverts", ["userID", "credits"], "Id = '" . $item["itemID"] . "'");
			
			if(!$advert or count($advert) > 1){
				//if no advert is found, then that means it's been deleted from the database, without taking care of
				//loaned items, which is bad.
				echo "Something has gone terribly wrong with the database. Please notify an administrator.";
			}
			else{
				//the exact formula still needs to be implemented. just putting it as credits per day for now
				//also should change it so it grabs the credits value from the moment it was loaned, not from the current advert
				$credits = $advert[0]["credits"] * $item["amount_loaned"] * ($item["timestamp"] - time()) / 86400; //86400 is the amount of seconds in a day
				
				$loaner_latest_transaction = $db->query("fh_ledger", ["balance"], "userID = '" . $uid . "' LIMIT 1", orderBy : ["id" => "DESC"]);
				$loaner_old_balance = $loaner_latest_transaction? $loaner_latest_transaction[0]["balance"] : 0;
				$loaner_new_balance = $loaner_old_balance - $credits;
				$loaner_database_values = ["userID" => $uid, "advertID" => $item["itemID"], "TransactionType" => 1, "ip_address" => $_SERVER['REMOTE_ADDR'],
				"timestamp" => time(), "viewed" => 0, "balance" => $loaner_new_balance, "changeCredit" => -$credits];
				
				$result = transaction($loaner_database_values, $loaner_old_balance);
				
				if($result){
					//everything is good for the loaner. now need to give the credits to the borrower
					$borrower_latest_transaction = $db->query("fh_ledger", ["balance"], "userID = '" . $advert["userID"] . "' LIMIT 1", orderBy : ["id" => "DESC"]);
					$borrower_old_balance = $borrower_latest_transaction? $borrower_latest_transaction[0]["balance"] : 0;
					$borrower_new_balance = $borrower_old_balance + $credits;
					$borrower_database_values = ["userID" => $advert["userID"], "advertID" => $item["itemID"], "TransactionType" => 2, "ip_address" => $_SERVER['REMOTE_ADDR'],
					"timestamp" => time(), "viewed" => 0, "balance" => $borrower_new_balance, "changeCredit" => $credits];
					
					$result = transaction($borrower_database_values, $borrower_old_balance);
					
					if($result){
						echo '<h1 class="announcement">Item Successfuly returned. "' . $credits . '" have been deducted.</h1>';
					}
					else{
						transaction_revert($loaner_database_values, $loaner_old_balance);
					}
				}
			}
		}
	}

    $user = $db->getRow("fh_users", "id=" . $uid);

    echo "<form id='regform' name='registrationform' method='post' action='index.php?page=account'>";

    echo "<table>";

    echo "<tr>";

    echo "<tr><td>Username:</td><td>" . $user['username'] . "</td></tr>";

    echo "<tr><td>Name:</td><td><input type=\"text\" name=\"name\" value=\"" . $user['name'] . "\"></td></tr>";

    echo "<tr><td>Surname:</td><td><input type=\"text\" name=\"surname\" value=\"" . $user['surname'] . "\"></td></tr>";

    echo "<tr><td>Password:</td><td><input type=\"password\" name=\"password\" value=\"" . $user['password'] . "\" placeholder\"Enter new password\"></td></tr>";

    echo "<tr><td>Phone 1:</td><td><input type=\"text\" name=\"phone1\" value=\"" . $user['phone1'] . "\"></td></tr>";

    echo "<tr><td>Phone 2:</td><td><input type=\"text\" name=\"phone2\" value=\"" . $user['phone2'] . "\"></td></tr>"; 

    echo "<tr><td>Address Line 1:</td><td><input type=\"text\" name=\"address_line1\" value=\"" . $user['address_line1'] . "\"></td></tr>";

    echo "<tr><td>Address Line 2:</td><td><input type=\"text\" name=\"address_line2\" value=\"" . $user['address_line2'] . "\"></td></tr>";

    echo "<tr><td>Address Line 3:</td><td><input type=\"text\" name=\"address_line3\" value=\"" . $user['address_line3'] . "\"></td></tr>";

    echo "<tr><td>Country:</td><td><input type=\"text\" name=\"country\" value=\"" . $user['country'] . "\"></td></tr>";

    echo "<tr><td>Post Code:</td><td><input type=\"text\" name=\"postcode\" value=\"" . $user['postcode'] . "\"></td></tr>";

    echo "<tr><td>Email:</td><td><input type=\"email\" name=\"email\" value=\"" . $user['email'] . "\"></td></tr>";

    echo "<tr><td>D.O.B:</td><td><input type=\"date\" name=\"dob\" value=\"" . $user['dob'] . "\"></td></tr>";

    echo "</table>";

    echo "<input type=\"submit\" value=\"Submit\" name='submit' class=\"btn\">";

    echo "</form>";
	
	echo '<br>';
	echo '<form action="?page=account" method="post">';
	echo '<table>';
	$latest_transaction = $db->query("fh_ledger", ["balance"], "userID = '" . $uid . "' LIMIT 1", orderBy : ["id" => "DESC"]);
	$credits = $latest_transaction? $latest_transaction[0]["balance"] : 0;
	echo '<tr>';
	echo '<td>Credits: ' . $credits . '</td>';
	echo '<td style = "width:50%"><input type = "number" name = "top up credits" placeholder = "Enter Credits To Top Up" min = "0.0001" step = "0.0001" required></td>';
	echo '<td><input type = "submit" name = "top up submit" value = "Top Up" class = "form_button"></input></td>';
	echo '</tr><table>';
	echo '</form>';

    echo "<h1>Items Borrowed</h1>";
	
	$items = $db->query("fh_items_loaned", NULL, "loanerID = '" . $uid . "'", orderBy : ["id" => "DESC"]);
	
	if($items){
		echo '<table><tr>';

		foreach($items[0] as $key => $value){
			echo '<th>' . $key . '</th>';
		}
		
		echo '</tr>';
		
		foreach($items as $index => $item){
			echo '<tr>';

			foreach($item as $key => $value){
				echo '<td>' . $value . '</td>';
			}
			
			$advert = $db->query("fh_adverts", ["credits"], "Id = '" . $item["itemID"] . "'");
			
			if(!$advert or count($advert) > 1){
				//if no advert is found, then that means it's been deleted from the database, without taking care of
				//loaned items, which is bad.
				echo "Something has gone terribly wrong with the database. Please notify an administrator.";
				echo "</tr>";
				break;
			}

			//the exact formula still needs to be implemented. just putting it as credits per day for now
			//also should change it so it grabs the credits value from the moment it was loaned, not from the current advert
			$credits = $advert[0]["credits"] * $item["amount_loaned"] * ($item["timestamp"] - time()) / 86400; //86400 is the amount of seconds in a day
			
			echo '<td>Current credits cost: ' . $credits .' (Note this value may change by the time the return process is completed)</td>';
			
			echo '<td><form action="?page=account" method="post">';
			echo '<input type = "hidden" name = "loan_id" value = "' . $item["id"] . '"</input>'; //the user doesn't have to input anything
			echo '<input type = "submit" name = "return item" value = "Return Item" class = "form_button"></input>';
			echo '</td>';

			echo '</tr>';
		}

		echo '</table>';
	}
	else{
		echo "<p>NONE</p>";
	}

    echo "<h1>Items Loanings</h1>";
	
	$items = $db->query("fh_adverts", NULL, "userID = '" . $uid . "'", orderBy : ["id" => "DESC"]);
	
	if($items){
		echo '<table><tr>';

		foreach($items[0] as $key => $value){
			echo '<th>' . $key . '</th>';
		}
		
		echo '</tr>';
		
		foreach($items as $index => $item){
			echo '<tr>';

			foreach($item as $key => $value){
				echo '<td>' . $value . '</td>';
			}

			echo '</tr>';
		}

		echo '</table>';
	}
	else{
		echo "<p>NONE</p>";
	}

    echo "<h1>My Credit Ledger</h1>";
	
	$transactions = $db->query("fh_ledger", NULL, "userID = '" . $uid . "'", true, ["id" => "DESC"]);
	
	if($transactions){
		echo '<table><tr>';

		foreach($transactions[0] as $key => $value){
			echo '<th>' . $key . '</th>';
		}
		
		echo '</tr>';
		
		foreach($transactions as $index => $transaction){
			echo '<tr>';
			
			$transaction["viewed"] = 1;

			$result = $db->update("fh_ledger", $transaction, "id = '" . $transaction["id"] . "'");
			
			if(!$result){
				echo "There has been an error while updating the credit ledger. Please try again or speak with an administrator.";
				echo "</tr>";
				break;
			}
			
			foreach($transaction as $key => $value){
				echo '<td>' . $value . '</td>';
			}

			echo '</tr>';
		}

		echo '</table>';
	}
	else{
		echo "<p>NONE</p>";
	}
}

// developed by Adam MacKay 2000418 - 14/03/22

?>
