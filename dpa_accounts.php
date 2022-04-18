<?php
    // this script is to delete all accounts that have aged 6 months (15811200 seconds)
    // since their last login with an active status of 0 (meaning account closed down)
    // the purpose is to comply with the security executives recomendations around data protection act 2018 legislations
    
    include ("settings.php");
    include ($lib_dir . "db_class.php");
    $db = new db();
    $db->delete("fh_users", "lastlogin_timestamp = " . time() - $expire_accounts . " AND active = 0");
    
    // create cronjob to run everynight to automatically execute the script
    // script by Adam Mackay 2000418
?>
