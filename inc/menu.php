<div class="navbar">

	<div class="logo_div">

		<a href="index.php"><h1>FrackHub</h1></a>

	</div>

	<ul>

	  <?php 	

		if (isset($uid)){

			echo "<li><a class=\"active\" href=\"?page=account\">Account</a>";

			echo "<li><a class=\"active\" href=\"?page=advertise\">Advertise</a>";



		}else{

		    echo "<li><a class=\"active\" href=\"index.php\">Home</a>";

		}

		?>

	  </a></li>

	  <li><a href="#news">Search</a></li>

	  <li><a href="#news">Message</a></li>

	  <li><a href="#contact">Contact</a></li>

	  <li><a href="?page=logout">Logout</a></li>

	</ul>

</div>
