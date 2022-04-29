<?php
    permission_check("admin");
    if (isset($_POST['submit'])){
        foreach ($_POST['users'] as $item){
            if (in_array($item, $_POST['active'])){
                    $db->update("fh_users", ['active' => 1], "id=" . $item);
            }else{
                $db->update("fh_users", ['active' => 0], "id=" . $item);
            }    

        }
    }
    $users = $db->query("fh_users", null, null, true);
    $accounTypes = $db->query("fh_priviledges", null, "active=1", false);
    echo "<h2>Admin Users:</h2>";
    echo "<hr>";
    echo "<div class='contactContainer'>";
    echo "<form name='admin_users' method='post' action='index.php?page=admin_users'>";
    echo "<table>";
    
    echo "<tr>";
    echo "<th>Username</th>";
    echo "<th>Name</th>";
    echo "<th>Surname</th>";
    echo "<th>Email</th>";
    echo "<th>First Line Address</th>";
    echo "<th>Phone</th>";
    echo "<th>Registered</th>";
    echo "<th>Last Login</th>";
    echo "<th>IP</th>";
    echo "<th>Active</th>";
    echo "<th>Account Type</th>";

    echo "</tr>";
    $active = 0;
    foreach ($users as $account){
        echo "<tr>";
        echo "<td><a href='index.php?page=userprofile&id=" . $account['id'] . "'>" . $account['username'] . "</a></td>";
        echo "<td>" . $account['name'] . "</td>";
        echo "<td>" . $account['surname'] . "</td>";
        echo "<td>" . $account['email'] . "</td>";
        echo "<td>" . $account['address_line1'] . "</td>";
        echo "<td>" . $account['phone1'] . "</td>";
        echo "<td>" . date("d/m/Y",  $account['timestamp']) . "</td>";
        echo "<td>" . date("H:i:s d/m/Y",  $account['lastlogin_timestamp']) . "</td>";
        echo "<td>" . $account['ip_address'] . "</td>";
        echo "<td><input type='checkbox' name='active[]' " . (($account['active'] == 1) ? "checked" : ""). " value='" . $account['id'] . "'> <input type='hidden' value='" . $account['id'] . "' name='users[]'></td>";
        echo "<td><select class='dropdown' name='priviledgeLevel[]'>";
        
        foreach ($accounTypes as $type){
            if ($type['id'] == $account['priviledge_id']){$selected = "selected";}else{$selected = "";}
            echo "<option " . $selected . " value='" . $type['id'] . "'>". $type['name'] . "</option>";
        }
        echo "</select></td>";
        echo "</tr>";
        if ($account['active']){
            $active++;
        }
    }
    echo "Total Users: " . count($users) . "<br>";
    echo "Active Accounts: " . $active . "<br>";
    echo "<br>";

?>
</table>
<input type="submit" name="submit" value="Change Details" class="btn">

</form>
</div>  
