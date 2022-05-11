<?php
	echo "<h2>Registration page</h2>";
	echo "<hr>";

    if (isset($_GET['linkverify'])){
        $verificationCode = $db->cleanSQLInjection($_GET['linkverify']);
        $user_data = $db->getRow("fh_users", "verification = '" .  $verificationCode . "' AND active = 0");
        if ($user_data){
            $done = $db->update("fh_users", ["active"=>1], "id=" . $user_data['id']);
            if ($done){
                echo "<h1>Your account is now active</h1>";
                echo "Please go to the home page to sign in";
                die;
            }else{
                echo "<h1>Error:</h1>";
                echo "unable to specify what happened, but the resgistration failed";
                die;
            }
        }
    }

	if (isset($_POST['register'])){

		// create database values to store in the database
		$database_values['timestamp'] = time();				// timestamp of registration
		$database_values['ip_address'] = $_SERVER['REMOTE_ADDR'];	// IP address 
        $encryption->setPlainText($db->cleanSQLInjection($_POST["username"]) . $db->cleanSQLInjection($_POST['email']) . $db->cleanSQLInjection($_POST['password']) . time() . strtotime($db->cleanSQLInjection($_POST['dob'])));
        $database_values['verification'] = $encryption->classRun();
		foreach ($_POST as $key => $value){
			if ($key != "register"){
			    switch ($key){
			        case "dob":
    				    if (is_null($value) || trim($value) == ""){
    				        $value = 0;
    				    }else{
        					$value = strtotime($value);
    				    }
        				$database_values[$key] = $db->cleanSQLInjection($value);	
			            break;
			        case "password":
                        $encryption->setPlainText($value . $db->cleanSQLInjection($_POST["username"]) . $db->cleanSQLInjection($_POST["email"]) . $database_values['timestamp']);
                        $value = $encryption->classRun();
        				$database_values[$key] = $db->cleanSQLInjection($value);	
			            break;
			        case "cardnumber":
			            include ($lib_dir . "luhn_checker.php");
                        $cardCheck = checkLuhnCardNumber($db->cleanSQLInjection($_POST['cardnumber']));
                        if ($cardCheck == false){
                            echo "<h1>Failed Luhn Card Verification</h1>";
                            die();
                        }
                        break;
                    default:
    				    $database_values[$key] = $db->cleanSQLInjection($value);	
			    }

			}
		}


		$userExist = $db->getRow("fh_users", "username='" . $database_values['username'] . "'");
		if ($userExist){
		    echo "<h1>Account Exists Already</h1>";
		}else{
    		$saved = $db->insert("fh_users", $database_values);	
              
    		if ($saved){
                    $user_data = $db->getRow("fh_users", "username='" . $database_values['username'] . "'");
                    include ($lib_dir . "email_class.php");
                    $e_mail = new emailer();
                    if ($user_data){
                        $e_mail->send_email("registration", $user_data );
                    }else{
                        echo "<h1>Error</h1>";
                    }
                    echo "<h1>Please check your email address " . $database_values['email'] . "</h1>";
                    echo "<p>You will need to activate your account before you can begin to use Frackhub</p>";
                    die;
    		}else{
    
    			echo "Unable to register, try again or speak with administration";
    
    			// developers to repopulate the values into the HTML textbox values
    
    		}
		}
	}
?>
<script src="<?php echo $js_dir . "luhn.js"; ?>"></script>
<?php
// comment out (table data)
    echo "<div class='contactContainer'>";
	echo '<form method="post" action="?page=register" name="submit_form" id="register_form" class="form">';
    echo "<br>";
//    echo "<table><tr><td colspan=2>";

    echo '<input type="text" id="username" name="username"' . (isset($_POST["username"])? ' value = "' . $_POST["username"] . '"' : "") . ' placeholder="Enter Username" required class="form_txtBox">';
    echo "<br>";

//    echo "</td></tr><tr><td>";
    echo '<input type="text" id="name" name="name"' . (isset($_POST["name"])? ' value = "' . $_POST["name"] . '"' : "") . ' placeholder="Enter Name" class="form_txtBox">';
//    echo "</td><td>";

    echo '<input type="text" id="surname" name="surname"' . (isset($_POST["surname"])? ' value = "' . $_POST["surname"] . '"' : "") . ' placeholder="Enter Surname" class="form_txtBox">';
    echo "<br>";
//    echo "</td></tr><tr><td>";
    echo "Passwords must contain at least ";
    if (isset($min_password)){
    	echo  $min_password . " characters in length ";
    }
	if (isset($number_password)){
	    echo "1 number ";
	}
	if (isset($lowercase_char_password) || isset($uppercase_char_password)){
	    echo " a case difference and a special character";
	}
    echo "<br>";
	echo '<input type="password" required id="pwd" onchange="verifyFields(\'pwd\', \'pwd2\', \'pwdMessage\');" placeholder="Password" name="password"' . (isset($_POST["password"])? ' value = "' . $_POST["password"] . '"' : "") . ' class="form_txtBox" ';
    	if (isset($min_password)){
    	    echo " minlength='" . $min_password . "'";
    	}
    $titleTxt = "";
	echo ' pattern="';
    	if (isset($number_password)){
    	    echo "(?=.*\d)";
    	    $titleTxt .= " a number,";
    	}
    	if (isset($lowercase_char_password)){
    	    echo "(?=.*[a-z])";
    	    $titleTxt .= " a upper case letter,";
    	}
    	if (isset($uppercase_char_password)){
    	    echo "(?=.*[A-Z])";
    	    $titleTxt .= " a lower case letter,";
    	}
    	if (isset($min_password)){
    	    $titleTxt .= " and be at least " . $min_password . " characters long or more";
        	echo ".{" . $min_password . ",}";
    	}
	echo '" title="Must contain at least ' . $titleTxt . '">';
	
