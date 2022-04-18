<?php
	if (!isset($_COOKIE[$login_cookie])){
		header("location:?page=unauthorised_access");
		exit();
	}

	setcookie($login_cookie, "", time()-3600);
	setcookie($session_code, "", time()-3600);

	header("location:?page=home_page");
	exit();

// developed by Adam MacKay 2000418 - 14/03/22
?>
