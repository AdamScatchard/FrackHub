<?php
if (isset($_GET['id'])){
    $profileID = $_GET['id'];
 //   if (is_int($profileID)){
        $user_data = $db->getRow("fh_users", "id=" . $profileID);
        if ($user_data){
            if (isset($uid)){
                $isAdmin = permission_check("admin", true);
            }else{
                $isAdmin = false;
            }
            
            echo "<h2>User: " . $user_data['username'] . "</h2>";
            echo "<hr>";
            echo "<div class='contactContainer'>";
            
            if (isset($_POST['edit'])){
                editProfile($user_data, $isAdmin);
            }else{
                viewOnly($user_data, $isAdmin);
            }
            
                        echo "<hr>";
            echo "Rating:";
            if ($user_data['rating_made'] > 0){
                $rating = 5 * ($user_data['rating_available'] / ($user_data['rating_made'] * 5)) ;
            }else{
                $rating = 0;
            }
            echo $rating . "/5<br>";
            echo "<hr>";
            echo "Items Available:";
            $items = $db->query("fh_adverts", null, "userID=" . $profileID . " AND active=1 AND available > 0");
            echo "<ol>";
            foreach ($items as $item){
                echo "<li><a href='index.php?page=item&view_item=" . $item['id'] . "'>" . $item['name'] . "</a>";
            }
            echo "</ol>";
            if (isset($uid)){
                echo "<form method='post' action='index.php?page=messages' >";
                echo "<input type='hidden' name='id' class='btn' value='" . $user_data['id'] . "'>";
                echo "<input type='submit' class='btn' value='Send Message' id='message' name='reply'>";
                echo "</form>";
            }
            if (isset($uid)){
                $idChk = $uid;
            }else{
                $idChk = 0;
            }
            if ($isAdmin==true || ($idChk==$profileID)){
                echo "<form method='post' action='index.php?page=userprofile&id=" .  $profileID . "'>";
                echo "<input type='submit' class='btn' value='Edit Profile' id='message' name='reply'>";
                echo "</form>";
            }
            if ($isAdmin){
                echo "<form method='post' action='index.php?page=activitylogs'>";
                echo "<input type='hidden' name='profileID' value='$profileID'>";
                echo "<input type='submit' class='btn' value='View Activity' id='message' name='activity'>";
                echo "</form>";
            }

            echo "</div>";
            
        }
            
  //  }
}

function viewOnly($user_data, $isAdmin){
            echo "Username: " . $user_data['username'] . "<br>";
            echo "Name: " . $user_data['name'] . "<br>";
            if ($isAdmin){
                echo "Admin View:<br>";
            echo "Surname: " . $user_data['surname'] . "<br>";
            echo "Email: " . $user_data['email'] . "<br>";
            echo "D.O.B: " . date("d/m/Y", $user_data['dob']) . "<br>";
            echo "House: " . $user_data['address_line1'] . "<br>";
            echo "Street: " . $user_data['address_line2'] . "<br>";
            echo "Town/City: " . $user_data['address_line3'] . "<br>";
            echo "Country: " . $user_data['country'] . "<br>";
            echo "Post Code: " . $user_data['postcode'] . "<br>";
            }else{
                echo "Non Admin View:<br>";
            }
            echo "Primary Phone: " . $user_data['phone1'] . "<br>";
            echo "Secondary Phone: " . $user_data['phone2'] . "<br>";
            if ($isAdmin){
                echo "Priviledge " . $user_data['priviledge_id'] . "<br>";
                echo ((strlen($user_data['ip_address']) < 20)? "IPv4": "IPv6") . " Address: " . $user_data['ip_address'] . "<br>";
            }
            echo "rating made: " . $user_data['rating_made'] . "<br>";
            echo "rating total: " . $user_data['rating_available'] . "<br>";
}

function editProfile($user_data, $isAdmin){
                echo "Username: " . $user_data['username'] . "<br>";
            echo "Name: " . $user_data['name'] . "<br>";
            if ($isAdmin){
                echo "Admin View:<br>";
            echo "Surname: " . $user_data['surname'] . "<br>";
            echo "Email: " . $user_data['email'] . "<br>";
            echo "D.O.B: " . date("d/m/Y", $user_data['dob']) . "<br>";
            echo "House: " . $user_data['address_line1'] . "<br>";
            echo "Street: " . $user_data['address_line2'] . "<br>";
            echo "Town/City: " . $user_data['address_line3'] . "<br>";
            echo "Country: " . $user_data['country'] . "<br>";
            echo "Post Code: " . $user_data['postcode'] . "<br>";
            }else{
                echo "Non Admin View:<br>";
            }
            echo "Primary Phone: " . $user_data['phone1'] . "<br>";
            echo "Secondary Phone: " . $user_data['phone2'] . "<br>";
            if ($isAdmin){
                echo "Priviledge " . $user_data['priviledge_id'] . "<br>";
                echo ((strlen($user_data['ip_address']) < 20)? "IPv4": "IPv6") . " Address: " . $user_data['ip_address'] . "<br>";
            }
            echo "rating made: " . $user_data['rating_made'] . "<br>";
            echo "rating total: " . $user_data['rating_available'] . "<br>";
            echo "<form method='post' action='index.php?page=messages' >";
            echo "<input type='hidden' name='id' class='btn' value='" . $user_data['id'] . "'>";
            echo "<input type='submit' class='btn' value='Send Message' id='message' name='reply'>";
            echo "</form>";
        
}

?>
