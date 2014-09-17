<?php
	
	include('microBoatDB.class.php');
	
	//See "./example/microboatdb_example.sql" -> Import this in your MySQL database in order to use the underlying examples.
	$db = new microBoatDB('localhost', 'microboatdb_example', 'root', 'usbw');
	$db->setDebugMode(true);
	
	$q = $db->query("SELECT * FROM `peaple`");
	
	echo '<b>test</b><pre>'. print_r($q->fetch(),true) .'</pre><hr>';
	
	
?>