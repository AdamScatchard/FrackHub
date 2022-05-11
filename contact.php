<?php
    echo "<h2>Contact Us</h2>";
    echo "<hr>";
    echo "<div id='contactContainer' class='contactContainer'>";
    echo "<h3>At Frackhub, we care about your experience</h3>";
    echo "<p>Here is how you can get in touch...</p>";
    if (isset($_POST['sendBtn'])){
        foreach ($_POST as $key => $value){
            // switch (select) statement will allow us to code exceptions
            // without filling the script with if statements
            switch ($key){
                case "sendBtn":
                    // do nothing, we dont need to save the user clicking Send Email
                    break;
                default:
                    // anything else should be saved
                    $database_values[$key] = $db->cleanSQLInjection(strip_tags($value));
            }
        }
        // Extras for administrative and security purposes
        $database_values['ip'] = $_SERVER['REMOTE_ADDR'];
        $database_values['timestamp'] = time();
                $saved = $db->insert("fh_contactus", $database_values);    
        ($saved==true) ? emailSaved() : emailFailed();
    }else{
        showform();
    }
    echo "</div>";



    function showform(){
        echo "<div id='contactForm'>";
        echo "<form name='contactUsFrm' id='contactUsfrm' method='post' action='index.php?page=contact'>"; 
        echo "<input type='text' name='name' placeholder='Name'>";
        echo "<input type='email' name='email' placeholder='E-Mail'><br>";
        echo "<input type='number' name='phone' placeholder='Phone Number'>";
        echo "<input type='text' name='subject' placeholder='Subject'><br>";
        echo "<textarea class='textarea' name='message' placeholder='Your email contents'></textarea><br>";
        echo "<input  class='btn' type=submit value='Send Email' name='sendBtn'>";
        echo "</form>";
        echo "</div>";

        echo "<div id='contactAddress'>";
        echo "<h3>FrackHub</h3>";
        echo "<hr>";
        echo "Wolverhampton University<br>";
        echo "Wulfruna Street<br>";
        echo "Wolverhampton<br>";
        echo "West Midlands<br>";
        echo "England<br><br>";
        echo "WV1 1LY<br><br>";
        echo "<a href='Tel:01902555555'>01902 555 555</a>";

        echo "</div>";
    }

    function emailsaved(){
        echo "<h3>We have received your email!</h3>";
        echo "<p>Due to large volume of emails it may not be possible to respond to all<br>";
        echo "Please allow up to 2-3 working weeks for a response</p>";
        echo "<p>If you have not heard anything beyond this time, please send us another email<br>";
        echo "or reach out to us via our direct contact details via phone or in person</p>.";
        echo "<hr>";
        echo "<p>Disclaimer: In the event you found this website via the internet, it is important you<br>";
        echo "be aware this is a university assignment, and thus not a real business</p>";
    }
    
    function emailfailed(){
        echo "<h2>There was a problem sending your email</h2>";
        showform();
    }
?>
