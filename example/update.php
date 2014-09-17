<?php
	
	include('microBoatDB.class.php');
	
	//See "./example/microboatdb_example.sql" -> Import this in your MySQL database in order to use the underlying examples.
	$db = new microBoatDB('localhost', 'microboatdb_example', 'root', 'usbw');
	$db->setDebugMode(true);
	
	//update single record
	//$db->table->update($id, $data);
	$id1 = $db->peaple->update(1, 'name=klaas,sirname=pipo');
	$id2 = $db->peaple->update(2, array('name'=>'kees', 'sirname'=>'vogel'));
	
	//update multiple records
	//$db->table->update($dataArry);
	
	$array = array(
		3 => array('name'=>'jan', 'sirname'=>'hond'),
		4 => array('name'=>'klaas', 'sirname'=>'vos')
	);
	$id3 = $db->peaple->update($array);
	
	$array = array(
		5 => 'name=kees,sirname=vogel',
		6 => 'name=kantje,sirname=boort'
	);
	$id4 = $db->peaple->update($array);
	
	echo '<b>test</b><pre>'. print_r($id1,true) .'</pre><hr>';
	echo '<b>test</b><pre>'. print_r($id2,true) .'</pre><hr>';
	echo '<b>test</b><pre>'. print_r($id3,true) .'</pre><hr>';
	echo '<b>test</b><pre>'. print_r($id4,true) .'</pre><hr>';
	
?>