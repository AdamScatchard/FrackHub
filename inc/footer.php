<?php
    echo "</main>";
	echo "<footer>";
    echo "<table id='desktopFooter'>";
    echo "<tr>";
    echo "<td>";
    
    echo "Frackhub<br>";
    echo "Wolverhampton University<br>";
    echo "Wulfruna Street<br>";
    echo "Wolverhampton<br>";
    echo "England<br>";
    echo "WV1 1LY";
    echo "</td>";
    echo "<td>";
    echo "<ul class='nobullets'>";
    echo "<li>Site Map</li>";
    echo "<li>About Us</li>";
    echo "<li>Press</li>";
    echo "<li>Career Opportunities</li>";
    echo "<li>Contact Us</li>";

    echo "</ul>";
    echo "</td>";
    echo "<td>";
    echo "Company Number: 2661891728";
    echo "</td>";
    echo "</tr>";
    echo "</table>";
	echo "FrackHub Collaborative Development 5CS024 &copy;Copyright all rights reserved " . date('Y') . "";
	echo "</footer>";
	// script to make the animated menu in mobile view
	echo "<script>";
	echo "function myFunction(x) {";
    echo "  x.classList.toggle('change');";
    echo "  el = document.getElementById('mobileSearchBar');";
    echo "  el2 = document.getElementById('mobileMenuBts');";
    echo "  if (el.style.display === 'none') {";
    echo "       el.style.display = 'inline-block';";
    echo "       el2.style.display = 'none';";
    echo "  } else {";
    echo "       el.style.display = 'none';";
    echo "       el2.style.display = 'inline-block';";
    echo "  }";
    echo "}";
	echo "</script>";
	echo "</html>";
?>
