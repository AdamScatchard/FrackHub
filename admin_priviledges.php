<?php
    permission_check("admin");
    $priviledges = $db->query("fh_priviledges", null, null, true);
    echo "<h2>User Priviledges:</h2>";
    echo "<hr>";
    echo "<div class='contactContainer'>";
    echo "<form method='post' action='index.php?page=admin_priviledges'>";
    echo "Select priviledge setting: ";
    echo "<select name='priviledgeSetting'>";
    $nameOfSetting = $priviledges[0]['name'];
    foreach ($priviledges as $priviledge){
        if (isset($_POST['priviledgeSetting'])){
            if ($priviledge['id'] == $_POST['priviledgeSetting']){
                $selected = "selected";
                $nameOfSetting = $priviledge['name'];
            }else{
                $selected = "";
            }
        }
        echo "<option $selected value='" . $priviledge['id'] . "'>". $priviledge['name'] . "</option>";
    }
    echo "</select>";
    echo "<input name='load' type='submit' value='Go' class='btn'>";
    echo "<hr>";
    if (isset($_POST['load'])){
        $getID = $_POST['priviledgeSetting'];
        $data = $db->getRow("fh_priviledge_settings", "id=" . $getID);
    }else{
        if (isset($_POST['save'])){
            // perform update and then get the details
            if (isset($_POST['admin'])){$saveValues['admin'] = 1;}else{$saveValues['admin'] = 0;}
            if (isset($_POST['account'])){$saveValues['account'] = 1;}else{$saveValues['account'] = 0;}
            if (isset($_POST['advertise'])){$saveValues['advertise'] = 1;}else{$saveValues['advertise'] = 0;}
            if (isset($_POST['bookings'])){$saveValues['bookings'] = 1;}else{$saveValues['bookings'] = 0;}
            if (isset($_POST['messenger'])){$saveValues['messenger'] = 1;}else{$saveValues['messenger'] = 0;}
            if (isset($_POST['admin_contactUs'])){$saveValues['admin_contactUs'] = 1;}else{$saveValues['admin_contactUs'] = 0;}
            if (isset($_POST['admin_adverts'])){$saveValues['admin_adverts'] = 1;}else{$saveValues['admin_adverts'] = 0;}
            
            $saved = $db->update("fh_priviledge_settings", $saveValues, "id=" . $_POST['priviledgeSetting']);
            if ($saved){
                echo "<h2>Saved changes to: $nameOfSetting </h2>";
            }else{
                echo "<h3>Something went wrong, please try again or report it to the developing team</h3>";
            }
            $data = $db->getRow("fh_priviledge_settings", "id=" .  $_POST['priviledgeSetting']);
        }else{
            // default page load (First entry in the priviledges database that is active)
            $data = $db->getRow("fh_priviledge_settings", "id=" . $priviledges[0]['id']);
        }
    }
    if ($data){
        echo "<h3>$nameOfSetting:</h3>";
        echo "<table>";
        echo "<tr>";
        echo "<td>Super Admin:</td>";
        echo "<td>";
        echo "To be coded: A super admin may not delete, edit or modify another super admin user";

        echo "</td>";
        echo "</tr>";
        
        
        echo "<tr>";
        echo "<td>Admin:</td>";
        echo "<td>";
        echo "<input type='checkbox' name='admin' " . (($data['admin'])? "checked":" ") . ">";

        echo "</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td>Account Management:</td>";
        echo "<td>";
        echo "<input type='checkbox' name='account' " . (($data['account'])? "checked":" ") . ">";
        echo "</td>";

        echo "</tr>";
        echo "<tr>";
        echo "<td>Advertise:</td>";

        echo "<td>";
        echo "<input type='checkbox' name='advertise' " . (($data['advertise'])? "checked":" ") . ">";
        echo "</td>";


        echo "</tr>";
        echo "<tr>";
        echo "<td>Book Items:</td>";

        echo "<td>";
        echo "<input type='checkbox' name='bookings' " . (($data['bookings'])? "checked":" ") . ">";
        echo "</td>";

        echo "</tr>";
        echo "<tr>";
        echo "<td>Mailbox:</td>";

        echo "<td>";
        echo "<input type='checkbox' name='messenger' " . (($data['messenger'])? "checked":" ") . ">";
        echo "</td>";

        echo "</tr>";
        echo "<tr>";
        echo "<td>Admin Contact Us</td>";

        echo "<td>";
        echo "<input type='checkbox' name='admin_contactUs' " . (($data['admin_contactUs'])? "checked":" ") . ">";
        echo "</td>";


        echo "</tr>";

        echo "<tr>";
        echo "<td>Admin Manage Adverts</td>";
        echo "<td>";
        echo "<input type='checkbox' name='admin_adverts' " . (($data['admin_adverts'])? "checked":" ") . ">";

        echo "</td>";


        echo "</tr>";

        echo "<tr>";
        echo "<td>Profiles</td>";

        echo "<td>";
        echo "<input type='checkbox' name='' " . (($data['userprofile'])? "checked":" ") . ">";

        echo "</td>";


        echo "</tr>";


        echo "</table>";
        echo "<input type='submit' name='save' value='Save' class='btn'>";
        echo "</form>";
    }
    echo "</div>"
?>
