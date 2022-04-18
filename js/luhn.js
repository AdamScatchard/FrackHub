// luhn Algorithm
// coded by Adam Mackay
// research using: https://www.youtube.com/watch?v=PNXXqzU4YnM, 
// Student id: 2000418

// last digit = Check Digit
// Even Digits (multiplied by 2 and added together if result is 10+)
// add numbers together including the checkDigit
// perform modulous 10.

function validateCard(strCardNumber){
	if (isNaN(strCardNumber)){
		console.log ("Card number contains non-numerical characters");
		return false;
	}else{
	    if ((parseInt(strCardNumber) % 10)==0){
	        return false;
	    }
	    if(strCardNumber.length < 14){
	        return false;
	    }
	}
	sum = 0;
	longNumber = strCardNumber.split('');
	chkDigit = parseInt(longNumber.pop());
	for (i = 0; i < longNumber.length; i++){
		if (i % 2 == 0){
			evens = (parseInt(longNumber[i]) * 2);
			if (evens >= 10){
				doubleDigits = evens.toString().split("");
				x = parseInt(doubleDigits[0]) + parseInt(doubleDigits[1]);
			}else{
				x = evens;
			}
		}else{
			x = longNumber[i];
		}
		sum+= parseInt(x);
	}
	sum+=chkDigit;
	if (sum % 10 == 0){
		return true;
	}
	return false;
}
