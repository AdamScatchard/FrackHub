<?php
    permission_check("admin");
	echo "<h2>Website Activity</h2>"; // stick to the styles developed by the theme of the website (<h1> tags)
	echo "<hr>";
	echo "<div class='contactContainer'>";
    
	echo "<table>";
	echo "<tr>";
	echo "<td>Entry ID</td>";
	echo "<td>Username</td>";
	echo "<td>Title</td>";
	echo "<td>Description</td>";
	echo "<td>Page</td>";
	echo "<td>Domain</td>";
	echo "<td>Date</td>";
	echo "<td>IP</td>";
	echo "<td>Sel</td>";
		echo "</tr>";
	if (isset($_POST['profileID'])){
    	$results = $db->query("activity", NULL, "userID=" . $db->cleanSQLInjection($_POST['profileID']), True, ["timestamp"=>"DESC"]);
	}else{
    	$results = $db->query("activity", NULL, NULL, True, ["timestamp"=>"DESC"], 100);
	}
	
    
	if (is_array($results)){
	    foreach ($results as $row){
			echo ("<tr>");
			echo ("<td>" . $row['id'] . "</td>");
			$username = $db->getRow("fh_users", "id=" . $row['userID']);
			echo ("<td>" . $username['username'] . "</td>");
			echo ("<td>" . $row['title'] . "</td>");
			echo ("<td>" . $row['description'] . "</td>");
			echo ("<td>" . $row['page'] . "</td>");
			echo ("<td>" . $row['domain'] . "</td>");
			echo ("<td>" . date("H:i:s d/M/Y", $row['timestamp']) . "</td>");
			echo ("<td>" . $row['ip_address'] . "</td>");

			echo ("<td><input type='checkbox' name='Item[]' value='" . $row['id'] . "'>");
			echo ("</tr>");
	    }
	    // your code needs to be clear, make sure the lecturer can see where and when the code stops
	    
	}
    echo ("</table>");
	echo "</div>";
	
	?>

</body>
</html>
