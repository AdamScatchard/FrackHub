<?php
    permission_check("admin");
    $adverts = $db->query("fh_adverts", null, "active=0");
    $photos = $db->query("fh_advert_images", null, "active=0");

    $adverts_pending = count($adverts);
    $adverts_photos = count($photos);
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
            <td><a href="index.php?page=admin_photos">Manage Advert Photos <?php echo "(" . $adverts_photos . ")"; ?></a></td>
        </tr>
        <tr>
            <td><a href="index.php?page=activitylogs">Website Activity</a></td>
        </tr>
        <tr>
            <td><a href="index.php?page=admin_bans">Banned IP addresses</a></td>
        </tr>
        <tr>
            <td><a href="index.php?page=admin_priviledges">Manage User Restrictions</a></td>
        </tr>
</table>
</div>
