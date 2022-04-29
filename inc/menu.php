<?php
	if (isset($uid)){
        $unread_messages = count($db->query("fh_messages", ["id"], "user_id=" . $uid . " AND viewed=0 AND mailbox=1"));
	}
?>
<header>
<div class="navbar">
	<a href="index.php">
	<img src="<?php echo $img_dir . 'logo.png'; ?>" class='menu_logo'">
    </a>
    <div id="searchBar">
    <form method="post" action="index.php?page=search">
        <input type="text" name="search_text" id="search" placeholder="search" required>
        <input type="submit" name="searchBtn" id="searchBtn" value="Search">
    </form>
    </div>
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
	        echo "<li><a href=\"index.php?page=register\" id='signup'>Sign Up</a></li>";
	    }
	?>
	</ul>
</div>
</header>
<main>
<?php
if (isset($uid)){
    $latest_transaction = $db->query("fh_ledger", ["balance"], "userID = '" . $uid . "'", false, ["timestamp" => "DESC"], 1);
	$credits = $latest_transaction? $latest_transaction[0]["balance"] : 0;
    echo '<div class="uidBar">';
    echo "<img alt='user' class='icon' src=" . $img_dir . "user.png>";
    echo "Username: " . $user['username'];
    echo " | ";
    echo "<img alt='credits' class='icon' src=" . $img_dir . "credit.png>";
    echo " Credits: " . $credits;
    if (isset($uid)){
        echo '<a href="?page=logout" class="logoutBtn">Logout</a>';		
    }

    echo "</div>";
}
?>
