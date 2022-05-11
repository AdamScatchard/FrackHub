<?php
    permission_check("admin");
    if (isset($_POST['logout'])){
        if (isset($_POST['selected'])){
            foreach ($_POST['selected'] as $item){
                    $db->update("fh_users", ['lastlogin_timestamp' => 0], "id=" .  $db->cleanSQLInjection($item));
            }
        }else{
            echo "<h2>No users selected</h2>";
        }
    }
    
    if (isset($_POST['update'])){
        if (isset($_POST['selected'])){
            foreach ($_POST['selected'] as $item){
                      $activeValue = $db->cleanSQLInjection($_POST['active'][$item]);
                      $priviledgeValue = $db->cleanSQLInjection($_POST['priviledgeLevel'][$item]);
                      $tempData = ["active" => $activeValue, "priviledge_id" => $priviledgeValue];
                    $db->update("fh_users", $tempData, "id=" .  $db->cleanSQLInjection($item));
            }
        }else{
            echo "<h2>No users selected</h2>";
        }
    }
    
    if (isset($_POST['ban'])){
        if (isset($_POST['selected'])){
            foreach ($_POST['selected'] as $item){
                // check is already banned
                $tempData = $db->getRow("fh_banned_connections", "user_id=" . $db->cleanSQLInjection($item));
                if (!$tempData){
                    // apply the ban
                    $tempData = $db->getRow("fh_users", "id=" . $db->cleanSQLInjection($item));
                    $data['user_ip'] = $tempData['ip_address'];
                    $data['user_id'] = $tempData['id'];
                    $data['admin_id'] = $uid;
                    $data['timestamp'] = time();
                    $actioned = $db->insert("fh_banned_connections", $data);
                    
                    // log out (incase the user is logged in at the time)
                    $db->update("fh_users", ['lastlogin_timestamp' => 0, 'banned'=>1, 'active' =>0], "id=" .  $db->cleanSQLInjection($item));
                    echo "<p>Changes to " . $tempData['username'];
                    if ($actioned){
                        echo " applied</p>";
                    }else{
                        echo " not applied</p>";
                    }
                }else{
                    echo "<h2>User already banned</h2>";
                }
            }
        }else{
            echo "<h2>No users selected</h2>";
        }
    }
    if (isset($_POST['delete'])){
        $tally = 0;
        if (isset($_POST['selected'])){
    
            foreach ($_POST['selected'] as $item){
                $tempData = $db->getRow("fh_users", "id=" . $db->cleanSQLInjection($item));
                if ($tempData != $user['priviledge_id']){
                    // not same ranking 
                    // this doesnt solve higher rankings ie Super Admin being deleted
                    // this is to be coded in the future
                    if ($tempData['active'] == 0){
                        // account is inactive
                        $activeAds = $db->query("fh_adverts", null, "userID=" .  $db->cleanSQLInjection($item) . " AND active = '1' AND available = '0'");
                        if (count($activeAds) == 0){
                            // OK to delete
                            $actioned = $db->delete("fh_users", "id=" . $db->cleanSQLInjection($item));
                            if ($actioned){
                                echo "<h3>User " . $item . " deleted</h3>";
                                $tally++;
                            }
                        }else{
                            // Activity is detected, you cannot delete a user with active adverts
                            echo "<h2>Activity Detected</h2>";
                            echo "There are active adverts associated with user: " . $tempData['username'] . "<br>";
                            echo "Unable to delete until the adverts are inactive and all items returned.";
                        }
                    }
                }

                // Deleted users must be INACTIVE first
                // This stops the user loggin in and creating new adverts
                // This also allows for any active adverts to see them returned
                // Once all items are returned, then the user can be deleted
                // If there adverts out there, this could pose a problem with credits
                // Delete the user
            }
        }else{
                echo "<h2>No users selected</h2>";
            }
        if (count($_POST['selected']) == $tally){
            echo "<p>All users deleted</p>";
        }else{
            echo "<p>Not all users were deleted</p>";
        }
    }

    $users = $db->query("fh_users", null, null, true);
    $accounTypes = $db->query("fh_priviledges", null, "active=1", false);
    echo "<h2>Admin Users:</h2>";
    echo "<hr>";
    echo "<div class='contactContainer'>";
    echo "<form name='admin_users' method='post' action='index.php?page=admin_users'>";
    echo "<table class='altRowStyles'>";
    
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
    echo "<th>Banned</th>";
    echo "<th>Selected</th>";
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
        echo "<td>";
        echo "<select name='active[" . $account['id'] . "]'>";
        echo "<option value='1'" . (($account['active'] == 1) ? " selected" : "") . ">Yes</option>";
        echo "<option value='0'" . (($account['active'] == 0) ? " selected" : "") . ">No</option>";

        echo "</select>";
        echo "</td>";
        
        
   /*     

     */
        echo "<td><select class='dropdown' name='priviledgeLevel[" . $account['id'] . "]'>";
        foreach ($accounTypes as $type){
            if ($type['id'] == $account['priviledge_id']){$selected = " selected";}else{$selected = "";}
            echo "<option " . $selected . " value='" . $type['id'] . "'>". $type['name'] . "</option>";
        }
        echo "</select></td>";
        echo "<td>";
        echo  (($account['banned'] == 1) ? "yes" : "no");
        echo "</td>";
        echo "<td>";
        echo "<input type='checkbox' name='selected[]' value='" . $account['id'] . "'>";
        echo "</td>";
        echo "</tr>";
        if ($account['active']){
            $active++;
        }
    }
?>
</table>
<?php
    echo "<br>Total Users: " . count($users) . "<br>";
    echo "Active Accounts: " . $active . "<br>";
    echo "<br>";

?>
<p>With selected users...</p>
<input type="submit" name="update" value="Update" class="btn">
<input type="submit" name="delete" value="delete users" class="btn">
<input type="submit" name="ban" value="ban users" class="btn">
<input type="submit" name="logout" value="log users out" class="btn">
</form>
</div>
