<?php

// developed by Adam MacKay 2000418 - 16/03/22



echo "<h1>Advertise Item</h1>";

echo "<br>";

if (isset($_POST['register'])){

	// create database values to store in the database

	foreach ($_POST as $key => $value){

		if ($key != "register"){

			$database_values[$key] = $value;	

		}

	}

	$database_values['userID'] = $uid;
	
	//moderators are supposed to turn the active to true, but for testing purposes it's already turned here instead
	$database_values['active'] = 1;

	$database_values['available'] = 1;

	$database_values['timestamp'] = time();				// timestamp of registration

	

	$saved = $db->insert("fh_adverts", $database_values);	

	if ($saved){

		//forward to relevant confirmation page

		echo ("Saved");

	}else{

		echo ("Unable to register, try again or speak with administration");

		// developers to repopulate the values into the HTML textbox values

	}

}

?>

<form method="post" action="?page=advertise" name="submit_form" id="register_form" class="form">

	<p class="form_p">Title:</p>

	<input type="text" id="name" name="name" placeholder="Title" class="form_txtBox">

	<p class="form_p">Description:</p>

	<textarea id="name" name="description" placeholder="Advert" class="form_txtBox"></textarea>

	<p class="form_p">Items Available:</p>

	<input type = "number" name="amount_available" id = "no_of_items" placeholder = "Number of items" required min = "1" class="form_txtBox">
	
	<p class="form_p">Cost per day:</p>

	<input type = "number" name="credits" id = "no_of_items" placeholder = "Number of credits" required min = "1" class="form_txtBox">

	<input type="submit" name="register" class="form_button" id="reg_button" value="Create Advert">

	<input type="reset" name="clear" class="form_button" id="clear_button" value="Clear">

</form>
