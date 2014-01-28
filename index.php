<?php
	
	include('microBoatDB.class.php');
	
	$db = new microBoatDB('localhost', 'databaseinator', 'root', 'usbw');
	
	$array = array(
		array(":id1", 12),
		array(":id2", 13),
		array(":id3", 14)
	);
	$query = $db->query("SELECT * FROM `leerlingen` WHERE `id` IN(:id1, :id2, :id3)", $array);
	
	echo '<pre>'. print_r($query->fetchAll(),true) .'</pre>';
	
?>