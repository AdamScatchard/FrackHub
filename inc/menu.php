<?php
if (isset($uid)){
    $latest_transaction = $db->query("fh_ledger", ["balance"], "userID = '" . $uid . "'", false, ["timestamp" => "DESC"], 1);
	$credits = $latest_transaction? $latest_transaction[0]["balance"] : 0;
    echo '<div class="uidBar">';
    echo "<img alt='user' class='icon' src=" . $img_dir . "user.png>";
    echo "Username: " . $user['username'];
    echo " | ";
    echo "<img alt='credits' class='icon' src=" . $img_dir . "credit.png>";
    echo " Credits: " . $credits;
    echo "</div>";
}
?>
<div class="navbar">
	<div class="logo_div">
		<a href="index.php"><h1>FrackHub</h1></a>
	</div>		<ul>
	<?php 	
		if (isset($uid)){			
		    echo "<li><a class=\"active\" href=\"?page=account\">Account</a></li>";
		    echo "<li><a class=\"active\" href=\"?page=advertise\">Advertise</a></li>";
		    echo "<li><a class=\"active\" href=\"?page=book_item\">Book Item</a></li>";
		}else{			
		    echo "<li><a class=\"active\" href=\"index.php\">Home</a></li>";
		}
	?>
	<li><a href="index.php?page=search">Search</a></li>
	<li><a href="#news">Message</a></li>
	<li><a href="#contact">Contact</a></li>	
	<?php		
	    if (isset($uid)){
	        echo '<li><a href="?page=logout">Logout</a></li>';		
	    }	
	?>
	</ul>
</div>
