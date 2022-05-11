<?php
    permission_check("admin_adverts");
    echo "<h2>Ban Table:</h2>";
    echo "<hr>";
    if (isset($_POST['removebtn'])){
        foreach ($_POST['remove'] as $item){
           $done =  $db->delete("fh_banned_connections", "user_id='" . $db->cleanSQLInjection($item) . "'");
           $done2 = $db->update("fh_users", ["banned"=>0], "id='". $db->cleanSQLInjection($item) . "'" );
           if ($done){
               echo "Removed item: " . $item . " from IP banned list<br>";
           }
        }
    }
    echo "<p>Only the admin that applied the ban can remove a ban</p>";
    $results = $db->query("fh_banned_connections",null,null, true);
    echo "<form name='form_banlist' method='post' action='index.php?page=admin_bans'>";
    echo "<table>";
    echo "<tr>";
    echo "<th>User ID</th>";
    echo "<th>Profile</th>";
    echo "<th>IP Address</th>";
    echo "<th>IP Type</th>";
    echo "<th>Date:</th>";
    echo "<th>Banned By</th>";
    echo "<th>";
    echo "</tr>";
    foreach ($results as $entry){
        echo "<tr>";
        echo "<td>";
        echo $entry['user_id'];
        echo "</td>";
        echo "<td>";
        echo "<a href='?page=userprofile&id=" . $entry['user_id'] . "'>Profile</a>";
        echo "</td>";
        echo "<td>";
        echo $entry['user_ip'];
        echo "</td>";
        echo "<td>";
        if (strlen($entry['user_ip'])<=20){
            echo "IPv 4";
        }else{
            echo "IPv 6";
        }
        echo "</td>";
        echo "<td>";
        echo date("h:i:s d/m/Y", $entry['timestamp']);
        echo "</td>";
        echo "<td>";
        echo $entry['admin_id'];
        echo "</td>";
        echo "<td>";
        if ($uid == $entry['admin_id']){
            echo "<input type='checkbox' name='remove[]' value='".$entry['user_id'] . "'>";
        }else{
            echo "----";
        }
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<input type='submit' name='removebtn' class='btn' value='remove'>";
    echo "</form>";
?>
