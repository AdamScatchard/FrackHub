<?php

echo "<h1>Logout</h1>";

if (isset($_COOKIE[$login_cookie])){

	setcookie($login_cookie, "", time()-3600);

}

// developed by Adam MacKay 2000418 - 14/03/22



?>
