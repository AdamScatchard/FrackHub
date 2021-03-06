<?php
    permission_check("bookings");
	
	echo '<h2>Booking page</h2>';
    echo "<hr>";
  	if(!isset($_POST["submit"])){
		goto exit_php;
	}
	
	//As far as i heard these checks need to be on both client and server side
	//for maximum efficiency x security.

	if(!isset($_POST["id"]) || !isset($_POST["amount_loaned"])){
		$msg = "Error, to book an item you need to fully complete the form.";
		goto exit_php;
	}
	
	if($_POST["amount_loaned"] <= 0 || !ctype_digit($_POST["amount_loaned"])){
		$msg = "Error, to book an item the amount needs to be an integer higher than 0.";
		goto exit_php;
	}
	
	//taking all collumns since the variable is going to be used to revert any changes on errors
	$original_adverts = $db->query("fh_adverts", NULL, "id = '" . $db->cleanSQLInjection($_POST["id"]) . "'", true);

	if(!$original_adverts){
		$msg = "Error, the item you're trying to book doesn't exist.";
		goto exit_php;
	}
	
	if(count($original_adverts) > 1){
		$msg = "Something has gone terribly wrong with the database. Please notify an administrator. Error code 1.";
		goto exit_php;
	}
	
	$original_advert = $original_adverts[0];
	
	if($original_advert["active"] == 0){
		$msg = "We're sorry. The item you're trying to book has not been checked by a moderator yet.\n
		Please wait until they do so.";
		goto exit_php;
	}
	
	if($original_advert["available"] == 0){
		$msg = "We're sorry. The item is not available to be booked yet.";
		goto exit_php;
	}
	
	//these errors shouldn't really appear, since the available field above should already take care of it,
	//but not taking any chances
	if($original_advert["amount_available"] <= 0){
		if($original_advert["amount_available"] == 0){
			echo("We're sorry. The item is not available to be booked yet.");
		}
		else{
			$msg = "Something has gone terribly wrong with the database. Please notify an administrator. Error code 2.";
		}
		
		//updating the available field since it appeared available based on the previous check
		$result = $db->update("fh_adverts", ["available" => 0], "id = '" . $db->cleanSQLInjection($_POST["id"]) . "'");

		if(!$result){
			echo("There has been an error while trying to update the database. Please notify an administrator. Error code 3");
			goto exit_php;
		}

		goto exit_php;
	}
	
	$updated_advert = $original_advert;

	//amount is definitely greater than 0 at this point
	$updated_advert["amount_available"] = $updated_advert["amount_available"] - $db->cleanSQLInjection($_POST["amount_loaned"]);
	
	if($updated_advert["amount_available"] < 0){
		$msg = "Error the item only has an amount of '" . $original_advert["amount_available"] . "' available.\nPlease input a lower number.";
		goto exit_php;
	}
	elseif($updated_advert["amount_available"] == 0){
		$updated_advert["available"] = 0;
	}

	$result = $db->update("fh_adverts", $updated_advert, "id = '" . $db->cleanSQLInjection($_POST["id"]) . "'");
	
	if(!$result){
		$msg = "There has been an error while trying to book the item. Try again or speak with an administrator.";
		goto exit_php;
	}
	
	$result = $db->insert("fh_items_loaned", ["itemID" => $db->cleanSQLInjection($_POST["id"]), "loanerID" => $uid, "amount_loaned" => $db->cleanSQLInjection($_POST["amount_loaned"]), "timestamp" => time()]);
	
	if(!$result){
		$msg = "There has been an error while trying to book the item. Try again or speak with an administrator.";
		
		//the adverts have already been updated, and an error here means that the two tables are now out of sync
		//so this is attempting to revert the previous update.
		
		$result = $db->update("fh_adverts", $original_advert, "id = '" . $db->cleanSQLInjection($_POST["id"]) . "'");
	
		//if this fails then the databases remain out of sync, which is bad
		if(!$result){
			$msg = "Something has gone terribly wrong with the database. Please notify an administrator.";
			goto exit_php;
		}
		
		//otherwise the databases are kept in sync
		goto exit_php;
	}

	$msg = "Item successfully booked!";
	
	exit_php:
?>

<?php	
    echo "<div class='contactContainer'>";
    echo $msg;
	echo "</div>";
?>
