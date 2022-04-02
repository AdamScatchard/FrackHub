<?php 
	// this page will include all inclusions that are necessary at the top of the page.
	// this file can be modified later by the dev team to add all relevant security checks
	// library / api PHP files https://frackhub.000webhostapp.com/adammac/
	include ("settings.php");
	include ($lib_dir . "db_class.php");
	include ($lib_dir . "encryption_class.php");

	$db = new db();
	$encryption = new encryption();
	$encryption->setKey($encryption_key);
	$connected = $db->connect();	
    function verify_access($db, $enc){
        if(isset($_COOKIE[$GLOBALS['login_cookie']])){
            if (isset($_COOKIE[$GLOBALS['session_code']])){
				$user_data = $db->getRow("fh_users", "id='" . $_COOKIE[$GLOBALS['login_cookie']] . "'");
                $salted_key = $user_data['id'] . $user_data['password'] . $user_data['lastlogin_timestamp'] . $user_data['timestamp'];
				$enc->setPlainText(trim($salted_key));       
                $session_Key = $enc->classRun();
                $sessionCode = $GLOBALS['session_code'];
                $cookie_code = $_COOKIE[$GLOBALS['session_code']];
                if ($session_Key == $cookie_code){
                    return true;
                }
            }
        }
        return false;
    }
	if ($connected){
		if (verify_access($db, $encryption)){
			if (isset($_COOKIE[$login_cookie])){
				$uid = $_COOKIE[$login_cookie];
				if ($uid > 0){
					// reset cookie length
					setcookie($login_cookie, $uid, $cookie_time);
					$user = $db->getRow("fh_users", "id = '" . $uid . "'");
					$userAccessLevel = $user['priviledge_id'];
					$accessCheck = $db->getRow("fh_priviledge_settings", "priviledge_id = '". $userAccessLevel . "'");
					if (isset($_GET['page']) && is_array($accessCheck)){
						if (array_key_exists($_GET['page'], $accessCheck)){
							// check priviledges to see if user can have this page shown or not
							// if not divert away to home page (defined in the settings)
							if ($_GET['page'] != "logout"){
								if ($accessCheck[$_GET['page']] == 1){
								   //Authorised
									$visiting_page = $_GET['page'] . ".php";
								}else{
								   // Unauthorised 
								   $visiting_page = "unauthorised_access.php";
								}
							}else{
								$visiting_page = $home_page;
							}
						}else{
							if ($_GET['page'] != "logout"){
								// page doesnt exist
								$visiting_page = "404.php";
							}else{
								$visiting_page = "logout.php";
							}
						}
					}else{
						$visiting_page = $home_page;
					}
				}else{
					if (isset($_GET['page'])){
						if($_GET['page'] != ""){
							$visiting_page =  $_GET['page'] . ".php";
						}else{
							$visiting_page = $home_page;
						}
					}else{
						$visiting_page = $home_page;
					}
				}
			}else{
				$user = NULL;
				if (isset($_GET['page'])){
					if($_GET['page'] != ""){
						$visiting_page =  $_GET['page'] . ".php";
					}else{
						$visiting_page = $home_page;
					}
				}else{
					$visiting_page = $home_page;
				}
			}
		}else{
			setcookie($login_cookie, "", time()-3600);
			setcookie($session_code, "", time()-3600);
			$visiting_page = $home_page;
		}
    }else{
        $visiting_page = $home;
    }
?>
