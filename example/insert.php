<?php
	
	//pseudo code:
	include('microBoatDB.class.php');
	
	//See "./example/microboatdb_example.sql" -> Import this in your MySQL database in order to use the underlying examples.
	$db = new microBoatDB('localhost', 'microboatdb_example', 'root', 'usbw'); //fill in the dbadres, dbname, dbuser and dbpass 
	$db->setDebugMode(true);
	
	//insert examples
	$id1 = $db->peaple->insert('name=jan,sirname=test,male=1,birthday=1994-04-05,fk_job=0');
	
	$id2 = $db->peaple->insert(array('name'=>'jan','sirname'=>'test2','male'=>0,'birthday'=>'1994-04-05','fk_job'=>1,));
	
	$collumns = array('name', 'sirname', 'male', 'birthday', 'fk_job');
	$data = array(
		array('jan1', 'test', 0, '1994-04-05', 2),
		array('jantine', 'test', 1, '1994-04-05', 4),
		array('jan2', 'test', 0, '1994-04-05', 1)
	);
	
	$id3 = $db->peaple->insert($collumns, $data);
	
	echo '<b>Single string shorthand insert</b><pre>'. print_r($id1,true) .'</pre><hr>';
	echo '<b>Single rocord insert</b><pre>'. print_r($id2,true) .'</pre><hr>';
	echo '<b>Muliple record insert</b><pre>'. print_r($id3,true) .'</pre><hr>';
	
?>