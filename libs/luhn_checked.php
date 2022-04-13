<?php
    // Coded by Adam Mackay 2000418
    // Luhn algorithm checker
    // one is done by JS but also another is carried out
    // backend (PHP) to ensure that developer tools is not use
    // data is not stored (and should not be modified to store data)
    
    function checkLuhnCardNumber($cardNumber){
        if (is_int(intval($cardNumber))){
            // is integer
            if (intval($cardNumber) % 10==0){
                echo "<h1>3</h1>";
                return false;
            }
            if (strlen($cardNumber) >= 14){
                // is 14 digits or more
                $sum = 0;
                $longNumber = str_split($cardNumber);
                $checkDigit = intval(array_pop($longNumber));
                for($i = 0; $i < count($longNumber); $i++){
                    if ($i % 2 == 0){
                        //even
                        $x = intval($longNumber[$i]) * 2;
                        if (strlen($x) == 2){
                            $val = intval(substr($x, 0, 1)) + intval(substr($x, 1,2));
                        }else{
                            $val = intval($x);
                        }
                    }else{
                        // odd
                        $val = intval($longNumber[$i]);
                        
                    }
                    $sum += $val;
                }
                $sum+= $checkDigit;
                if ($sum % 10 == 0){
                    return true;
                }
                
            }else{
                echo "<h1>2</h1>";
            }
        }else{
            echo "<h1>1</h1>";
        }
        // where not meeting the criteria return false
        return false;
        
    }
?>
