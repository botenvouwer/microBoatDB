<?php
	
	include('database.class.php');
	
	$db = new microBoatDB('localhost:3306', 'test', 'root', 'usbw');
	$db->setDebugMode(true);
	
    echo $db->lijsten->loop('test [naam] <br>');
	
	
?>