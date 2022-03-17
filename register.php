<?php
// developed by Adam MacKay 2000418 - 14/03/22

echo "<h1>Registration page</h1>";
echo "<br>";
if (isset($_POST['register'])){
	// create database values to store in the database
	foreach ($_POST as $key => $value){
		if ($_POST['register'] != $key){
		    if ($key == "dob"){
		        $value = strtotime($value);
		    }
			$database_values[$key] = $value;	
		}
	}
	$database_values['timestamp'] = time();				// timestamp of registration
	$database_values['ip_address'] = $_SERVER['REMOTE_ADDR'];	// IP address 
	
	$saved = $db->insert("fh_users", $database_values);	
	if ($saved){
		//forward to relevant confirmation page
	}else{
		echo "Unable to register, try again or speak with administration";
		// developers to repopulate the values into the HTML textbox values
	}
}
?>
<form method="post" action="?page=register" name="submit_form" id="register_form" class="form">
	<p class="form_p">Username:</p>
	<input type="text" id="username" name="username" placeholder="Enter Username" class="form_txtBox">
	<p class="form_p">Name:</p>
	<input type="text" id="name" name="name" placeholder="Enter Name" class="form_txtBox">
	<p class="form_p">Surname:</p>
	<input type="text" id="surname" name="surname" placeholder="Enter Surname" class="form_txtBox">
	<p class="form_p">Email:</p>
	<input type="email" id="email" name="email" placeholder="Enter Email" class="form_txtBox">
	<p class="form_p">Date of Birth:</p>
	<input type="date" id="dob" name="dob" placeholder="Enter Date of Birth" class="form_txtBox">
	<p class="form_p">Address Line 1:</p>
	<input type="text" id="username" name="address_line1" placeholder="Enter Username" class="form_txtBox">
	<p class="form_p">Address Line 2:</p>
	<input type="text" id="username" name="address_line2" placeholder="Enter Username" class="form_txtBox">
	<p class="form_p">Address Line 3:</p>
	<input type="text" id="username" name="address_line3" placeholder="Enter Username" class="form_txtBox">
	<p class="form_p">Country:</p>
	<input type="text" id="username" name="country" placeholder="Enter Username" class="form_txtBox">
	<p class="form_p">Post Code:</p>
	<input type="text" id="username" name="postcode" placeholder="Enter Username" class="form_txtBox" max=9>
	<p class="form_p">Phone:</p>
	<input type="text" id="username" name="phone1" placeholder="Enter Username" class="form_txtBox">
	<p class="form_p">Phone:</p>
	<input type="text" id="username" name="phone2" placeholder="Enter Username" class="form_txtBox">	
	<p class="form_p">Password:</p>
	<input type="password" id="username" name="password"  class="form_txtBox">
	<p class="form_p">Re-Type Password:</p>
<!--	<input type="password" id="username" name="retypedpassword"  class="form_txtBox"> -->
	<br>
	<input type="submit" name="register" class="form_button" id="reg_button" value="register" text="Click here to register">
	<input type="reset" name="clear" class="form_button" id="clear_button" value="clear" text="restart your application form">
</form>