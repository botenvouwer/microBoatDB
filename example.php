<?php
	
	#-> Include the database class
	include('microBoatDB.class.php');
	
	#-> Initiate and connect
	$db = new microBoatDB('localhost:3306', 'test', 'root', 'usbw');
	$db->setDebugMode(true);
	
    #-> New method for easy html creation (just try it is fun)
	echo $db->lijsten->loop('Name: [NAAM] - [MAX] <br>');
	echo '<br>';
	
	#-> Smart query method with diffrent modes
	/*
		microBoatDB::query($query [, $params]);
		microBoatDB::query($query [, $tag, $value [, $type]]);
		
		$query: A old fasion query string
		
		mode 1
		$params: An single array or a multidimentional array with query parameters
		
			$params = array(':tag', 2, 'int');
			
			or
			
			$params = array(
				array(':tag1', 2), //auto's to int
				array(':tag2', 'bla'), //auto's to string
				array(':tag3', '32434', 'int') //force int
			);
			
		mode 2
		$tag: the prepare tag
		$value: the value
		$type: the value type
		 
	*/
		
	//Just a casual query
	$query = $db->query("SELECT * FROM `lijsten`");
	
	//A prepared query with one prepared value
	$query = $db->query("SELECT * FROM `lijsten` WHERE `PK_LIJSTEN` = :id", ':id', $_REQUEST['userinput']); //can also force userinput to be prepared ass int by adding int ass 4rth param
	
	//A prepared query with multiple prepared values
	$array = array(
		array(':name1', $_REQUEST['userinput1']),
		array(':name2', $_REQUEST['userinput2']),
		array(':name3', $_REQUEST['userinput3'])
	);
	$query = $db->query("SELECT * FROM `lijsten` WHERE `NAAM` IN(:name1, :name2, :name3)", $array);
	
	#-> Easy update and instert method
	
	//insert
	$db->update('tablename', $values);
	
	//update
	$db->update('tablename', $values, $id);
	
?>