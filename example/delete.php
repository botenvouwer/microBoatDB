<?php
	
	//pseudo code:
	include('microBoatDB.class.php');
	
	//See "./example/microboatdb_example.sql" -> Import this in your MySQL database in order to use the underlying examples.
	$db = new microBoatDB('localhost', 'microboatdb_example', 'root', 'usbw'); //fill in the dbadres, dbname, dbuser and dbpass 
	$db->setDebugMode(true);
	
	//delete examples:
	
	//$db->tableName->delete(primary key)
	$db->peaple->delete(1);
	$db->peaple->delete('1,2,3');
	$db->peaple->delete(array(1,2,3));
	
?>