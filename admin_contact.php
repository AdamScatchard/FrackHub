<?php
    permission_check("admin_contactUs");
    echo "<h2>Contact Us Emails:</h2>";
    echo "<hr>";
    $adverts = $db->query("fh_contactus", null, "viewed=0");
    echo "<div class='contactContainer'>";
    echo "<table>";
    echo "<tr>";
    echo "<th>Time Date</th>";
    echo "<th>Name</th>";
    echo "<th>E-Mail</th>";
    echo "<th>Subject</th>";
    echo "<th>Senders IP</th>";
    echo "<th>View</th>";
    echo "</tr>";
    foreach ($adverts as $email){
        echo "<tr>";
        echo "<td>";
        echo date("H:i d/m/Y", $email['timestamp']);
        echo "</td>";
        echo "<td>";
        echo $email['name'];
        echo "</td>";
        echo "<td>";
        echo $email['email'];
        echo "</td>";
        echo "<td>";
        echo $email['subject'];
        echo "</td>";
        echo "<td>";
        echo $email['ip'];
        echo "</td>";
        echo "<td>";
        echo "<a href='index.php?page=admin_reply&id=" . $email['id'] . "'>View Email</a>";
        echo "</td>";
        echo "</tr>";

    }
    echo "</table></div>";
?>
