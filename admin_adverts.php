<?php
    permission_check("admin_adverts");
    echo "<h2>Manage Advertisements:</h2>";
    echo "<hr>";
    //
    if (isset($_POST['activate'])){
        // submission made
        foreach($_POST['selection'] as $item){
            $db->update("fh_adverts", ['active' => 1], "id=" . $item);
        }
    }
    
    if (isset($_POST['delete'])){
        foreach ($_POST['selection'] as $item){
            $db->delete("fh_adverts", "id=" . $item);
            $picName = $db->getRow("fh_advert_images", "advert_id=" . $item);
            if ($picName['file_name']){
                unlink($img_dir . $picName['file_name']);
            }
        }
        
        // for deleting we need 2 things
        // delete the advert 
        // delete any associated images off the server
    }
    
    $adverts = $db->query("fh_adverts", null, "active=0");
    echo "<div class='contactContainer'>";
    echo "<h3 class='subheading'>Inactive Adverts</h3>";
    echo "<form method='post' action='" .  $_SERVER['PHP_SELF'] . "?page=admin_adverts' name='advertForm'>"; 
    echo "<table>";
    echo "<tr>";
    echo "<th>Advert</th>";
    echo "<th>User</th>";
    echo "<th>time</th>";
    echo "<th>Date</th>";
    echo "<th>Select</th>";
    echo "</tr>";
    foreach ($adverts as $advert){
        echo "<tr>";
        echo "<td>";
        echo "<a href='https://frackhub.000webhostapp.com/index.php?page=item&view_item=" . $advert['id'] . "' target='_blank'>" . $advert['name'] . "</a>";
        echo "</td>";
        echo "<td>";
        $userD = $db->getRow("fh_users", "id=" . $advert['userID']);
        echo $userD['username'];
        echo "</td>";
        echo "<td>";
        echo date("H:i:s", $advert['timestamp']);
        echo "</td>";
        echo "<td>";
        echo date("d/m/Y", $advert['timestamp']);
        echo "</td>";
        echo "<td>";
        echo "<input type='checkbox' name='selection[]' value='" . $advert['id'] . "'>";
        echo "</td>";
        echo "</tr>";

    }
    echo "</table>";
    echo "<input type='submit' class='btn' name='activate' value='Activate'>";
    echo "<input type='submit' class='btn' name='delete' value='Delete'>";

    echo "</form></div>";
?>
