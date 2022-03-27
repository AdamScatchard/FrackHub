<?php
    include ("page_init.php"); // read the documentation I wrote (it mentioned page_init.php) - Adam M
    
	echo "<h1>Activity Logs</h1>"; // stick to the styles developed by the theme of the website (<h1> tags)
	
	echo "<table border='1' width='300' cellspacing='0'>";
			echo "<td></td>";
			echo "<td>id</td>";
			echo "<td>text_field</td>";
			echo "<td>timestamp</td>";
			echo "<td>viewed</td>";
		echo "</tr>";
		
	// research what table you will be connecting with and what data your extracting
	// then modify the paramaters to this method in line with the documentation
	
	$results = $db->query("taskZero", NULL, NULL, True, ["id"=>"ASC"]);
	if (is_array($results)){
		echo "<table name='results' id='results'>";
	    foreach ($results as $row){
			echo ("<tr>");
			echo ("<td>" . $row['id'] . "</td>");
			echo ("<td>" . $row['text_field'] . "</td>");
			echo ("<td>" . $row['timestamp'] . "</td>");
			echo ("<td>" . $row['viewed'] . "</td>");
			echo ("<td><input type='checkbox' name='Item[]' value='" . $row['id'] . "'>");
			echo ("</tr>");
	    }
	    // your code needs to be clear, make sure the lecturer can see where and when the code stops
	    
        echo ("</table>");
        
        // this is outside of the table tags and it not needed
		echo "<tr>";
			echo "<td>1</td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
			echo "<td></td>";
		echo "</tr>";
	echo "</table>";    // extra close table tag
	}
	
	// the purpose is to give you enough information to push your self to research and not give you the answers
	// please read the documentation I have provided, if your not able to make sense of the documenation, i can offer you support here
	// naturally, you need to come out of your comfort zone, as I gave you some information (examples)
	// and you pasted the examples and confused why it didnt work, this would be me coding it for you
	// and I want you to learn, the key aspect is to research 
	
	// connect to PHP my admin have a look at the tables and cells and work out what it is you want to filter out of the table and place it as paramters to the method query
	?>

</body>
</html>
