<?php
    permission_check("account");

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

    
    // Close an account (Adam M)
    if (isset($_POST['close'])){
        if (isset($_POST['closePassword'])){
            $user = $db->getRow("fh_users", "id='" . $uid . "'");
            $encryption->setPlainText($_POST['closePassword'] . $user["username"] . $user["email"]. $user["timestamp"]);
            $pwdEntered = $encryption->classRun();
            if ($pwdEntered == $user['password']){
        		$db->update("fh_users", ["active"=>0], "id=" . $uid);
        		setcookie($login_cookie, "", time()-3600);
        		setcookie($session_code, "", time()-3600);
        		echo "<h1>Your account has been closed down</h1>";
        		die();
            }else{
        		echo "<h1>Your password supplied was incorrect</h1>";
        		echo "<p>As a protective meassure, we have logged you out, if you still wish to close your account<br>";
        		echo "please log back in and enter the correct password associated with this account.</p>";
        		setcookie($login_cookie, "", time()-3600);
        		setcookie($session_code, "", time()-3600);
                die();                
            }
        }
        // check password
        // encrypt it
        // if match do this
		// else
		// massage close failed.
	}
	
	//Edit Account Info (Adam M)
    if (isset($_POST['submit'])){
    	foreach ($_POST as $key => $value){
    		if ($key != "submit"){
    		    switch ($key){
    		        case "dob":
    		            if ($value != NULL){
    		                $value = $db->cleanSQLInjection(trim(strtotime($value)));
    		            }else{
    		                $value = 0;
    		            }
		                $database_values[$key] = $db->cleanSQLInjection($value);
		                break;
		            case "password":
						if ($value != ""){
        				    $encryption->setPlainText($db->cleanSQLInjection($value) . $db->cleanSQLInjection($_POST["username"]) . $db->cleanSQLInjection($_POST["email"]) . time());
        				    $value = $encryption->classRun();
        				    $database_values[$key] = $value;
						}
		                break;
    		        default:
		                $database_values[$key] = $db->cleanSQLInjection($value);
    		    }
    		}
	    }
    	$saved = $db->update("fh_users", $database_values, "id='".$uid . "'");	
    	if ($saved){
    	    echo '<h1 class="announcement">Account Information Updated</h1>';
    	    $user = $db->getRow("fh_users", "id='" . $uid . "'");
    	}
    }
    

    // return items    
    if(isset($_POST['return_item'])){
        $transaction = $db->getRow("fh_items_loaned", "id='" . $db->cleanSQLInjection($_POST['return_item']) . "'");
        if ($transaction){
            if ($transaction['loanerID'] == $uid){
                // user has loaded this item and can return it (No else condition as we just ignore it if the user)
                // has messed with the source code in developer mode.
                $advert = $db->getRow("fh_adverts", "id='" . $transaction['itemID'] . "'");
                $returnedAmt = $advert['amount_available'] + $transaction['amount_loaned'];
                // calculate credits charge
                // calculate days
                $days = intval((time() - $transaction['timestamp']) / 86400) + 1;
                $charge = ($advert['credits'] * $transaction['amount_loaned']) * $days;
                if ($credits > $charge){
                    // Loaners Balance Changes
                    $credits -= $charge;
                    $newData['userID'] =$uid;
                    $newData['advertID']=$advert['id'];
                    $newData['transactionType']=1;
                    $newData['ip_address'] = $_SERVER['REMOTE_ADDR'];
                    $newData['timestamp'] = time();
                    $newData['balance'] = $credits;
                    $newData['changeCredit'] = $charge;
                    $newData['viewed'] = 0;
                    $db->insert("fh_ledger", $newData);
                    $returned = $db->update("fh_adverts", ['amount_available' => $returnedAmt, 'available'=>1], "id='" . $advert['id'] . "'" );
                    $returned = $db->delete("fh_items_loaned", "id=" . $transaction['id']);
                    
                    // now top up the loaners account
                    $utp = $db->query("fh_ledger", null, "userID='" . $advert['userID'] . "'", false, ['timestamp' => "DESC"], 1);
                    // credits
                    
                    $newData['userID'] =$advert['userID'];
                    $newData['advertID']=$advert['id'];
                    $newData['transactionType']=2;
                    $newData['ip_address'] = $_SERVER['REMOTE_ADDR'];
                    if ($utp){
                        $newData['balance'] = $utp[0]['balance'] += $charge;
                    }else{
                        $newData['balance'] = $charge;
                    }
                    $newData['changeCredit'] = $charge;
                    $newData['viewed'] = 0;
                    $db->insert("fh_ledger", $newData);
                    
                    if ($returned){
                        echo "<h3>Item recorded as returned</h3>";
                    }else{
                        echo "<h3>There has been an issue returning this item</h3>";
                    }
                
                }else{
                    echo "<h2>You have insufficient credits to return this item</h2>";
                }
            }
        }
	}
    
    // Topup credit (Adam M & Christian)
    if(isset($_POST['top_up_submit']) && isset($_POST['top_up_credits'])){
		if($_POST['top_up_credits'] > 0){
            include ($lib_dir . "luhn_checker.php");
            $cardCheck = $_POST['cardnumber'];
            if ($cardCheck == false){
                echo "<h1>Failed Luhn Card Verification</h1>";
                die();
            }

		    $latest_transaction = $db->query("fh_ledger", ["balance"], "userID = '" . $uid . "'", false, ["timestamp" => "DESC"], 1);
			$old_balance = $latest_transaction? $latest_transaction[0]["balance"] : 0;
			$new_balance = $old_balance + intval($_POST['top_up_credits']);
			$database_values = ["userID" => $uid, "advertID" => -1, "TransactionType" => 0, "ip_address" => $_SERVER['REMOTE_ADDR'],
			"timestamp" => time(), "viewed" => 0, "balance" => $new_balance, "changeCredit" => $db->cleanSQLInjection($_POST['top_up_credits'])];
			
			$result = transaction($database_values, $old_balance);
			
			if($result){
				echo '<h1 class="announcement">Transaction Successful!</h1>';
			}
		}
		else{
			echo '<h1 class="announcement">Error, cannot top up 0 credits.</h1>';
		}
	}
    
    echo "<h2>Account Information: </h2>";
    echo "<hr>";
    echo "<div id='container'>";
    
    echo "<div id='account_data' class='column contactContainer'>";
    echo "<form id='regform' name='registrationform' method='post' action='index.php?page=account'>";
    echo "<h3 class='subheading'>My Data</h3>";
    echo "<table>";
    echo "<tr><td>Name:</td><td><input type='text' name='name' value='" . $user['name'] . "'></td></tr>";
    echo "<tr><td>Surname:</td><td><input type='text' name='surname' value='" . $user['surname'] . "'></td></tr>";
    echo "<tr><td>Password:</td><td><input id='pwd' type='password' name='password'  onchange='verifyFields(\"pwd\", \"pwd2\", \"pwdMessage\");'></td></tr>";
    echo "<tr><td>Re-Enter Password:</td><td><input id='pwd2' type='password' onchange='verifyFields(\"pwd\", \"pwd2\", \"pwdMessage\");' ><span id='pwdMessage'></span></td></tr>";
    echo "<tr><td>Phone 1:</td><td><input type='text' name='phone1' value='" . $user['phone1'] . "'></td></tr>";
    echo "<tr><td>Phone 2:</td><td><input type='text' name='phone2' value='" . $user['phone2'] . "'></td></tr>";
    echo "<tr><td>House No:</td><td><input type='text' name='address_line1' value='" . $user['address_line1'] . "'></td></tr>";
    echo "<tr><td>Street:</td><td><input type='text' name='address_line2' value='" . $user['address_line2'] . "'></td></tr>";
    echo "<tr><td>City:</td><td><input type='text' name='address_line3' required value='" . $user['address_line3'] . "'></td></tr>";
    echo "<tr><td>Country:</td><td><input type='text' name='country' value='" . $user['country'] . "'></td></tr>";
    echo "<tr><td>Post Code:</td><td><input type='text' name='postcode' value='" . $user['postcode'] . "'></td></tr>";
    echo "<tr><td>Email:</td><td><input type='text' name='email' value='" . $user['email'] . "'></td></tr>";
    echo "<tr><td>DoB:</td><td><input type='date' id='dob' name='dob' value='" .  date('Y-m-d',$user['dob']) . "' onblur='ageVerification(this, \"dobSign\");'><span id='dobSign'></span></td></tr>";
    echo "</table>";
    echo "<input type='submit' id='updateAcc' value='Submit' name='submit' class='btn' >";
    echo "<input type='button' value='Close Account' onclick='verifyCloseAccount(true);' class='btn'>";
    echo "</form>";
    echo "</div>";
    
    echo "<div id='credit_data' class='column contactContainer'>";
    echo "<h3 class='subheading'>Credits</h3>";

    $latest_transaction = $db->query("fh_ledger", ["balance"], "userID = '" . $uid . "'", false, ["timestamp" => "DESC"], 1);
	$credits = $latest_transaction? $latest_transaction[0]["balance"] : 0;
	echo '<form action="?page=account" method="post">';
	echo '<table>';
	echo '<tr>';
	echo '<td>Credits: </td>';
	echo '<td><input type = "number" required name = "top_up_credits" placeholder = "Enter Credits To Top Up" min = "1"></td></tr><tr>';
	echo '<td>Debit/Credit Card:</td><td><input type = "number" id="cardnumber" name="cardnumber" placeholder = "Enter debit/credit card" onblur="luhn_check()"><span id="luhnMessage"></span></td></tr>';
	echo "<tr><td>Start Date:</td>";
	echo "<td><select id='sdm'>";
	for ($i = 1; $i <= 12; $i++){
	    echo "<option value=" . $i . ">" . $i . "</option>";
	}
	echo "</select> / <select id='sdy'>";
	for ($i = (date("Y") - 4); $i <= (date("Y")); $i++){
	    echo "<option value=" . $i . ">" . $i . "</option>";
	}
	echo "</select></td></tr>";
	
	echo "<tr><td>End Date:</td>";
	echo "<td><select id='edm'>";
	for ($i = 1; $i <= 12; $i++){
	    echo "<option value=" . $i . ">" . $i . "</option>";
	}
	echo "</select> / <select id='edy'>";
	for ($i = (date("Y") - 4); $i <= (date("Y") + 4); $i++){
	    echo "<option value=" . $i . ">" . $i . "</option>";
	}
	echo "</select></td></tr><tr>";
	echo '<td colspan=2><input type = "submit" name = "top_up_submit"  id = "topup" value = "Top Up" class = "btn" disabled=disabled></td>';


	echo '</tr>';
	echo '</table>';
	echo '</form>';
    echo "<br>";
    echo "<h3 class='subheading'>Credit Statement</h3>";
	$transactions = $db->query("fh_ledger", null, "userID = '" . $uid . "'", false,  ["timestamp" => "DESC"], 10);
	
	if($transactions){
		echo "<table><tr>";
		echo "<th>From/For</th>";
		echo "<th>Bal Change</th>";
		echo "<th>Balance</th>";
		echo "<th>Time and Date</th>";

		echo "</tr>";
		foreach($transactions as $transaction){
    		echo "<tr>";
    		$loaner = $db->getRow("fh_adverts", "id=" . $transaction['advertID']);
    		if ($loaner){
        		echo "<td>" . (($transaction['TransactionType']==0) ? "TopUp" : "<a href='index.php?page=item&view_item=" . $transaction['advertID'] . "'>" . $loaner['name'] . "</a>") . "</td>";
    		}else{
    		    echo "<td>" . (($transaction['TransactionType']==0) ? "TopUp" : "<a href='index.php?page=item&view_item=" . $transaction['advertID'] . "'>Unknown Advert</a>") . "</td>";
    		}
    		echo "<td>" . $transaction['changeCredit'] . "</td>";
    		echo "<td>" . $transaction['balance'] . "</td>";
    		echo "<td>" . date('H:i:s d/M/Y',$transaction['timestamp']) . "</td>";
    		
            echo "</tr>";

		}
        echo "</table>";
        echo "<p><a href='index.php?page=creditstatement'>more...</a>";
	}else{
	    echo "<h2>No items</h2>";
	}
    
    echo "</div>";
    
    echo "<div id='items_borrowed' class='column contactContainer'>";

    echo "<h3 class='subheading'>Items Advertised</h3>";
	$items = $db->query("fh_adverts", null, "userID = '" . $uid . "'",  true, ["id" => "DESC"]);
	if ($items){
    	echo "<table><tr>";
        echo "<th>Advert</th>";
        echo "<th>Quantity</th>";
        echo "<th>Credits</th>";
        echo "<th>Active</th>";
        echo "<th>Live</th>";
        echo "</tr>";
    	foreach ($items as $item){
	        echo "<tr>";
	        echo "<td><a href='index.php?page=item&view_item=" . $item['id'] . "'>" . $item['name']  . "</a></td>";
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

    echo "</div>";
    echo "<div id='items_loaned' class='column contactContainer'>";
    echo "<h3 class='subheading'>Items Borrowed</h3>";

    $items = $db->query("fh_items_loaned", null, "loanerID = '" . $uid . "'",  true, ["id" => "DESC"]);
    if ($items){
        echo "<table><tr>";
        echo "<th>Item</th>";
        echo "<th>Time/Date</th>";
        echo "<th>Quantity</th>";
        echo "<th>Cost</th>";
        echo "<th></th>";
        echo "</tr>";
        
        foreach ($items as $item){
            $adData = $db->query("fh_adverts", ["credits", "name"], "id = '" . $item["itemID"] . "'");
            echo "<tr>";
            echo "<td>";
            if (!$adData){
                echo "Item deleted";
            }else{
                echo "<a href='?page=item&view_item=" . $item['itemID'] . "'>" . $adData[0]['name'] . "</a>";
            }
            echo "</td>";
            echo "<td>";
            echo date("H:i:s d/m/Y", $item['timestamp']);
            echo "</td>";
            echo "<td>";
            echo $item['amount_loaned'];
            echo "</td>";
            echo "<td>";
            if (!$adData){
                echo "Item removed";
            }else{
                
    			
    			$credits = ($adData[0]["credits"] * $item["amount_loaned"]) * (((time() - $item["timestamp"]) / 86400));
        	    $credits = intval($credits);
        	    if ($credits == 0){$adData[0]["credits"] * $item["amount_loaned"];}
        	    echo $credits;
            }
            echo "</td>";
            echo "<td>";
            echo '<form action="?page=account" method="post">';
        	echo '<button type = "submit" name = "return_item" value = "' . $item["id"] . '" class = "btn">Return Item</button>';
        	echo '</form>';
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
        			

    }else{
        echo "<h2>You do not have any borrowed items</h2>";
    }
    echo "</div>";    
echo "</div>"; 
echo "<div id='closeAccScreen'>";
echo "<form id='closeForm' name='registrationform' method='post' action='index.php?page=account'>";
echo "<h1>Close Account Confirmation</h1>";
echo "<h3>You are about to close your account down</h3>";
echo "<p>To proceed enter your password and then select 'Close My Account'<br><br>";
echo "<input type='password' name='closePassword' placeholder='verify password'><br><br>";
echo "<input type='submit' class='btn' value='Close My Account' name='close'>";
echo "<input type='button' class='btn' value='Cancel' onclick='verifyCloseAccount(false)'>";
echo "</p>";
echo "</form>";
echo "</div>";

?>

<script src="<?php echo $js_dir . "luhn.js"; ?>"></script>

<script>
    const checks = {
    "pwdMessage": true,
    "dob": true
    }
    
    function luhn_check(){
        el = document.getElementById("luhnMessage");
        submitButton = document.getElementById("topup");
        cardNo = document.getElementById("cardnumber").value;
        
        val = validateCard(cardNo);
        if (val == false){
            el.innerHTML = "&#10060;";
            submitButton.setAttribute("disabled","disabled");
            el.style = "color: red;";
        }else{
            el.innerHTML = "&nbsp;&#10004;";
            submitButton.removeAttribute("disabled");
            el.style = "color: green;";
        }
    }

    function passfailDisplay(elID, passFail){
        submitButton = document.getElementById("updateAcc");
        el = document.getElementById(elID);
        if (passFail == true){
            el.innerHTML = "&nbsp;&#10004;";
            el.style = "color: green;";
            if ((Object.keys(checks).every(name => checks[name]))){
                submitButton.removeAttribute("disabled");
            }
        }else{
            el.innerHTML = "&#10060;";
            el.style = "color: red;";
            submitButton.setAttribute("disabled","disabled");
        }
    }
    function verifyFields(id1, id2, fieldSign){
       el1 = document.getElementById(id1); 
       el2 = document.getElementById(id2);
       if (el1.value==el2.value){
           match = true;
       }else{
           match = false;
       }
       checks[fieldSign] = match;
       passfailDisplay(fieldSign, match);
    }
    
    function ageVerification(el, fieldsSgn){
        dateNow = new Date();
        liveDate = Date.parse(dateNow);
        dobOfUser = Date.parse(el.value);
        if ((liveDate - dobOfUser) < 567648000000){
            age = false;
        }else{
            age = true;
        }
        checks['dob'] = age;
        passfailDisplay(fieldsSgn, age);
    }
	function verifyCloseAccount(show){
    	el = document.getElementById("closeAccScreen");
    	if (show){
    		el.style.height = window.innerHeight + "px";
    		el.style.width = window.innerWidth + "px";
    		el.style.display = "block";
    	}else{
    		el.style.display = "none";
    	}
	}

</script>
