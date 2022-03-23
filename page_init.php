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
		$visiting_page = $inc_dir . "no_db.php";	
	}

?>
