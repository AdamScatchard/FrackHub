<?php
    permission_check("admin");
    $adverts = $db->query("fh_adverts", null, "active=0");
    $adverts_pending = count($adverts);
?>
<h2>Admin Control Panel</h2>
<hr>
<div class='contactContainer'>
<table>
        <tr>
            <td><a href="index.php?page=admin_users">Manage Users</a></td>
        </tr>
        <tr>
            <td><a href="index.php?page=admin_adverts">Manage Adverts <?php echo "(" . $adverts_pending . ")";?></a></td>
        </tr>
        <tr>
            <td><a href="index.php?page=admin_contact">Contact Us Emails</a></td>
        </tr>
        <tr>
            <td><a href="#">Manage Advert Photos</a></td>
        </tr>
        <tr>
            <td><a href="#">Flagged Messages</a></td>
        </tr>
        <tr>
            <td><a href="index.php?page=activitylogs">Website Activity</a></td>
        </tr>
        <tr>
            <td><a href="#">Set Language and labels</a></td>
        </tr>
        <tr>
            <td><a href="#">Banned IP addresses</a></td>
        </tr>
        <tr>
            <td><a href="index.php?page=admin_priviledges">Manage User Restrictions</a></td>
        </tr>
</table>
</div>
