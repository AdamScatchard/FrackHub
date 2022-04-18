<?php
   class encryption{
        
        //Private palintext variable.
        private $plaintext;
        private $plaintextArray;
        private $arrayConvert;
        public $errorMessage;
        private $privateKey;
        private $caesarConvert;
        private $hexConvert;
        private $hexScramble;
        private $hexScramble2;
        private $encodeString;
        private $encodeString2;
    
        public function resetValues(){
            $this->plaintext = NULL;
            $this->plaintextArray = NULL;
            $this->arrayConvert = NULL;
            $this->errorMessage = NULL;
            // ignore resetting private key
            $this->caesarConvert = NULL;
            $this->hexConvert = NULL;
            $this->hexScramble = NULL;
            $this->hexScramble2 = NULL;
            $this->encodeString = NULL;
            $this->encodeString2 = NULL;
            
        } 
        // testing and debugging getters
        public function getPlainText(){
            return $this->plaintext;
        }
        
        public function getPrivateKey(){
            return $this->privateKey;
        }
        
        
        //Public function setting value for plaintext.
        public function setPlainText($plaintext){
            // reset all values, incase obj ran twice
            $this->resetValues();
            $this->plaintext = $plaintext;
        }

        //Function to run the class functions.
        public function classRun(){
            $output = $this->stringSplit();
            $output = $this->asciiConvert($output);
            $output = $this->ceaserCipher($output);
            $output = $this->hexConversion($output);
            $output = $this->arrayScramble($output);
            $output = $this->encodeArray($output);
            return $output;
        }
        
        public function setKey($key){
            if (is_int($key)){
                $this->privateKey = $key;
            }else{
                $this->privateKey = 7; // default
            }
        }
        
        //Private function splitting the plaintext string.
        private function stringSplit(){
            $plaintextArray = str_split($this->plaintext);
            $this->plaintextArray = $plaintextArray;
            return $plaintextArray;
        }

        //Function to convert plaintext into ascii.
        private function asciiConvert(){
            if (is_array($this->plaintextArray)){
                foreach ($this->plaintextArray as $key=>$value){
                    $arrayConvert[$key] = ord($value);
                }
                
                $this->arrayConvert = $arrayConvert;
                return $arrayConvert;
            }
            else{
                $this->errorMessage = "There has been an error within function 'asciiConvert'.";
            }
        }

        //Function to convert values using the Caesar Cipher.
        private function ceaserCipher(){
            foreach ($this->arrayConvert as $key=>$value){
                $ascii = ($value + ($this->privateKey)) % 255;
                $caesarConvert[$key]= $ascii;
            }
            
            $this->caesarConvert = $caesarConvert;
            return $caesarConvert;
            
        }

        //Function to convert new 'caesarConvert' values into Hexadecimal values. 
        private function hexConversion(){
            foreach ($this->caesarConvert as $key=>$value){
                $hexConvert[$key] = dechex($value);
            }
            
            $this->hexConvert = $hexConvert;
            return $hexConvert;
        }

        //Function to scramble the order of the values within the array.
        private function arrayScramble(){
            foreach ($this->hexConvert as $key=>$value){
                $hexScramble[$key] = strrev($value);
            }

            $this->hexScramble = $hexScramble;

            $hexScramble2 = array_reverse($hexScramble);
            $this->hexScramble2 = $hexScramble2;

            return $hexScramble2;
        }

        //Function to encode array into MD5.
        private function encodeArray(){
            $encodeString = implode("", $this->hexScramble2);

            $encodeString2 = md5($encodeString);
            return $encodeString2;
        }
    }

?>
