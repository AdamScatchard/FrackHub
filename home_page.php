<?php

	if (isset($_POST['login_btn'])){

		// login button clicked

		$results = $db->query("fh_users", ["id", "username", "password"], "username = '" . $_POST['username'] . "'", True, NULL);

		if ($results[0]["password"] == $_POST['password']) {

			setcookie("frachub", $results[0]['id'] , $cookie_time);

			header ("location:?page=members_home");

			die();

		}else{

			echo "access denied";

		}

	}

?>

    <?php 	

        if (isset($uid)){

            if ($uid > 0){

                // welcome message for logged in member 

                // maybe an account summary such as

                // count of messages unread

                // count of adverts active

                // reminder of items due back

                include ('members_home.php');

            }

        }else{

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
