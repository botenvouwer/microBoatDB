<?php
	
	//pseudo code:
	include('microBoatDB.class.php');
	
	//See "./example/microboatdb_example.sql" -> Import this in your MySQL database in order to use the underlying examples.
	$db = new microBoatDB('localhost', 'microboatdb_example', 'root', 'usbw'); //fill in the dbadres, dbname, dbuser and dbpass 
	$db->setDebugMode(true);
	
	//select examples:
	
	$query0 = $db->query('SELECT * FROM `peaple` WHERE `pk_peaple` = 1');
	
	$query00 = $db->query('SELECT * FROM `peaple` WHERE `pk_peaple` = 1', NULL);
	
	//select with prepared statements examples:
	
	$query1 = $db->query('SELECT * FROM `peaple` WHERE `pk_peaple` = :param', ':param', 1);
	
	$query2 = $db->query('SELECT * FROM `peaple` WHERE `pk_peaple` = :param', 1); //When using shorthand the param name is always :param
	
	$query3 = $db->query('SELECT * FROM `peaple` WHERE `pk_peaple` = :param', array(':param', 1, 'int'));
	
	$params = array(
		array(':param1', 1, 'int'),
		array(':param2', 2, 'int'),
		array(':param3', 3) // when type is not defined it will be interpreted as a string
	);
	$query4 = $db->query('SELECT * FROM `peaple` WHERE `pk_peaple` IN (:param1, :param2, :param3)', $params);
	
	//Select one specific value
	$query5 = $db->one('SELECT `name` FROM `peaple` WHERE `pk_peaple` = 1');
	
	$query6 = $db->one('SELECT `name` FROM `peaple` WHERE `pk_peaple` = :param', 1);

	//output
	echo '<b>Default query</b><pre>'. print_r($query0->fetchAll(),true) .'</pre><hr>';
	echo '<b>Default internal query</b><pre>'. print_r($query00->fetchAll(),true) .'</pre><hr>';
	echo '<b>Single prepared statement query</b><pre>'. print_r($query1->fetchAll(),true) .'</pre><hr>';
	echo '<b>Shorthand single prepared statement query</b><pre>'. print_r($query2->fetchAll(),true) .'</pre><hr>';
	echo '<b>Array single prepared statement</b><pre>'. print_r($query3->fetchAll(),true) .'</pre><hr>';
	echo '<b>Multiple prepared statements</b><pre>'. print_r($query4->fetchAll(),true) .'</pre><hr>';
	echo '<b>Select one specific value</b><pre>'. $query5 .'</pre><hr>';
	echo '<b>Select one specific value with prepared statement</b><pre>'. $query6 .'</pre><hr>';
	
?>