<?php
	if (isset($uid)){
        $unread_messages = count($db->query("fh_messages", ["id"], "user_id=" . $uid . " AND viewed=0 AND mailbox=1"));
	}
?>

<header>
<div class="navbar">
	<a href="index.php">
	<?php echo "<img src='" . $img_dir . "logo.png'"; ?> class="menu_logo" alt="logo">
    </a>
    <div id="searchBar">
    <form method="post" action="index.php?page=search">
        <input type="text" name="search_text" id="searchDesktop" placeholder="search" required>
        <input id="desktopSearchIcon" type="image" name="searchBtn" src="<?php echo $img_dir . "search.png"; ?>" alt="Submit" >
    </form>
    </div>
    <?php
        if (isset($uid)){
            // mobile menu logged in is positioned differently to when logged out
            echo "<div id='mobileMenuLI'>";
        }else{
            // logged out mobile menu (css differed to mobieMenuLI)
            echo '<div id="mobileMenu">';
        }
    ?>
        <div class="MMcontainer" onclick="myFunction(this)">
          <div class="MMbar1"></div>
          <div class="MMbar2"></div>
          <div class="MMbar3"></div>
        </div>
        <div id="mobileSearchBar">
        <form method="post" action="index.php?page=search">
            <input type="text" name="search_text" id="searchMobile" placeholder="search" required>
             <input type="image" id="mobileSearchIcon" name="searchBtn" src="<?php echo $img_dir . "search.png"; ?>" alt="Submit" >
        </form>
        </div>
        <div id="mobileMenuBts">
        <ul id="mMenu">
    	<?php 	
    		if (isset($uid)){
                echo '<li><a href="?page=logout" id="mobileLogoutBtn">Logout</a></li>';		

    		    if (isset($permissions)){
    		        if ($permissions['admin'] == 1){
            		    echo "<li id='mobileAdminBtn'><a class=\"active\" href=\"?page=admin_home\">Admin</a></li>";
    		        }
    		        if ($permissions['account'] == 1){
            		    echo "<li><a class=\"active\" href=\"?page=account\">Account</a></li>";
    		        }
    		        if ($permissions['advertise'] == 1){
            		    echo "<li><a class=\"active\" href=\"?page=advertise\">Advertise</a></li>";
    		        }
    		        if ($permissions['messenger'] == 1){
                    	echo "<li><a href='index.php?page=messages'>Messages (". $unread_messages . ")</a></li>";
    		        }
    		    }
    
    		}else{			
    		    echo "<li><a class=\"active\" href=\"index.php\">Home</a></li>";
    		}
    	?>
    	<li><a href="?page=contact">Contact</a></li>	
    	<?php		
    	    if (isset($uid) == false){
    	        echo "<li><a href=\"index.php?page=register\" id='signupMobile'>Sign Up</a></li>";
    	    }
    	?>
    	</ul>
    	</div>
    </div>
    <div id="desktopMenu">
    	<ul id="menu">
    	<?php 	
    		if (isset($uid)){
    		    if (isset($permissions)){
    		        if ($permissions['admin'] == 1){
            		    echo "<li><a class=\"active\" href=\"?page=admin_home\">Admin</a></li>";
    		        }
    		        if ($permissions['account'] == 1){
            		    echo "<li><a class=\"active\" href=\"?page=account\">Account</a></li>";
    		        }
    		        if ($permissions['advertise'] == 1){
            		    echo "<li><a class=\"active\" href=\"?page=advertise\">Advertise</a></li>";
    		        }
    		        if ($permissions['messenger'] == 1){
                    	echo "<li><a href='index.php?page=messages'>Messages (". $unread_messages . ")</a></li>";
    		        }
    		    }
    		}else{			
    		    echo "<li><a class=\"active\" href=\"index.php\">Home</a></li>";
    		}
    	?>
    	<li><a href="?page=contact">Contact</a></li>	
    	<?php		
    	    if (isset($uid) == false){
    	        echo "<li><a href=\"index.php?page=register\" id='signupDesktop'>Sign Up</a></li>";
    	    }
    	?>
    	</ul>
    </div>
</div>
</header>
<main>
<?php
if (isset($uid)){
    $latest_transaction = $db->query("fh_ledger", ["balance"], "userID = '" . $uid . "'", false, ["timestamp" => "DESC"], 1);
	$credits = $latest_transaction? $latest_transaction[0]["balance"] : 0;
    echo '<div class="uidBar">';
    echo "<img alt='user' class='icon' src='" . $img_dir . "user.png'>";
    echo "Username: " . $user['username'];
    echo " | ";
    echo "<img alt='credits' class='icon' src='" . $img_dir . "credit.png'>";
    echo " Credits: " . $credits;
    if (isset($uid)){
        echo '<a href="?page=logout" class="logoutBtn" id="desktopLogoutBtn">Logout</a>';		
    }

    echo "</div>";
}
?>
