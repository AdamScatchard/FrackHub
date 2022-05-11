<?php
    echo "<h3>You are viewing...</h3>";
    echo "<hr>";
    echo "<div class='contactContainer'>";
    $item_no = $db->cleanSQLInjection($_GET['view_item']);
    $result = $db->getRow("fh_adverts", "id=" . $item_no);
    if ($result){
        $seller_data = $db->getRow("fh_users", "id=" . $result["userID"]);
        view_advert($db, $result, $seller_data);
    
        if (isset($uid) && $seller_data){
            if (permission_check("bookings", true)){
                if ($uid != $result['userID']){
                    echo "<div>";   
                    echo "<h2>I would like to...</h2>";
                    echo "<form method='post' action='index.php?page=messages' >";
                    echo "<input type='hidden' name='id' class='btn' value='" . $item_no . "'>";
                    echo "<input type='submit' class='btn' value='Send Message' id='message' name='reply'>";
                    echo "</form>";
                    if ($permissions['bookings'] == 1){
                        if ($uid != $result['userID']){
                            echo "<form action='index.php?page=book_item' name='' method='post'>";
                            echo "Quantity: <input type='number' min='1' max='" .$result['amount_available'] . "' name='amount_loaned'>";
                            echo "<input type='hidden' name='id' value='" . $result['id'] . "'>";
                            echo "<input class='btn' type='submit' value='Book Item' name='submit' id='book_item'>";
                            echo "</form>";
                        }
                    }
                    echo "</div>";
                }
            }
        }else{
            if ($seller_data){
                echo "<h2>Want to hire this product?</h2>";
                echo "<p><a href='index.php?page=register'>Sign up today</a> or <a href='index.php'>Sign in</a></p>";
            }
        }
    }else{
        echo "<h2>Item not found</h2>";
        echo "<p>This item appears to have been removed by the user who posted it or has been deleted by admin</p>";
    }
function view_advert($db, $result, $seller_data){
    if ($seller_data){
        if (isset($_GET['view_item'])){
            $item_no = $db->cleanSQLInjection($_GET['view_item']);
            $img = $db->getRow("fh_advert_images", "advert_id=" . $result['id'] . " AND active='1'");
            echo "<div id='adImg' class='advImgDiv'>";
                if (isset($img['file_name'])){
                    if ($img['file_name']){
                        echo "<img src='advert_images/" . $img['file_name'] . "' alt='frackhub image' class='advert_image'>";
                    }
                }else{
                    echo "<img src='img/noimage.jpg' alt='frackhub image' class='advert_image'>"; 
                }
            echo "</div>";
            echo "<div id='advertContent' class='adDescDiv'>";        
                echo "<table>";
                echo "<tr>";
                echo "<td><h2>" .$result['name']. "</h2></td>";
                echo "</tr>";
                echo "<tr>";
                echo "<td><h3>Description</h3></td>";
                echo "</tr><tr>";
                echo "<td>" .  $result["description"] . "</td>";
                echo "</tr>";
                echo "<tr>";
                echo "<td><h3>Available</h3></td>";
                echo "</tr>";
                echo "<tr>";
                echo "<td>" . $result["amount_available"] . "</td>";
                echo "</tr>";
                echo "<tr>";
                echo "<td><h3>Area</h3></td>";
                echo "</tr>";
                echo "<tr>";
                echo "<td>" . $seller_data["address_line3"]  . "</td>";
                echo "</tr><tr>";
                echo "<td><h3>Cost per day:</h3></td>";
                echo "</tr>";
                echo "<tr>";
                echo "<td>" . $result["credits"] . "</td>";
                
                if ($result["collection"] != 0){
                    echo "</tr>";
                    echo "<tr><td><h3>This item is for collection only</h3>";
                    // check if you have this item
                    $found = false;
                    if (isset($GLOBALS['uid'])){
                        $results = $db->getRow("fh_items_loaned", "loanerID = '" . $GLOBALS['uid'] . "' and itemID='" . $GLOBALS['item_no'] . "'");
                        if ($results){
                            $found = true;
                        }
                    }
                    if (!$found){
                        echo "Collection information will be available once item has been booked</td>";
                    }else{
                        echo "Address:<br>";
                        echo $seller_data["address_line1"] . "<br>";
                        echo $seller_data["address_line2"] . "<br>";
                        echo $seller_data["address_line3"] . "<br><br>";
                        echo $seller_data["country"] . "<br>";
                        echo $seller_data["postcode"] . "<br><br>";
                        echo "Phone:<br>";
                        echo $seller_data["phone1"] . "<br>";
                        echo $seller_data["phone2"] . "<br>";
                    }
                }
                echo "</tr>";
                echo "</table>";
        }else{
            echo "<h1>Opps something went wrong in your URL</h1>";    
        }
    }else{
        echo "<h2>Item not available</h2>";
        echo "<p>The user associated with this item, is no longer using our services.</p>";

    }
    echo "</div>";
    echo "</div>";
}
?>
