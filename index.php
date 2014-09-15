<?php
	
	include('microBoatDB.class.php');
	
	//See "./example/microboatdb_example.sql" -> Import this in your MySQL database in order to use the underlying examples.
	$db = new microBoatDB('localhost', 'microboatdb_example', 'root', 'usbw');
	
	
	//$query = $db->query('SELECT * FROM `peaple` WHERE `id` = :param', ':param', 1);
	//$query = $db->peaple->get('*');
	$query = $db->peaple->infoColmns();
	
	echo '<pre>'. print_r($query,true) .'</pre>';
	
?>