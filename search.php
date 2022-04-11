<?php
    $page_title = "Search: ";
    if (isset($_POST["search_text"])){
        $page_title .= $_POST["search_text"];
    }
    echo "<form method=\"post\" name=\"search_form\" target=\"index.php?page=search\">";
    echo "Search: <input type=\"text\" name=\"search_text\">";
    echo "<input type=\"submit\" name=\"submit\">";
    echo "</form>";

    if (isset($_POST["submit"])) {
    	$str = $_POST["search_text"];
    	$res = $db->query("fh_adverts", NULL, "name LIKE '%" . $str . "%'");
        if (count($res) > 0){
            echo "<h1>Results:</h1>";
            echo "<table><tr><th>Name</th><th>Description</th></tr>";
            foreach ($res as $result){
                echo "<tr>";
                echo "<td>" . $result['name'] . "</td>";
                echo "<td>" . $result['description'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }else{
            echo "<h1>No results located for: '" . $str . "'</h1>";
        }
    }
// code written by Adam Mackay (2000418)
?>
