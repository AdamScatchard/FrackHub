<?php
	echo "<!DOCTYPE html>";
	echo "<html lang='en'>";
	echo "<head>";
    echo "<meta name='viewport' content='width=device-width, initial-scale=1'>";
		// modified by adam mackay as it should have $css_dir define the css path.
	// in the future the HTML title tag will contain data to show the page title.
	echo "<link rel=\"stylesheet\" href=\"" . $css_dir . "page_formatting.css\">";
	echo "<meta charset=\"UTF-8\">";

	// page title needs adding here
	if (isset($uid)){
    	if ($uid > 0){
    	    if (isset($_GET['page'])){
        	    switch(strtolower($_GET['page'])){
        	        case "registration":
        	            $title = "Frackhub: Registration";
        	            break;
        	         case "account":
        	             $title = "Frackhub: Account details";
        	             break;
                     case "logout":
                         $title = "Frackhub: Logged out";
                         break;
                    default:
                        $title = "Welcome back " . $user['name'] . " to FrackHub";
                        break;
        	    }
    	    }else{
                $title = "Welcome to Frackhub";
    	    }
        	echo "<title>" .$title ."</title>";
    	}else{
    	    echo "<title>Welcome to Frackhub</title>";
    	}
	}else{
    	    echo "<title>Welcome to Frackhub</title>";
	}
	echo "</head>";
	echo "<body>";
?>
