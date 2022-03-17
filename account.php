<?php

echo "<h1>Account Details</h1>";

if (isset($uid)){

    $user = $db->getRow("fh_users", "id=" . $uid);

    echo "<table>";

    echo "<tr>";

    echo "<tr><td>Username:</td><td>" . $user['username'] . "</td></tr>";

    echo "<tr><td>Name:</td><td><input type=\"text\" name=\"name\" value=\"" . $user['name'] . "\"></td></tr>";

    echo "<tr><td>Surname:</td><td><input type=\"text\" name=\"surname\" value=\"" . $user['surname'] . "\"></td></tr>";

    echo "<tr><td>Password:</td><td><input type=\"password\" name=\"password\" placeholder\"Enter new password\"></td></tr>";

    echo "<tr><td>Phone 1:</td><td><input type=\"text\" name=\"phone1\" value=\"" . $user['phone1'] . "\"></td></tr>";

    echo "<tr><td>Phone 2:</td><td><input type=\"text\" name=\"phone2\" value=\"" . $user['phone2'] . "\"></td></tr>"; 

    echo "<tr><td>Address Line 1:</td><td><input type=\"text\" name=\"address_line1\" value=\"" . $user['address_line1'] . "\"></td></tr>";

    echo "<tr><td>Address Line 2:</td><td><input type=\"text\" name=\"address_line2\" value=\"" . $user['address_line2'] . "\"></td></tr>";

    echo "<tr><td>Address Line 3:</td><td><input type=\"text\" name=\"address_line3\" value=\"" . $user['address_line3'] . "\"></td></tr>";

    echo "<tr><td>Country:</td><td><input type=\"text\" name=\"country\" value=\"" . $user['country'] . "\"></td></tr>";

    echo "<tr><td>Post Code:</td><td><input type=\"text\" name=\"postcode\" value=\"" . $user['postcode'] . "\"></td></tr>";

    echo "<tr><td>Email:</td><td><input type=\"email\" name=\"email\" value=\"" . $user['email'] . "\"></td></tr>";

    echo "<tr><td>D.O.B:</td><td><input type=\"date\" name=\"dob\" value=\"" . $user['dob'] . "\"></td></tr>";

    echo "</table>";

    echo "<input type=\"submit\" value=\"Submit\">";

    echo "<h1>Items Borrowed</h1>";

    echo "<h1>Items Loanings</h1>";

    echo "<h1>My Credit Ledger</h1>";

}

// developed by Adam MacKay 2000418 - 14/03/22
?>
