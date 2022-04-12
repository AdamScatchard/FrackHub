<?php
	if (!isset($uid)){
		header("location:?page=unauthorised_access");
		exit();
	}

	function transaction_revert($database_values, $old_balance){
		$database_values["TransactionType"] = -1;
		$database_values["balance"] = $old_balance;
		$database_values["changeCredit"] *= -1;
		$result = $GLOBALS["db"]->insert("fh_ledger", $database_values);
		
		if(!$result){
			//if the revert fails then it's really bad
			echo '<h1 class="announcement">Something has gone terribly wrong with the database. Please speak to an administrator.</h1>';
			return false;
		}
		
		return true;
	}

	function transaction($database_values, $old_balance){
		$result = $GLOBALS["db"]->insert("fh_ledger", $database_values);
		
		if(!$result){
			echo '<h1 class="announcement">There has been an error with the transaction. Try again or speak to an administrator.</h1>';
			return false;
		}

		//making sure there hasn't been an error with the original query
		$latest_transaction = $GLOBALS["db"]->query("fh_ledger", ["balance"], "userID = '" . $database_values["userID"] . "'", false, ["id" => "DESC"],  1);

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
	if (isset($_POST['close'])){
		$db->update("fh_users", ["active"=>0], "id=" . $uid);
		setcookie($login_cookie, "", time()-3600);
		setcookie($session_code, "", time()-3600);
		echo "<h1>Your account has been closed down</h1>";
		die();
	}
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
	    	    
				if ($key == "password"){
				    $encryption->setPlainText($value . $_POST["username"] . $_POST["email"] . time());
				    $value = $encryption->classRun();
				}
    			$database_values[$key] = $value;	

    		}

	    }

    	$saved = $db->update("fh_users", $database_values, "id='".$uid . "'");	

    	if ($saved){
    	    echo '<h1 class="announcement">Account Information Updated</h1>';
    	}

    }
	elseif(isset($_POST['top_up_submit']) && isset($_POST['top_up_credits'])){
		if($_POST['top_up_credits'] > 0){
			$latest_transaction = $db->query("fh_ledger", ["balance"], "userID = '" . $uid . "'", ["id" => "DESC"], 1);
			$old_balance = $latest_transaction? $latest_transaction[0]["balance"] : 0;
			$new_balance = $old_balance + $_POST['top_up_credits'];
			$database_values = ["userID" => $uid, "advertID" => -1, "TransactionType" => 0, "ip_address" => $_SERVER['REMOTE_ADDR'],
			"timestamp" => time(), "viewed" => 0, "balance" => $new_balance, "changeCredit" => $_POST['top_up_credits']];
			
			$result = transaction($database_values, $old_balance);
			
			if($result){
				echo '<h1 class="announcement">Transaction Successful!</h1>';
			}
		}
		else{
			echo '<h1 class="announcement">Error, cannot top up 0 credits.</h1>';
		}
	}
	elseif(isset($_POST['return_item'])){
		$items_loaned = $db->query("fh_items_loaned", "id = '" . $_POST['return_item'] . "'", true);

		//making sure the user who owns the item is the one making the request to return it
		if(!$items_loaned || $items_loaned[0]["loanerID"] != $uid){
			echo '<h1 class="announcement">There has been an error while trying to return the item. Try again or speak to an administrator.</h1>';
		}
		elseif(count($items_loaned) > 1){
			echo '<h1 class="announcement">Something has gone terribly wrong with the database. Please notify an administrator.</h1>'; 
		}
		else{
			$item_loaned = $items_loaned[0];
			$original_adverts = $db->query("fh_adverts", "Id = '" . $item_loaned["itemID"] . "'", true);
			
			if(!$original_adverts or count($original_adverts) > 1){
				//if no advert is found, then that means it's been deleted from the database, without taking care of
				//loaned items, which is bad.
				echo "Something has gone terribly wrong with the database. Please notify an administrator.";
			}
			else{
				$original_advert = $original_adverts[0];

				//the exact formula still needs to be implemented. just putting it as credits per day for now
				//also should change it so it grabs the credits value from the moment it was loaned, not from the current advert
				$credits = $original_advert["credits"] * $item_loaned["amount_loaned"] * (time() - $item_loaned["timestamp"]) / 86400; //86400 is the amount of seconds in a day
				//putting it to integer since that's what the database uses
				$credits = (int) $credits;

				$loaner_latest_transaction = $db->query("fh_ledger", ["balance"], "userID = '" . $uid . "'", ["id" => "DESC"],1);
				$loaner_old_balance = $loaner_latest_transaction? $loaner_latest_transaction[0]["balance"] : 0;
				$loaner_new_balance = $loaner_old_balance - $credits;
				$loaner_database_values = ["userID" => $uid, "advertID" => $item_loaned["itemID"], "TransactionType" => 1, "ip_address" => $_SERVER['REMOTE_ADDR'],
				"timestamp" => time(), "viewed" => 0, "balance" => $loaner_new_balance, "changeCredit" => -$credits];
				
				$result = transaction($loaner_database_values, $loaner_old_balance);
				
				if($result){
					//everything is good for the loaner. now need to give the credits to the borrower
					$borrower_latest_transaction = $db->query("fh_ledger", ["balance"], "userID = '" . $original_advert["userID"] . "'",  ["id" => "DESC"], 1);
					$borrower_old_balance = $borrower_latest_transaction? $borrower_latest_transaction[0]["balance"] : 0;
					$borrower_new_balance = $borrower_old_balance + $credits;
					$borrower_database_values = ["userID" => $original_advert["userID"], "advertID" => $item_loaned["itemID"], "TransactionType" => 2, "ip_address" => $_SERVER['REMOTE_ADDR'],
					"timestamp" => time(), "viewed" => 0, "balance" => $borrower_new_balance, "changeCredit" => $credits];
					
					$result = transaction($borrower_database_values, $borrower_old_balance);
					$error = false;

					if($result){
						$updated_advert = $original_advert;
						$updated_advert["amount_available"] += $item_loaned["amount_loaned"];
						
						if($updated_advert["available"] == 0 && $updated_advert["amount_available"] > 0){
							$updated_advert["available"] = 1;
						}

						$result = $db->update("fh_adverts", $updated_advert, "id = '" . $item_loaned["itemID"] . "'");
						
						if($result){
							$result = $db->delete("fh_items_loaned", "id = '" . $item_loaned["id"] . "'");		
							if($result){
								echo '<h1 class="announcement">Item Successfuly returned. "' . $credits . '" credits have been deducted.</h1>';
							}
							else{
								$error = true;
							}
							
							if($error){
								$result = $db->update("fh_adverts", $original_advert, "id = '" . $item_loaned["itemID"] . "'");
								
								if(!$result){
									//if the revert fails, it's bad
									echo '<h1 class="announcement">Something has gone terribly wrong with the database. Please notify an administrator.</h1>';
								}
							}
						}
						else{
							$error = true;
						}
						
						if($error){
							transaction_revert($borrower_database_values, $borrower_old_balance);
						}
					}
					else{
						$error = true;
					}
					
					if($error){
						transaction_revert($loaner_database_values, $loaner_old_balance);
					}
				}
			}
		}
	}
