<?php
if (isset($_GET['view_item'])){
    $item_no = $_GET['view_item'];
    $result = $db->getRow("fh_adverts", "id=" . $item_no);
    echo "<h1>You are viewing...</h1>";
    echo "<h2>" . $result['name'] . "</h2>";
    echo "<h3>Description:</h3>";
    echo "<p>" . $result["description"] . "</p>";
    
    echo "<h2>I would like to...</h2>";
    echo "<input type='button' value='Send Message' id='message'>";
    echo "<input type='button' value='Book' id='book_item'>";
}else{
    echo "<h1>Opps something went wrong in your URL</h1>";
}
?>
