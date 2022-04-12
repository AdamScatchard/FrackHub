// luhn Algorithm
// coded by Adam Mackay
// research using: https://www.youtube.com/watch?v=PNXXqzU4YnM, 
// Student id: 2000418

// last digit = Check Digit
// Even Digits (multiplied by 2 and added together if result is 10+)
// add numbers together including the checkDigit
// perform modulous 10.

function validateCard(strCardNumber){
  // check the card number is solely numbers.
	if (isNaN(strCardNumber)){
		console.log ("Card number contains non-numerical characters");
		return false;
	}
  // initiate integer variable sum
	sum = 0;
  // split the card number into an array called longNumber
	longNumber = strCardNumber.split('');
  // Capture the last digit of the array as the check digit and remove it
  // from the original array (longNumber)
	chkDigit = parseInt(longNumber.pop());
  
  // itenerate the longNumber array items
	for (i = 0; i < longNumber.length; i++){
    // if i is an even nth position then double the digit and (add the digits together if
    // the value is greater or equals to 10), otherwise simply keep variable x as the single digit
		if (i % 2 == 0){
			evens = (parseInt(longNumber[i]) * 2);
			if (evens >= 10){
				doubleDigits = evens.toString().split("");
				x = parseInt(doubleDigits[0]) + parseInt(doubleDigits[1]);
			}else{
				x = evens;
			}
		}else{
      // not an even nth position, simply copy the value to variable x
			x = longNumber[i];
		}
    // sum up the value of variable sum with the new value of x
		sum+= parseInt(x);
	}
  // add the chkDigit value to the sum 
	sum+=chkDigit;
  // if divisible by 10 then the card passes luhn algorithm
	if (sum % 10 == 0){
		return true;
	}
  // otherwise the card number doesnt check out
	return false;
}