?>

<?php
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
	echo "<input type=\"submit\" value=\"Close Account\" name='close' class=\"btn\>";
    echo "</form>";
	
	echo '<br>';
	echo '<form action="?page=account" method="post">';
	echo '<table cellspacing = "15">';
	$latest_transaction = $db->query("fh_ledger", ["balance"], "userID = '" . $uid . "'", ["id" => "DESC"], 1);
	$credits = $latest_transaction? $latest_transaction[0]["balance"] : 0;
	echo '<tr>';
	echo '<td>Credits: ' . $credits . '</td>';
	echo '<td><input type = "number" name = "top_up_credits" placeholder = "Enter Credits To Top Up" min = "1" required></input></td>';
	echo '<td><input type = "submit" name = "top_up_submit" value = "Top Up" class = "btn"></input></td>';
	echo '</tr><table>';
	echo '</form>';

    echo "<h1>Items Borrowed</h1>";
	
	$items = $db->query("fh_items_loaned", "loanerID = '" . $uid . "'",  true, ["id" => "DESC"]);
	
	if($items){
		echo '<table cellspacing = "15"><tr>';
		foreach($items[0] as $key => $value){
			echo '<th>' . $key . '</th>';
		}
		echo '</tr>';
		foreach($items as $index => $item){
			echo '<tr>';

			foreach($item as $key => $value){
				echo '<td>' . $value . '</td>';
			}
			
			$adverts = $db->query("fh_adverts", ["credits"], "Id = '" . $item["itemID"] . "'");
			
			if(!$adverts or count($adverts) > 1){
				//if no advert is found, then that means it's been deleted from the database, without taking care of
				//loaned items, which is bad.
				echo "Something has gone terribly wrong with the database. Please notify an administrator.";
				echo "</tr>";
				break;
			}
			
			$advert = $adverts[0];
			//the exact formula still needs to be implemented. just putting it as credits per day for now
			//also should change it so it grabs the credits value from the moment it was loaned, not from the current advert
			$credits = $advert["credits"] * $item["amount_loaned"] * (time() - $item["timestamp"]) / 86400; //86400 is the amount of seconds in a day
			//putting it to integer since that's what the database uses
			$credits = (int) $credits;

			echo '<td>Current credits cost: ' . $credits .' (Note this value may change by the time the return process is completed)</td>';
			
			echo '<td><form action="?page=account" method="post">';
			echo '<button type = "submit" name = "return_item" value = "' . $item["id"] . '" class = "btn">Return Item</button>';
			echo '</td>';

			echo '</tr>';
		}

		echo '</table>';
	}
	else{
		echo "<p>NONE</p>";
	}

    echo "<h1>Items Loaning Out</h1>";
	
	$items = $db->query("fh_adverts", "userID = '" . $uid . "'",  true, ["id" => "DESC"]);
	if ($items){
    	echo "<table cellspacing = '15'><tr>";
        echo "<th>Item No</th>";
        echo "<th>Borrower</th>";
        echo "<th>Time and Date</th>";
        echo "<th>Item</th>";
        echo "<th>Description</th>";
        echo "<th>Amount Available</th>";
        echo "<th>Cost</th>";
        echo "<th>Active Advert</th>";
        echo "<th>Available</th>";
        echo "</tr>";
    	foreach ($items as $item){
	        echo "<tr>";
	        echo "<td><a href='index.php?page=advert&id=" . $item['id'] . "'>" . $item['id'] . "</a></td>";
	        echo "<td><a href='index.php?page=userprofile&id=" . $item['userID'] . "'>User</a></td>";
	        echo "<td>" . date('H:i:s d/M/Y', $item['timestamp']) . "</td>";
	        echo "<td>" . $item['name'] . "</td>";
	        echo "<td>" . substr($item['description'], 0, 20) . "... </td>";
	        echo "<td>" . $item['amount_available'] . "</td>";
	        echo "<td>" . $item['credits'] . "</td>";
	        echo "<td>" . (($item['active']==1)? "Yes": "No") . "</td>";
	        echo "<td>" . (($item['available']==1)? "Yes": "No") . "</td>";
	        echo "</tr>";
    	}
    	echo "</table>";
	}else{
	    echo "<h2>No items</h2>";
	}
    echo "<h1>My Credit Ledger</h1>";
	
	$transactions = $db->query("fh_ledger", "userID = '" . $uid . "'", true,  ["timestamp" => "DESC"]);
	
	if($transactions){
		echo "<table cellspacing = '15'><tr>";
		echo "<th>Transaction Type</th>";
		echo "<th>Time and Date</th>";
		echo "<th>Balance</th>";
		echo "<th>Credit Change</th>";
		echo "<th>Advert ID</th>";
		echo "<th>User</td>";
		echo "</tr>";
		foreach($transactions as $transaction){
    		echo "<tr>";
    		echo "<td>";
    		switch($transaction['TransactionType']){
    		    case 0:
    		        echo "Top Up";
    		        break;
    		    case 1:
    		        echo "Item Borrowed";
    		        break;
    		    case 2:
    		        echo "Item Loaned";
    		        break;
    		    case 3:
    		        echo "Admin Adjustment";
    		        break;
    		} 
    		
    		echo "</td>";
    		echo "<td>" . date('H:i:s d/M/Y',$transaction['timestamp']) . "</td>";
    		echo "<td>" . $transaction['balance'] . "</td>";
    		echo "<td>" . $transaction['changeCredit'] . "</td>";
    		echo "<td>" . (($transaction['advertID']==-1) ? "None" : $transaction['advertID']) . "</td>";
    		echo "<td>" . $transaction['userID'] . "</td>";
    		echo "</tr>";

		}
        echo "</table>";
	}else{
	    echo "<h2>No items</h2>";
	}
?>
