<?php 

	// Adam Mackay's work

	include ("page_init.php");

	//Kieran C's Work

	require_once($inc_dir . 'head_section.php');

	

	include ($inc_dir . 'menu.php');



	include ($visiting_page);

	

	include($inc_dir . 'footer.php');

	$db->disconnect();

	

?>
