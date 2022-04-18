<?php
// code by Adam Mackay
class emailer{

    function send_email($template_name = null, $user_data = null, $htmlMail = true){
        switch(strtolower($template_name)){
            case "registration":
                $subject = "Registration to Frackhub Services";
                $html = $this->registration($user_data);
                break;
            case "forgotten password":
                $subject = "Forgotten Password";
                $html = "No forgotten password template producted";
                break;
            case "closed account":
                $subject = "Closed account";
                $html = "no closed account template produced";
                break;
            case "locked account":
                $subject = "Locked out";
                $html = "no locked account template produced";
                break;
            case "unread mail":
                $subject = "Unread mail";
                $html = "you have email template not produced";
                break;
            case "credits":
                $html = "no credit or statement template produced";
                break;
            case "statement":
                $html = "no statement tempalte produced";
                break;
            default:
                return false;
        }
        $html_email = $this->html_businessHeader();
        $html_email .= $html;
        $html_email .= $this->html_businessFooter();
        
        $this->send_webmail($user_data['email'], $subject, $html_email);
    }
function send_webmail($to, $subject, $message){
        // basic email with no html
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
 
        // Create email headers
        $headers .= "From: no-reply@frackhub.co.uk\r\n".
        'Reply-To: "no-reply@frackhub.co.uk"' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();
        try{
            mail($to,$subject,$message,$headers);
        }catch(exception $e){
            echo "Error";
            die;
        }
    }
    
    
    function registration($user_data){
        $returnString = "";
        $returnString .= "<h1>Welcome to FrackHub " . $user_data['name'] . "</h1>";
        $returnString .= "<br>";        
        $returnString .= "<p>Your free membership has begun, all that is required from you is to verify your email address<br>";
        $returnString .= "click following link to  <a href='https://frackhub.000webhostapp.com/index.php?page=register&linkverify=" . $user_data['verification'] . "'>verify your email address</a>";
        $returnString .="<br><br>";
        $returnString .= "<h2>Important information:<h2>";
        $returnString .= "<p>We ask all members to adhere to the community guidelines and the advertising policies.<br>";
        $returnString .= "You can always contact loaners via the advert email button<br>";
        $returnString .= "We are always happy to hear from you, in the event you feel the need to ask for help<br>";
        $returnString .= "or support, simply click on contact us from the menu once you have logged in</p>";
        $returnString .= "<br>";
        return $returnString;
    }
    
    
    function html_businessHeader(){
        // HTML Header
        $returnString = "<html><header>";
        $returnString .= "<div id='logo'>";
        $returnString .= "<img alt='Frackhub services' src='img/logo.png' class='email_logo'>";
        $returnString .= "</header>";
        $returnString .= "<hr>";
        return $returnString;
    }
    
    
    function html_businessFooter(){
        $returnString = "<footer>&copy;Copyright Frackhub Services Ltd " . date('Y') . "</footer></html>";
        return $returnString;
    }
}

?>
