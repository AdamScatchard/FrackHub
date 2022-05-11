<?php
    if (isset($_POST['ip'])){
        // block ip
    }
    permission_check("admin_contactUs");
    echo "<h2>Contact Reply:</h2>";
    echo "<hr>";
    echo "<div class='contactContainer'>";  
    echo "<h3>Reply: </h3>";
    if (isset($_GET['id'])){
        $id = $_GET['id'];
        $result = $db->getRow("fh_contactus", "id=" . $db->cleanSQLInjection($id));
        if ($result['viewed']==0){
            $db->update("fh_contactus", ["viewed"=>1], "id=" . $db->cleanSQLInjection($id));
        }
        if (isset($_POST['send'])){
            include ($lib_dir . "email_class.php");
            $e_mail = new emailer();
            if ($user_data){
                $e_mail->send_email("sendmail", $result, true, $db->cleanSQLInjection($_POST['email']) );
            }else{
                echo "<h1>Error</h1>";
            }
          
        }
        
        echo "<form method='post'>";
        echo "<textarea name='email' class='textarea'>";
        echo "\n\n\n\n\n\n\n";
        echo "<" . $result['name'] . "> " . $result['email'] . "\n";
        echo "time/date: " . date("h:i:s d/m/Y", $result['timestamp']) . "\n";
        echo "Subject: ". $result['subject'] . "\n";
        echo $result['message'];
        echo "\n\nContact Number: " . $result['phone'];
        echo "</textarea><br>";
        echo "Phone number: " . $result['phone'] . "<br>";
        echo "<input type='submit' value='Send' name='send' class='btn'>";
        echo "<input type='submit' value='Block IP' name='ip' class='btn'>";
        echo "</form>";
        echo "</div>";
    }else{
        echo "<h2>Error</h2>";
        echo "<p>Unable to retrieve details of this email.</p>";
    }
?>
