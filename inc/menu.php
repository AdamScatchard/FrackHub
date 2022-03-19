<div class="navbar">

	<div class="logo_div">

		<a href="index.php"><h1>FrackHub</h1></a>

	</div>
	
	<ul>

	<?php 	
		if (isset($uid)){
			echo '<li><a class="active" href="?page=account">Account</a></li>
			<li><a class="active" href="?page=advertise">Advertise</a></li>
			<li><a class="active" href="?page=book_item">Book Item</a></li>';
		}else{
			echo '<li><a class="active" href="index.php">Home</a></li>';
		}
	?>

	<li><a href="#news">Search</a></li>

	<li><a href="#news">Message</a></li>

	<li><a href="#contact">Contact</a></li>

	<?php
		if (isset($uid)){
			echo '<li><a href="?page=logout">Logout</a></li>';
		}
	?>

	</ul>

</div>
