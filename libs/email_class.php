<?php
// code by Adam Mackay
class mail{
    function template_email($recipient, $type = "registration", $format = "basic", $uid = 0){
        switch(strtolower($type)){
            case "confirmation":
                break;
            case "otp":
                break;
            case "resetpwd":
                break;
            case "ban":
                break;
            case "lockedout":
                break;
            case "unknownlocation":
                break;
            case "statement":
                break;
            case "cancel":
                break;
            default:
                return false;
        }
        switch(strtolower($format)){
            case "basic":
                $this->send_text($to, $subject, $message);
                break;
            case "html":
                $this->send_html($to, $subject, $message);
                break;
            default:
                $this->send_text($to, $subject, $message);
                break;
        }
    }
    
    private function send_text($to, $subject, $message){
        // basic email with no html
        $headers = "From: no-reply@frachub.co.uk";
        mail($to,$subject,$txt,$headers);
    }
    
    private function send_html(){
        $to = "somebody@example.com, somebodyelse@example.com";
        $subject = "HTML email";
        
        $message = "
        <html>
        <head>
        <title>HTML email</title>
        </head>
        <body>
        <p>This email contains HTML Tags!</p>
        <table>
        <tr>
        <th>Firstname</th>
        <th>Lastname</th>
        </tr>
        <tr>
        <td>John</td>
        <td>Doe</td>
        </tr>
        </table>
        </body>
        </html>
        ";
        
        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        
        // More headers
        $headers .= 'From: <webmaster@example.com>' . "\r\n";
        $headers .= 'Cc: myboss@example.com' . "\r\n";
        
        mail($to,$subject,$message,$headers);
    }
    
    private function new_user(){
    
    }
    
    private function password_reset(){
    
    }
    
    private function ban(){
    
    }
    
    function communication(){
    
    }
    
    private function x(){
    
    }
}
?>
