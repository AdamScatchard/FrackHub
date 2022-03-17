<?php 
	// this page will include all inclusions that are necessary at the top of the page.
	// this file can be modified later by the dev team to add all relevant security checks
	// library / api PHP files https://frackhub.000webhostapp.com/adammac/
	include ("settings.php");
	include ($lib_dir . "db_class.php");
	$db = new db();
	$connected = $db->connect();	
	if ($connected){
		// check for existing login:
		if (isset($_COOKIE['frachub'])){
			$uid = $_COOKIE['frachub'];
			if ($uid > 0){
			// reset cookie length
				setcookie("frachub", $uid, $cookie_time);
				$user = $db->query("fh_users", "id=" . $uid, false, NULL);
			}
		}else{
			$user = NULL;
		}
		// check priviledges to see if user can have this page shown or not
		// if not divert away to home page (defined in the settings)
		if (isset($_GET['page'])){
			$visiting_page = $_GET['page'] . ".php";
		}else{
			$visiting_page = $home_page;
		}
	}else{
		$visiting_page = $inc_dir . "no_db.php";	
	}

?>