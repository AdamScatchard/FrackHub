<?php 
	// Adam Mackay's work
	include ("page_init.php");
	//Kieran C's Work
	require_once($inc_dir . 'head_section.php');
	
	include ($inc_dir . 'menu.php');
    $banned = $db->getRow("fh_banned_connections", "user_ip ='" . $_SERVER['REMOTE_ADDR'] . "'");
    if ($banned){
        echo "<h2>Banned Connection</h2>";
        echo "<hr>";
        echo "<p>Your connection " . $_SERVER['REMOTE_ADDR'] . " is banned from our services</p>";
    }else{
    	include ($visiting_page);
    }
	include($inc_dir . 'footer.php');
	$db->disconnect();
	
?>
