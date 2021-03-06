<?php
	if (isset($_POST['login_btn'])){
		// login button clicked
		$results = $db->query("fh_users", ["id", "username", "password", "email", "timestamp"], "username = '" . $db->cleanSQLInjection($_POST['username']) . "' AND banned=0", True, NULL);
		$user_data = $db->getRow("fh_users", "username='" . $db->cleanSQLInjection($_POST['username']) . "' and active='1'");
    	if ($user_data){
    		if ($user_data['attempts'] == 3){
    		    // 3 attempts check for the time
    			if ((time() - $user_data['lastlogin_timestamp']) > $temporary_lock_time){
    			    login($encryption, $db, $user_data);
    			}else{
    			    echo "<h1 class='loginMsg'>This acccount is temporarily locked</h1>";
    			}

    		}else{
    		    login($encryption, $db, $user_data);
    		}
    	}else{
	    	echo "<h1 class='loginMsg'>No account associated with these login credentials, please use the sign up button</h1>";
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
			if (isset($_GET['page'])){
    			header ("location:?page=" . $_GET['page']);
			}else{
    			header ("location:?page=account");
			}
			die();
		}else{
			
    		$attempts = $user_data['attempts'];
    			if ($attempts >= 3){
    				echo "<h2 class='loginMsg'>Your account is temporarily locked</h2>";
    			}else{
    				echo "<h2 class='loginMsg'>You have " . 3 - $attempts . " remaining, you have entered the wrong login details</h2>";
    				$db->update("fh_users", ["attempts" => $attempts + 1, "lastlogin_timestamp"=> time()], "id=" . $user_data['id']);
    			}
		}
    }

    function loginForm(){
        // logged out show login form
        echo "<div class=\"banner\">";
        echo "<div class=\"welcome_msg\">";
        echo "<h2>Welcome to FrackHub!</h2><br><h4>We are a non-profit, community driven service that allows users to post &amp; borrow different items in exchange for credits.</h4><br><h3>Membership is free, sign up below and begin your FrackHub journey!</h3>";
        echo "</div>";
        echo ("<div class=\"login_div\">");
		echo ("<form action=\"" . $_SERVER['PHP_SELF'] . "\" method=\"post\" id='loginForm'>");        
		echo ("<input type=\"text\" name=\"username\" placeholder=\"Username\" required><br>");        
	    echo ("<input type=\"password\" name=\"password\"  placeholder=\"Password\" required><br>");         
		echo ("<button class=\"btn\" type=\"submit\" name=\"login_btn\">Sign in</button>"); 
		echo ("</form>");
        echo ("</div>");
        echo ("</div>");
        //<!-- Page content -->
        echo "<div class=\"content\">";

        echo "<h2 class=\"content-title\">Recent Items Submitted</h2>";
        echo ("<hr>");
    	$results = $GLOBALS['db']->query("fh_adverts", NULL, "active='1' AND available > 0", true, ["id"=>"DESC"], $GLOBALS['homepage_adverts']);
        echo "<div class='carusellBtnEnclosure'>";
        echo "<button class='carusellBtn' id='carusellLeftBtn' onclick=\"carusell('carrusell', 'right');\">&lt;</button>";
        echo "<div class='carrusell' id='carrusell'>";
        echo "<table><tr>";
        foreach ($results as $result){
            echo "<td class='topalign'><table><tr><td class='homePageAdverts'>";
            $img = $GLOBALS['db']->getRow("fh_advert_images", "advert_id=" . $result['id'] . " AND active='1'");
            if (isset($img['file_name'])){
                if ($img['file_name']){
                    echo "<img src='advert_images/" . $img['file_name'] . "' alt='frackhub image' class='advert_image_small'>";
                }
            }else{
                echo "<img src='img/noimage.jpg' alt='frackhub image' class='advert_image_small'>"; 
            }
            echo "</td></tr><tr><td>";
            echo "<h3><a href='index.php?page=item&view_item=" . $result['id'] . "'>" . $result['name'] . "</a></h3>";
            echo "</td></tr><tr><td>";
            echo  $result['description'];
            echo "</td></tr></table>";
            echo "</td>";
            
        }
        echo "</tr></table>";
        echo "</div>";
        echo "<button class='carusellBtn' id='carusellRightBtn' onclick=\"carusell('carrusell', 'left');\">&gt;</button>";
        echo "</div>";
        echo "</div>";
        
        //<!-- // Page content -->
        
    }
    
?>
<script>
    let carusellPosition = 0;
    function carusell(id, direction){
        
        let el = document.getElementById(id);
        let amt = el.offsetWidth;
        let max = (el.scrollWidth-el.offsetWidth);
        if (direction == "left"){
            if ((carusellPosition + amt) > max){
                carusellPosition = max;
            }else{
                carusellPosition+=amt
            }
        }else{
            if ((carusellPosition - amt) < 0){
                carusellPosition = 0;
            }else{
                carusellPosition-=amt
            }
        }
        el.scrollTo({left: carusellPosition, behaviour: 'smooth'});

    }
</script>
