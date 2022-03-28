<?php
	if (!isset($uid)){
		header("location:?page=unauthorised_access");
		exit();
	}
	
	echo '<h1>Booking page</h1>';

	if(!isset($_POST["submit"])){
		goto exit_php;
	}
	
	//As far as i heard these checks need to be on both client and server side
	//for maximum efficiency x security.

	if(!isset($_POST["id"]) || !isset($_POST["amount_loaned"])){
		echo("Error, to book an item you need to fully complete the form.");
		goto exit_php;
	}
	
	if($_POST["amount_loaned"] <= 0 || !ctype_digit($_POST["amount_loaned"])){
		echo("Error, to book an item the amount needs to be an integer higher than 0.");
		goto exit_php;
	}
	
	//taking all collumns since the variable is going to be used to revert any changes on errors
	$original_adverts = $db->query("fh_adverts", NULL, "id = '" . $_POST["id"] . "'", true);

	if(!$original_adverts){
		echo("Error, the item you're trying to book doesn't exist.");
		goto exit_php;
	}
	
	if(count($original_adverts) > 1){
		echo("Something has gone terribly wrong with the database. Please notify an administrator. Error code 1.");
		goto exit_php;
	}
	
	$original_advert = $original_adverts[0];
	
	if($original_advert["active"] == 0){
		echo("We're sorry. The item you're trying to book has not been checked by a moderator yet.\n
		Please wait until they do so.");
		goto exit_php;
	}
	
	if($original_advert["available"] == 0){
		echo("We're sorry. The item is not available to be booked yet.");
		goto exit_php;
	}
	
	//these errors shouldn't really appear, since the available field above should already take care of it,
	//but not taking any chances
	if($original_advert["amount_available"] <= 0){
		if($original_advert["amount_available"] == 0){
			echo("We're sorry. The item is not available to be booked yet.");
		}
		else{
			echo("Something has gone terribly wrong with the database. Please notify an administrator. Error code 2.");
		}
		
		//updating the available field since it appeared available based on the previous check
		$result = $db->update("fh_adverts", ["available" => 0], "id = '" . $_POST["id"] . "'");

		if(!$result){
			echo("There has been an error while trying to update the database. Please notify an administrator.");
			goto exit_php;
		}

		goto exit_php;
	}
	
	$updated_advert = $original_advert;

	//amount is definitely greater than 0 at this point
	$updated_advert["amount_available"] = $updated_advert["amount_available"] - $_POST["amount_loaned"];
	
	if($updated_advert["amount_available"] < 0){
		echo("Error the item only has an amount of '" . $original_advert["amount_available"] . "' available.\nPlease input a lower number.");
		goto exit_php;
	}
	elseif($updated_advert["amount_available"] == 0){
		$updated_advert["available"] = 0;
	}

	$result = $db->update("fh_adverts", $updated_advert, "id = '" . $_POST["id"] . "'");
	
	if(!$result){
		echo("There has been an error while trying to book the item. Try again or speak with an administrator.");
		goto exit_php;
	}
	
	$result = $db->insert("fh_items_loaned", ["itemID" => $_POST["id"], "loanerID" => $uid, "amount_loaned" => $_POST["amount_loaned"], "timestamp" => time()]);
	
	if(!$result){
		echo("There has been an error while trying to book the item. Try again or speak with an administrator.");
		
		//the adverts have already been updated, and an error here means that the two tables are now out of sync
		//so this is attempting to revert the previous update.
		
		$result = $db->update("fh_adverts", $original_advert, "id = '" . $_POST["id"] . "'");
	
		//if this fails then the databases remain out of sync, which is bad
		if(!$result){
			echo("Something has gone terribly wrong with the database. Please notify an administrator. Error code 3.");
			goto exit_php;
		}
		
		//otherwise the databases are kept in sync
		goto exit_php;
	}

	echo("Item successfully booked!");
	
	exit_php:
?>

<?php	
	echo '<form action="?page=book_item" method="post">
	<p class = "form_p">Item ID</p>
	<input type = "text" name="id" id = "id"' . (isset($_POST["id"])? ' value = "' . $_POST["id"] . '"' : "") . ' placeholder = "Enter Item ID" required class="form_txtBox">
	<p class = "form_p">Items Amount</p>
	<input type = "number" name="amount_loaned" id = "amount_loaned"' . (isset($_POST["amount_loaned"])? ' value = "' . $_POST["amount_loaned"] . '"' : "") . ' placeholder = "Enter Amount To Loan" required min = "1" step = "1" class="form_txtBox">
	<input type="submit" name="submit" value="Book Item" class = "form_button">
	</form>'
?>
