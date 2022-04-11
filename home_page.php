<?php
	if (isset($_POST['login_btn'])){
		// login button clicked
		$results = $db->query("fh_users", ["id", "username", "password", "email", "timestamp"], "username = '" . $_POST['username'] . "'", True, NULL);
		$user_data = $db->getRow("fh_users", "username='" . $_POST['username'] . "' and active='1'");
    	if ($user_data){
    		if ($user_data['attempts'] == 3){
    		    // 3 attempts check for the time
    			if ((time() - $user_data['lastlogin_timestamp']) > $temporary_lock_time){
    			    login($encryption, $db, $user_data);
    			}else{
    			    echo "<h1>This acccount is temporarely locked</h1>";
    			}

    		}else{
    		    login($encryption, $db, $user_data);
    		}
    	}else{
	    	echo "<h1>No account associated with these login credentials, please use the register button</h1>";
	    }
	}
    loginForm();

    function login($encryption, $db, $user_data){
        $encryption->setPlainText($_POST['password'] . $user_data["username"] . $user_data["email"]. $user_data["timestamp"]);
		if ($user_data["password"] == $encryption->classRun()) {
			setcookie($GLOBALS['login_cookie'], $user_data['id'] , $cookie_time);
			$timeStamp = time();
			$db->update("fh_users", ["lastlogin_timestamp" => $timeStamp, "attempts"=>0], "id=" . $user_data['id']);
			$salted_key = $user_data['id'] . $user_data['password'] . $timeStamp . $user_data['timestamp'];
			$encryption->setPlainText(trim($salted_key));
			$code = $encryption->classRun();
			setcookie($GLOBALS['session_code'],$code , $GLOBALS['cookie_time']);
			header ("location:?page=members_home");
			die();
		}else{
			
    		$attempts = $user_data['attempts'];
    			if ($attempts >= 3){
    				echo "<h2>Your account is temporarely locked</h2>";
    			}else{
    				echo "<h2>You have " . 3 - $attempts . " remaining, you entered the wrong login details</h2>";
    				$db->update("fh_users", ["attempts" => $attempts + 1, "lastlogin_timestamp"=> time()], "id=" . $user_data['id']);
    			}
		}
    }

    function loginForm(){
        // logged out show login form
        echo "<div class=\"banner\">";
        echo "<div class=\"welcome_msg\">";
        echo "<h1>Make use of your junk with Frack Hub</h1>";
        echo "<p>Use And Submit Your</p><br>"; 
        echo "Items And<br>"; 
        echo "Gain Credit! <br>";
        echo "<span>👩‍🦳👩‍🍳🧑👨‍🔧</span>";
        echo "</p>";
        echo "<a href=\"index.php?page=register\" class=\"btn\">Join Here</a>";
        echo "</div>";
        echo ("<div class=\"login_div\">");
		echo ("<form action=\"" . $_SERVER['PHP_SELF'] . "\" method=\"post\" >");        
		echo ("<h2>Login</h2>");        
		echo ("<input type=\"text\" name=\"username\" placeholder=\"Username\">");        
	    echo ("<input type=\"password\" name=\"password\"  placeholder=\"Password\">");         
		echo ("<button class=\"btn\" type=\"submit\" name=\"login_btn\">Sign in</button>");        
		echo ("</form>");
        echo ("</div>");
        echo ("</div>");
        //<!-- Page content -->
        echo "<div class=\"content\">";

        echo "<h2 class=\"content-title\">Recent Items Submitted:</h2>";
        echo ("<hr>");

        //	<!-- more content still to come here ... -->
        
        echo "</div>";
        
        //<!-- // Page content -->
        
    }
    
?>
