<?php
    $page_title = "Search: ";
    if (isset($_POST["search_text"])){
        $page_title .= $_POST["search_text"];
    }
    echo "<h2>Results:</h2>";
    echo "<hr>";
    echo "<div class='contactContainer'>";
    if (isset($_POST["searchBtn"])) {
    	$str = $_POST["search_text"];
    	if (($str != null) && ($str != "")){ 
        	$res = $db->query("fh_adverts", NULL, "name LIKE '%" . $str . "%' AND available = 1");
            if (count($res) > 0){
                echo "<table><tr><th>Name</th><th>Description</th></tr>";
                foreach ($res as $result){
                    echo "<tr>";
                    echo "<td><a href=\"index.php?page=item&view_item=" . $result['id'] . "\">" . $result['name'] . "</a></td>";
                    echo "<td>" . $result['description'] . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }else{
                echo "<h1>No results located for: '" . $str . "'</h1>";
            }
    	}else{
    	    echo "<h3>What is it you are looking for?</h3>";
    	    echo "You have submitted a search request without any information to look up<br>";
    	    echo "Try using the search bar again, but add a little imagination this time<br>";
    	    echo "What is it you would like to borrow";
    	}
    }
    echo "</div>";
?>
