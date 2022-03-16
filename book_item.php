<?php
	if (!isset($uid)){
		header("location:?page=404");
		exit();
	}
	
	echo '<h1>Booking page</h1>';

	if(!isset($_POST["submit"])){
		goto exit_php;
	}

	if(!isset($_POST["item_id"])){
		echo("Error, to book an item you need to input its id.");
		goto exit_php;
	}
	
	$result = $db->query("fh_adverts", ["active", "available"], "id = '" . $_POST["item_id"] . "'");

	if(!$result){
		echo("Error, the item you're trying to book doesn't exist.");
		goto exit_php;
	}
	
	if(count($result) > 1){
		echo("Something has gone terribly wrong with the database. Please notify an administrator.");
		goto exit_php;
	}
	
	if($result[0]["active"] == 0){
		echo("We're sorry. The item you're trying to book has not been checked by a moderator yet.\n
		Please wait until they do so.");
		goto exit_php;
	}
	
	if($result[0]["available"] == 0){
		echo("We're sorry. The item has already been booked.");
		goto exit_php;
	}
	
	$result = $db->update("fh_adverts", ["available" => 0, "timestamp" => time()], "id = '" . $_POST["item_id"] . "'");
	
	if(!$result){
		echo("There has been an error while trying to book the item. Try again or speak with an administrator.");
		goto exit_php;
	}
	
	echo("Item successfully booked!");
	
	exit_php:
?>

<form action="?page=book_item" method="post">
	<p class = "form_p">Item ID</p>
	<input type = "text" name="item_id" class="form_txtBox">
	
	<input type="submit" name="submit" value="book_item" placeholder = "Book Item">
</form>