//	echo "</td><td>";
	echo '<input type="password" required id="pwd2" onchange="verifyFields(\'pwd\', \'pwd2\', \'pwdMessage\');" placeholder="Retype Password" class="form_txtBox FloatRight"';
	if (isset($min_password)){
	    echo " minlength='" . $min_password . "'";
	}
	echo ">";

	echo '<span id="pwdMessage"></span>';
    echo "<br>";

//	echo "</td></tr><tr><td>";

	echo '<input type="text" id="landline" name="phone1"' . (isset($_POST["phone1"])? ' value = "' . $_POST["phone1"] . '"' : "") . ' placeholder="Landline" class="form_txtBox">';

//	echo "</td><td>";

	echo '<input type="text" id="mobile" name="phone2"' . (isset($_POST["phone2"])? ' value = "' . $_POST["phone2"] . '"' : "") . ' placeholder="Mobile" class="form_txtBox">';
    echo "<br>";
//    echo "</td></tr><tr><td>";

    echo '<input type="email" required id="email" onchange="verifyFields(\'email\', \'email2\', \'emailMessage\');" name="email"' . (isset($_POST["email"])? ' value = "' . $_POST["email"] . '"' : "") . ' placeholder="Enter Email" class="form_txtBox">';
//    echo "</td><td>";

    echo '<input type="email" required id="email2" onchange="verifyFields(\'email\', \'email2\', \'emailMessage\');" placeholder="Verify Email" class="form_txtBox">';
	echo '<span id="emailMessage"></span>';
    echo "<br>";
//    echo "</td></tr><tr><td colspan=2>";

    echo '<input type="text" id="FLineAdd" name="address_line1"' . (isset($_POST["address_line1"])? ' value = "' . $_POST["address_line1"] . '"' : "") . ' placeholder="House Number" class="form_txtBox">';
    echo "<br>";
//    echo "</td></tr><tr><td colspan=2>";

    echo '<input type="text" id="SLineAdd" name="address_line2"' . (isset($_POST["address_line2"])? ' value = "' . $_POST["address_line2"] . '"' : "") . ' placeholder="Street" class="form_txtBox">';
    echo "<br>";
//    echo "</td><tr><tr><td colspan=2>";

    echo '<input type="text" id="TLineAddr" name="address_line3"' . (isset($_POST["address_line3"])? ' value = "' . $_POST["address_line3"] . '"' : "") . ' placeholder="Town or City" required class="form_txtBox">';
    echo "<br>";
//    echo "</td></tr><tr><td colspan=2>";

	echo '<input type="text" id="country" name="country"' . (isset($_POST["country"])? ' value = "' . $_POST["country"] . '"' : "") . ' placeholder="Country" class="form_txtBox">';
    echo "<br>";
//	echo "</td></tr><tr><td colspan=2>";

	echo '<input type="text" id="pcode" name="postcode"' . (isset($_POST["postcode"])? ' value = "' . $_POST["postcode"] . '"' : "") . ' placeholder="Enter Post Code" class="form_txtBox" maxlength=9>';
    echo "<br>";
//	echo "</td></tr><tr><td>";

	echo '<input type="number" id="cardnumber" name="cardnumber" placeholder="credit card" class="form_txtBox" onblur="luhn_check()">';
	echo '<span id="luhnMessage"></span>';
//	echo "</td><td>";

    echo '<input type="date" id="dob" name="dob"' . (isset($_POST["dob"])? ' value="' . $_POST["dob"] . '"' : '') . '  class="form_txtBox FloatRight" onblur="ageVerification(this, \'dobSign\');">';
    echo '<span id="dobSign"></span>';
    echo "<br>";
//	echo "</td></tr>";
//	echo "</table>";

	echo '<p class="form_p">I agree to <a href="tandc.php" target="_blank">Terms and Conditions</a> <input type="checkbox" id="termsConditions" required></p>';
    echo '
		<input type="submit" name="register" class="form_button btn" id="reg_button" disabled value="Click here to register">

		<input type="reset" name="clear" class="form_button btn" id="clear_button" value="Restart your application form">

	</form>';
	echo "</div>";
?>
<script>
    const checks = {
    "luhnMessage": false,
    "emailMessage": false,
    "pwdMessage": false,
    "dob": false
    }
    function luhn_check(){
        cardNo = document.getElementById("cardnumber").value;
        val = validateCard(cardNo);
        if (val == false){
            cc = false;
        }else{
            cc = true;
        }
        checks['luhnMessage'] = cc;
        passfailDisplay("luhnMessage", cc);
    }
    function passfailDisplay(elID, passFail){
        submitButton = document.getElementById("reg_button");
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
</script>
