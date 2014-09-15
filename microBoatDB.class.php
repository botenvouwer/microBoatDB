<?php
	
	/*
		
		version 0.0.4
	
		william © botenvouwer - microBoatDB class
		
		class made for easy database interaction.
	
		todo:
		
			-	update function
			-	delete function
			-	innerjoin automaticly
			-   primary key mode
		
		solution  for inner join:
			//relatie info
			SELECT * FROM information_schema.KEY_COLUMN_USAGE
			//relatie info over specefike tabel
			SELECT * FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_NAME = 'leerlingen' AND REFERENCED_TABLE_NAME IS NOT NULL
			//NODIG VOOR INNERJOIN BOUWEN
			SELECT COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_NAME = 'leerlingen' AND REFERENCED_TABLE_NAME IS NOT NULL
	
	*/
	
	//Database class
	class microBoatDB extends PDO{
		
		private $debugMode = false;
		private $objectMode = true;
		public $lastQuery = 'No Queries made yet';
		private $primaryKeyPrefixMode = true;
		public $primaryKeyPrefixName = 'pk_';
		
		public function __construct($server = '', $dbname = '', $user = '', $pass = ''){
			
			parent::__construct('mysql:dbname='.$dbname.';host='.$server, $user, $pass);
			
			$this->setConf();	
			$this->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
			
			//vervang voor list tables
			$query = $this->query("SHOW TABLES");
			$this->tabels = $query->fetchAll();
			
			$colmname = 'Tables_in_'.$dbname;
			foreach($this->tabels as $key => $object){
				$ntc = $object->$colmname;
				$this->$ntc = new microBoatDBTable($this, $object->$colmname);
			}
			
		}
		
		protected function setConf(){
			
			if($this->debugMode){
				$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
			else{
				$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
			}
			
			if($this->objectMode){
				$this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
			}
			else{
				$this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
			}
		}
		
		public function setDebugMode($bool){
			if(!is_bool($bool)){
				 throw new Exception('Attribute must be bool');
			}
			$this->debugMode = $bool;
			$this->setConf();
		}
		
		public function setObjectMode($bool){
			if(!is_bool($bool)){
				 throw new Exception('Attribute must be bool');
			}
			$this->objectMode = $bool;
			$this->setConf();
		}
		
		public function setPrimaryKeyPrefixMode($bool){
			if(!is_bool($bool)){
				 throw new Exception('Attribute must be bool');
			}
			$this->primaryKeyPrefixMode = $bool;
			$this->setConf();
		}
		
		public function query(){
			
			$num = func_num_args();
			$arg = func_get_args();
			
			if($num == 1 || $num == 2 && $arg[1] === NULL){
				$this->lastQuery = $arg[0];
				return parent::query($arg[0]);
			}
			else if($num > 1){
				
				if($num == 2){
					if(is_array($arg[1])){
						$param = $arg[1];
						if(!is_array($arg[1][0])){
							$param = array($param);
						}
					}
					else if(is_string($arg[1])){
						$param = array(array(':param', $arg[1], 'str'));
					}
					else if(is_int($arg[1])){
						$param = array(array(':param', $arg[1], 'int'));
					}
					else{
						throw new Exception('Second parameter must be an array or use single tag mode');
					}
				}
				else if($num == 3 || $num == 4){
					$param = array($arg[1], $arg[2]);
					if($num == 4){
						$param[2] = $arg[3];
					}
					$param = array($param);
				}
				else{
					throw new Exception('No support for 5th argument');
				}
				
				$query = $this->prepare($arg[0]);
				foreach($param as $par){
					if(!isset($par[2])){
						$par[2] = 'str';
					}
					switch($par[2]){
						case 'int':
							$query->bindParam($par[0], $par[1], PDO::PARAM_INT);
							break;
						case 'str':
							$query->bindParam($par[0], $par[1], PDO::PARAM_STR);
							break;
						case 'blob':
							$query->bindParam($par[0], $par[1], PDO::PARAM_LOB);
							break;
						default:
							$query->bindParam($par[0], $par[1], PDO::PARAM_STR);
							break;
					}
				}
				$query->execute();
				$this->lastQuery = $arg[0];
				return $query;
				
			}
			else{
				throw new Exception('Can\'t query withoud a query!');
			}
		}
		
		public function one($query, $param = NULL){
			$query = $this->query($query, $param);
			$query = $query->fetchColumn();
			return $query;
		}
		
		public function conInfo()
		{
			$array = array(
				'server' => $this->getAttribute(PDO::ATTR_SERVER_INFO),
				'server version' => $this->getAttribute(PDO::ATTR_SERVER_VERSION),
				'client version' => $this->getAttribute(PDO::ATTR_CLIENT_VERSION),
				'driver' => $this->getAttribute(PDO::ATTR_DRIVER_NAME),
				'connection' => $this->getAttribute(PDO::ATTR_CONNECTION_STATUS)
			);
			
			return $array;
		}
		
	}
	
	class microBoatDBTable{
		
		private $name = '';
		private $onemode = true;
		protected $db = null;
		public $result = null;
		public $count = 0;
		public $lastQuery;
		
		public function __construct($db, $name){
			$this->name = $name;
			$this->db = $db;
			$this->lastQuery = 'No Queries made yet on table: '.$this->name;
		}
		
		public function getPrimaryKeyName(){
			if($db->primaryKeyPrefixMode){
				return $db->primaryKeyPrefixName.$this->name;
			}
			else{
				return $db->primaryKeyPrefixName;
			}
		}
		
		public function count($filter = '', $params = NULL){
			$filter = ($filter ? 'WHERE '.$filter : '');
			return $this->db->one("SELECT COUNT(*) FROM `$this->name` $filter", $params);
		}
		
		public function get($id = null, $columns = null, $order = null, $filer = null){
			
			$parram = array();
			
			$onemode1 = false;
			$onemode2 = false;
			if(is_numeric($id)){
				$pkn = $this->getPrimaryKeyName();
				$where = "WHERE `$pkn` = :id";
				$parram[] = array(':id', $id, 'int');
				$onemode1 = true;
			}
			else if($id == '*'){
				$where = '';
			}
			else if(is_string($id) && $id){
			
				$list = explode(',', $id);
				
				$id = '';
				foreach($list as $key => $value){
					$parram[] = array(":id_$key", $value, 'int');
					$list[$key] = ":id_$key";
				}
				
				$id = implode(',', $list);
				
				$where = "WHERE `pk_$this->name` IN ($id)";
			}
			else if(!$id){
				$where = '';
			}
			else{
				throw new Exception('id can be number(id of field), array(list of id\'s) and nothing(select all)');
			}
			
			if(is_string($columns) && $columns){
				if(strpos($columns,',') === false){
					$onemode2 = true;
				}
			}
			else if(!$columns){
				$columns = '*';
			}
			else{
				throw new Exception('columns can be string(comma seperate list of column names) or nothing(select all)');
			}

			if(is_string($order) && $order){
				$order = "ORDER BY $order";
			}
			else if(!$order){
				$order = '';
			}
			else{
				throw new Exception('order must be a string or nothing');
			}
			
			if(is_string($filer) && $filer){
				$where = ($where ? "$where AND $filer" : "WHERE $filer");
			}
			else if(!$filer){
				
			}
			else{
				throw new Exception('filter must be a string or nothing');
			}
			
			$query = "SELECT $columns FROM `$this->name` $where $order";
			$this->lastQuery = $query;
			$query = $this->db->query($query, $parram);
			
			if($onemode1 && $onemode2 && $this->onemode){
				$query = $query->fetchColumn();
				return $query;
			}
			else{
				$array = $query->fetchAll();
				$this->result = (empty($array) ? false : true);
				$this->count = count($array);
				return $array;
			}
		}
		
		public function listColmns(){
			$query = $this->db->query("DESCRIBE `$this->name`");
			$query = $query->fetchAll();
			
			$list = array();
			foreach($query as $object){
				$list[] = $object->Field;
			}
			return $list;
		}
		
		public function infoColmns(){
			$query = $this->db->query("DESCRIBE `$this->name`");
			$query = $query->fetchAll();
			return $query;
		}
		
		public function loop($string = '', $id = null, $order = null, $filer = null){
			
			$realcolumns = $this->listColmns();
			$lf = new microBoatLoopFunctions();
			
			$instring = array();
			preg_match_all('/\[(.*?)\\]/s', $string, $instring);
			
			$columns = array();
			$select = array();
			foreach($instring[1] as $key => $column){
				
				$mode = true;
				$lf->selector = false;
				if(strpos($column,'.') !== false){
					
					$function = array();
					preg_match('/\.(.*?)\\(/s', $column,$function);
					$function = $function[1];
					
					$attributes = array();
					preg_match('/\((.*?)\\)/s', $column,$attributes);
					$attributes = explode(',', $attributes[1]);
					
					$array = array();
					preg_match('/(.*?)(?=\.|$)/', $column,$array);
					$column = $array[1];
					
					$mode = false;
				}
				
				if(in_array($column, $realcolumns) && !in_array($column, $select) && $mode){
					$select[] = $column;
				}
				elseif(in_array($column, $realcolumns) && !$mode){
					
					$selector = $column;
					$func = 'pre_'.$function;
					if(method_exists($lf,$func)){
						$selector = $lf->$func($attributes, $column);
					}
					
					if(!in_array($selector, $select)){
						$select[] = $selector;
					}
				}
				
				if(in_array($column, $realcolumns) && $mode){
					$columns[] = array($instring[1][$key], $column);
				}
				elseif(in_array($column, $realcolumns) && !$mode){
					if($lf->selector){
						$column = $lf->selector;
					}
					$columns[] = array($instring[1][$key], $column, $function, $attributes);
				}
				
			}
			
			$this->onemode = false;
			$select = implode(',' ,$select);
			$query = $this->get($id, $select, $order, $filer);
			$this->onemode = true;
			
			$newstring = '';
			foreach($query as $row){
				$temp = $string;
				foreach($columns as $column){
					$value = $row->$column[1];
					if(isset($column[2])){
						$function = 're_'.$column[2];
						if(method_exists($lf,$function)){
							$value = $lf->$function($column[3], $column[1], $row->$column[1]);
						}
					}
					$temp = preg_replace('#\['.preg_quote($column[0]).'\]#', $value, $temp, 1);
				}
				$newstring .= $temp;
			}
			return $newstring;
		}
		
		public function remove($id){
			$this->delete($id);
		} 
		
		public function delete($id){
			
			//moet argument hebben
			
			//1 record verwijder modus
			
			//2 of meer verwijder modus
			
				//alle ids preparen
			
		}
		
		public function change($id, $data){
			$this->update($id, $data);
		}
		
		public function update($id, $data){
			
			//moet 2 argumenten hebben
			
			//1 record update mode
			
			//2 of meer update mode
			
				//alle data preparen
			
		}
		
		public function add($data){
			$this->insert($data);
		}
		
		public function insert($data){
			
			//Insert statement maken... 
			
		}
		
		public function json($id = null, $columns = null, $order = null, $filer = null){
			$query = $this->get($id, $columns, $order, $filer);
			return json_encode($query);
		}
		
		public function xml($id = null, $columns = null, $order = null, $filer = null){
			$query = $this->get($id, $columns, $order, $filer);
			
			$xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><'.$this->name.' />');
			
			foreach($query as $row){
				$ROW = $xml->addChild('row');
				$ROW->addAttribute('id', $row->id);
				foreach($row as $name => $value){
					$ROW->addChild($name, $value);
				}
			}
			
			return $xml->asXML();
		}
		
	}
	
	class microBoatLoopFunctions{
		
		private $id = 0;
		public $selector = false;
			
		public function re_boolean($array, $name, $value){
			return ($value ? $array[0] : $array[1]);
		}		
		
		public function re_bool($array, $name, $value){
			return ($value ? $array[0] : $array[1]);
		}
		
		public function pre_date($array, $name){
			$this->id++;
			$this->selector = $name.'_'.$this->id;
			return "DATE_FORMAT(`$name`, '$array[0]') AS `".$name."_$this->id`";
		}
		
		public function pre_sub($array, $name){
			$this->id++;
			$this->selector = $name.'_'.$this->id;
			return "SUBSTRING(`$name`, 1, '$array[0]') AS `".$name."_$this->id`";
		}
		
		public function re_money($array, $name, $value){
			return (strlen(substr(strrchr(floatval($value), '.'), 1)) == 0 ? number_format($value, 0, ',', '.') . ',-' : number_format($value, 2, ',', '.'));
		}
	}
	
?>