<?php

echo "<h1>Logout</h1>";

if (isset($_COOKIE["frachub"])){

	setcookie("frachub", "", time()-3600);

}

// developed by Adam MacKay 2000418 - 14/03/22


?>
