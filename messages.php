<?php
    permission_check("messenger");
    // submission checks
    
    // read status change to unread
    if (isset($_POST['viewed'])){
        // check for deleveloper mode manipulation (The ID should match the UID otherwise its an attempt
        // to delete other peples mailboxes)
        
        // THE FOLLOWING LINE NEEDS AMENDING TO PREVENT SQL INJECTION!
        $message = $db->getRow("fh_messages", "id=" . $_POST['id']);
        if ($message){
            // message found (Should be found when used correctly but if manipulated risks not being found)
            if ($message['user_id'] == $uid){
                // matches the user proceed with the changes
                $db->update("fh_messages", ['viewed' => 0], "id=" . $_POST['id']);
            }
        }
    }
    
    // delete message
        if (isset($_POST['delete_mail'])){
        // check for deleveloper mode manipulation (The ID should match the UID otherwise its an attempt
        // to delete other peples mailboxes)
        
        // THE FOLLOWING LINE NEEDS AMENDING TO PREVENT SQL INJECTION!
        $message = $db->getRow("fh_messages", "id=" . $_POST['id']);
        if ($message){
            // message found (Should be found when used correctly but if manipulated risks not being found)
            if ($message['user_id'] == $uid){
                // mark as deleted (No message is deleted due to criminal activity risks)
                  $db->update("fh_messages", ["deleted" => 1], "id='" . $_POST['id'] . "'");
            }
        }
    }

    
    
    echo "<h3>Mailbox</h3>";
    echo "<hr>";
    echo "<div class='contactContainer'>";
    echo "<div class='column'>";
    echo "<table>";
    echo "<tr>";
    echo "<td><h1><a href='?page=messages&mailbox=inbox'>Inbox</a></h1></td>";
    echo "<td rowspan=2 class='mailbox'>";
    // here for the mailbox data
        if (isset($_GET['mailbox'])){
            if ($_GET['mailbox']=="outbox"){
                echo "<h2>Outbox</h2>";
                $mailbox = $db->query("fh_messages", null, "sender_id=" . $uid . " AND mailbox=2");
            }else{
                echo "<h2>Inbox</h2>";
                $mailbox = $db->query("fh_messages", null, "user_id=" . $uid . " AND mailbox=1");
            }
        }else{
            echo "<h2>Inbox</h2>";
            $mailbox = $db->query("fh_messages", null, "user_id=" . $uid . " AND mailbox=1");
        }
        if (count($mailbox) > 0){
            echo "<table class='tableMailbox'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th class='mailboxCells'>From</th>";
            echo "<th class='mailboxCells'>Subject</th>";
            echo "<th class='mailboxCells'>Item</th>";
            echo "<th class='mailboxCells'>Date</th>";
            echo "<th class='mailboxCells'>Read</th>";
            echo "<th class='mailboxCells'></th>";
            echo "</tr>";
            foreach ($mailbox as $message){
                echo "<tr>";
                $recipient = $db->getRow("fh_users", "id='" . $message['sender_id'] . "'" );
                echo "<td><a href='index.php?page=messages&read=" . $message['id'] . "'>". $recipient['username'] . "</a></td>";
                echo "<td><a href='index.php?page=messages&read=" . $message['id'] . "'>" . $message['subject'] . "</a></td>";
                $item = $db->getRow("fh_adverts", "id='" . $message['item_id'] . "'" );
                if ($item){
                    echo "<td><a href='index.php?page=messages&read=" . $message['id'] . "'>" . $item['name'] . "</a></td>";
                }else{
                    echo "<td>DB Error</td>";
                }
                echo "<td><a href='index.php?page=messages&read=" . $message['id'] . "'>" . date('h:i:s d/m/Y', $message['timestamp']) . "</a></td>";
                echo "<td><a href='index.php?page=messages&read=" . $message['id'] . "'>" . (($message['viewed'] == 1)? "Read" : "Unread") . "</a></td>";
                echo "<td><input type='checkbox' value='" . $message['id'] . "' name='delete_mail[]'></td>";
                echo "</tr>";
            }
            echo "</table>";
        }else{
            echo "<p>You have no messages</p>";
        }
    echo "</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td><h1><a href='?page=messages&mailbox=outbox'>Outbox</a></h1></td>";
    echo "</tr>";
    echo "</table>";
    echo "</div>";
    echo "<div class='column'>";

    if (isset($_GET['read'])){
        $mail = $db->getRow("fh_messages", "id='". $_GET['read'] . "' AND user_id = '" . $uid . "' OR sender_id = '" . $uid . "'");
        if ($mail){
            echo "<br>";
            echo "<h3>Subject: ". $mail['subject'] . "</h3>";
            echo "<br>";
            echo "<h3>Message Details:</h3>";
            echo "<p>" . $mail['message'] . "</p><br><br>";
            echo "<form action='index.php?page=messages' method='post'>";
            echo "<input type='hidden' name='id' value='" . $mail['id'] . "'>"; 
            echo "<input type='submit' name='reply' value='reply' class='btn'>";
            echo "<input type='submit' name='viewed' value='unread' class='btn'>";
            echo "<input type='submit' name='delete_mail' value='delete' class='btn'>";
            echo "</form>";
            
            // set it to read so long as the user has not clicked to mark as unread and read status is unread
            // this stops unnecessary updates to the database that doesnt change any data. 
            if (($mail['viewed'] == 0) && (!isset($_POST['viewed']))){
                $db->update("fh_messages", ["viewed" => 1], "id='" . $mail['id'] . "'");
            }
        }else{
            echo "<h2>No message found</h2>";
        }
        
    }
    if (isset($_POST['reply'])){
        echo "<form method='post' action='index.php?page=messages'>";
        echo "<input type='hidden' name='id' value='" . $_POST['id'] . "'>";
        $message = $db->getRow("fh_messages", "id=" . $_POST['id']);
        if ($message){
                $mailUser = $db->getRow("fh_users", "id='" .  $message['sender_id']. "'");
                if ($mailUser){
                    echo "Sending to: " . $mailUser['username'] . "<br>";
                    echo "<input type='hidden' name='recipientID' value='" . $message['sender_id'] . "'>";
                    echo "<input type='hidden' name='subject' value='" . $message['subject'] . "'>";
                    echo "Subject: " . $message['subject'] . "<br>";
                }
        }else{
            // check for advert id
            $advert = $db->getRow("fh_adverts", "id=" . $_POST['id']);
            if ($advert){
                $mailUser = $db->getRow("fh_users", "id='" . $advert['userID'] . "'");
                if ($mailUser){
                    echo "<input type='hidden' name='recipientID' value='" . $advert['userID'] . "'>";
                    echo "Sending to: " . $mailUser['username'] . "<br>";
                    echo "Subject: <input type='text' name='subject' value='" . $advert['name'] . "'><br>";
                } 
            }
        }
        echo "<textarea id='message_box' name='message_box' class='textarea'></textarea><br>";
        echo "<input name='sendMail' class='btn' value='Send Message' type='submit'>";
        echo "<input class='btn' value='Cancel' type='submit' name='cancelBtn'>";
        echo "</form>";
    }
    
    // send email
    if (isset($_POST['sendMail'])){
        // display variabled
        // sender
         $mailData['user_id'] = strip_tags($_POST['recipientID']); // alernate the values
         $mailData['sender_id'] = $uid; // alternate the values
         $mailData['mailbox'] = 1; // 1 or 2 // alernate the values
         $mailData['item_id'] = strip_tags($_POST['id']); // This needs passing 
         $mailData['subject'] = strip_tags($_POST['subject']);
         $mailData['message'] = strip_tags($_POST['message_box']);
         $mailData['viewed'] = 0;
         $mailData['deleted'] = 0;
         $mailData['ip_address'] = $_SERVER['REMOTE_ADDR'];
         $mailData['timestamp'] = time();
         
         $mailboxIN = $db->insert("fh_messages", $mailData);
         
         // now switch the data
         $mailData['mailbox'] = 2; // 1 or 2 // alernate the values
         
         $mailboxOUT = $db->insert("fh_messages", $mailData);
         
         if ($mailboxIN && $mailboxOUT){
             echo "<h2>Email sent</h2>";
             echo "Your message has been sent to the recipient";
         }else{
             echo "<h2>Error: Message sending failed</h2>";
             echo "We regret we were unable to send your message at this time, please try again later";
         }
    }
    echo "</div>";
    echo "</div>";
    
    
    
?>
