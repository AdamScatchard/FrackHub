<?php
	echo "<h1>Registration page</h1>";

	echo "<br>";

	if (isset($_POST['register'])){

		// create database values to store in the database
		$database_values['timestamp'] = time();				// timestamp of registration
		$database_values['ip_address'] = $_SERVER['REMOTE_ADDR'];	// IP address 


		foreach ($_POST as $key => $value){

			if ($key != "register"){
				if ($key == "dob"){
				    echo "Value is: " . $value;
				    if (is_null($value) || trim($value) == ""){
				        $value = 0;
				    }else{
    					$value = strtotime($value);
				    }
				}
				if ($key == "password"){
				    $encryption->setPlainText($value . $_POST["username"] . $_POST["email"] . $database_values['timestamp']);
				    $value = $encryption->classRun();
				}

				$database_values[$key] = $value;	

			}

		}


		

		$saved = $db->insert("fh_users", $database_values);	

		if ($saved){

			//forward to relevant confirmation page

		}else{

			echo "Unable to register, try again or speak with administration";

			// developers to repopulate the values into the HTML textbox values

		}

	}
?>

<?php
	echo '<form method="post" action="?page=register" name="submit_form" id="register_form" class="form">

		<p class="form_p">Username:</p>

		<input type="text" id="username" name="username"' . (isset($_POST["username"])? ' value = "' . $_POST["username"] . '"' : "") . ' placeholder="Enter Username" class="form_txtBox">

		<p class="form_p">Name:</p>

		<input type="text" id="name" name="name"' . (isset($_POST["name"])? ' value = "' . $_POST["name"] . '"' : "") . ' placeholder="Enter Name" class="form_txtBox">

		<p class="form_p">Surname:</p>

		<input type="text" id="surname" name="surname"' . (isset($_POST["surname"])? ' value = "' . $_POST["surname"] . '"' : "") . ' placeholder="Enter Surname" class="form_txtBox">

		<p class="form_p">Email:</p>

		<input type="email" id="email" name="email"' . (isset($_POST["email"])? ' value = "' . $_POST["email"] . '"' : "") . ' placeholder="Enter Email" class="form_txtBox">

		<p class="form_p">Date of Birth:</p>

		<input type="date" id="dob" name="dob"' . (isset($_POST["dob"])? ' value = "' . $_POST["dob"] . '"' : "") . ' placeholder="Enter Date of Birth" class="form_txtBox">

		<p class="form_p">Address Line 1:</p>

		<input type="text" id="FLineAdd" name="address_line1"' . (isset($_POST["address_line1"])? ' value = "' . $_POST["address_line1"] . '"' : "") . ' placeholder="First Line of Address" class="form_txtBox">

		<p class="form_p">Address Line 2:</p>

		<input type="text" id="SLineAdd" name="address_line2"' . (isset($_POST["address_line2"])? ' value = "' . $_POST["address_line2"] . '"' : "") . ' placeholder="Second line of address" class="form_txtBox">

		<p class="form_p">Address Line 3:</p>

		<input type="text" id="TLineAddr" name="address_line3"' . (isset($_POST["address_line3"])? ' value = "' . $_POST["address_line3"] . '"' : "") . ' placeholder="Third line of address" class="form_txtBox">

		<p class="form_p">Country:</p>

		<input type="text" id="country" name="country"' . (isset($_POST["country"])? ' value = "' . $_POST["country"] . '"' : "") . ' placeholder="Enter Country" class="form_txtBox">

		<p class="form_p">Post Code:</p>

		<input type="text" id="pcode" name="postcode"' . (isset($_POST["postcode"])? ' value = "' . $_POST["postcode"] . '"' : "") . ' placeholder="Enter Post Code" class="form_txtBox" max=9>

		<p class="form_p">Phone:</p>

		<input type="text" id="landline" name="phone1"' . (isset($_POST["phone1"])? ' value = "' . $_POST["phone1"] . '"' : "") . ' placeholder="Landline" class="form_txtBox">

		<p class="form_p">Phone:</p>

		<input type="text" id="mobile" name="phone2"' . (isset($_POST["phone2"])? ' value = "' . $_POST["phone2"] . '"' : "") . ' placeholder="Mobile" class="form_txtBox">	

        <p class="form_p"Debit/Credit Card (Age Verification):</p>
        <input type="verify" id="cardnumber" name="cardNumber" class="form_txtBox">
		<p class="form_p">Password:</p>
		<input type="password" id="username" name="password"' . (isset($_POST["password"])? ' value = "' . $_POST["password"] . '"' : "") . ' class="form_txtBox">

		<p class="form_p">Re-Type Password:</p>' .

		//<input type="password" id="username" name="retypedpassword"  class="form_txtBox">

		'<br>
		<p class="form_p">I agree to <a href="#">Terms and Conditions</a></p>
		<input type="checkbox" id="termsConditions" required>

		<input type="submit" name="register" class="form_button" id="reg_button" value="Click here to register">

		<input type="reset" name="clear" class="form_button" id="clear_button" value="Restart your application form">

	</form>'
?>
