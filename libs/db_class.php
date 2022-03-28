<?php

/*

	See documentation provided on shared drive.

	Use "about" method to identify the version of the code as we progress from MVP to MMP

	as well as my details for assessors during submission.

	

	Notes:

	This class is not intended to be a comprehensive database class, as its not developed to handle unions, joins, temporary tables etc.

	it is intended to assist with db interactions in a more simplistic method to get the project going.

	Should there become a need, then pleaase contact me to develop the code for more precise please contact myself (Adam M)

	

	[Change log]

	Version 1.0:

		Connection to the database, close database methods added, with options to insert a row and dlete

	Version 1.1

		This version release saw an update, display error, drop and clear methods implements, The file also became a class object, thus no longer

		just a set of methods called but allowing to use $db->methodName()

	Version 1.2

		About was added that results an array of values, the most useful is version number which can help developers identify is a new version has been released since their last

		work and thus if changes made abort scripts. ie if version!=1.2 or if version <=1.2 then do x y z, close was also renames, to disconnect 

	Version 1.3

		getRow was introduced to return a single entry, this is more relevant for username checks or retrieving an advert rather than a cluster of adverts

	Version 1.4

		activitylog this version saw the introduction of activity logging 

*/

$conn;

if (isset($_GET['debug'])){

    $debug_url = $_GET['debug'];

}else{

    $debug_url = false;

}

class db{

	private $settings_ok = false;

	private function getUID(){

    	if (isset($GLOBALS['uid'])){

    	    $uid=$GLOBALS['uid'];

    	    

    	}else{

    	    $uid =0;

    	    

    	}

    	return $uid;

	}

	function about(){

		return array(

			"version:"=>"1.4", 

			"developer"=>"A.Mackay", 

			"student"=>"2000418", 

			"copyright"=>"&copy;2022 all rights reserved", 

			"about"=>"Wolverhampton University - Collaborative Development on Frachub project - Semester 2 of 2022");

	}

	private function display_error($e){

		if ($GLOBALS['debug']){

			echo "Error: " . $e->getMessage();

		}

	}	



	function connect(){

		if (!isset($GLOBALS['settings'])){

		// display message to browser and stop all scripting

			echo ("Settings are missing");

			die;

		}else{

			// try establish a connection with the database

			$db_host = $GLOBALS['db_host'];

			$db_db = $GLOBALS['db_db'];

			$db_sn = $GLOBALS['db_sn'];

			$db_pwd = $GLOBALS['db_pwd'];

			try {

				$GLOBALS['conn'] = new PDO("mysql:host=$db_host;dbname=$db_db", $db_sn, $db_pwd);

				$GLOBALS['conn']->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				return true;

			} catch(PDOException $e) {

				$this->display_error($e);

				return false;

			}

		}

	}	



	function disconnect(){

		// delete instance of db;	

		$GLOBALS['conn'] = NULL;

	}



	function delete($table, $where = NULL, $safeguard = false){

		if ($GLOBALS['conn']){

			// check if a where clause has been offered or the boolean value was set to true

			// before executing any command without a where clause.

			$activity = "deleting table " . $table . " where " . $where;
			$uid = $this->getUID();

			if (is_null($where) && ($safeguard == false)){

				echo ("<li>Missing where clause or confirmation not declared");				

				return false;

			}else{

				$GLOBALS['conn']->prepare("DELETE FROM " . $table . " WHERE " . $where)->execute();

				$this->activityLog("Delete Data", $activity, $uid);

				return true;

			}

		}else{

			echo ("No Database connection");

			return false;

		}

	}



	private function activityLog($action, $activity, $userID){

		$arr['title'] = $action;

		$arr['description'] = $activity;

		$arr['userID'] = $userID;

		$arr['page'] = $_SERVER["SCRIPT_NAME"] . "?" . http_build_query($_GET, "", "&amp;");

		$arr['error'] = 0;

		$arr['read_entry'] = 0;

		$arr['timestamp'] = time();

		$this->insert("activity", $arr, false);

		

		// records an action on the website performed by the user (data overload mind)

	}

	private function errorLog($errno, $errstr, $errfile, $errline){		// store any error trigger to save the error in the database.

		$values = array(""=>"", ""=>"", ""=>"", ""=>"");

		//echo "Error " . $errno;

		return $this->insert("errors", $values);

	}



	function insert($table, $cell_and_values, $do_log=true){

		// inserts a row into a table of the database based on an associated array of values

		// provided, and prepared statement is used.

			$activity = "New entry made to " . $table . " with values " . implode(", ", $cell_and_values);

		    $uid = $this->getUID();

			$sqlQuery = "INSERT INTO " . $table . " ";

			$cells = "";

			$values = "";

			$execution = [];

			foreach($cell_and_values as $key=>$value){

				if ($cells == ""){

					$cells = $key;

				}else{

					$cells .= ", " . $key;

				}

				

				if ($values == ""){

					$values = ":" . $key;

				}else{

					$values .= ", :" . $key;

				}	

				$execution[":" . $key] = $value;

			}

			$sqlQuery .= "(" . $cells . ") VALUES (" . $values . ");";

			try{

				$query = $GLOBALS['conn']->prepare($sqlQuery);

				$query->execute($execution);

				if($do_log){

    				$this->activityLog("new data", $activity, $uid);

				}

				return true;

			} catch(PDOException $e) {

				$this->display_error($e);

				return false;

			}

		if (1==1){

		}else{

			if ($GLOBALS['debug']){

				echo ("<li>Unable to execute this command sqlAdd as there is no active db connnection");

			}

		}

	}



	function update($table, $values, $whereCondition = NULL, $safeguard = False){



		// replaces existing data within the database with new values provided.

		// the method contains the failsafe, (boolean variable) where there has not been

		// a where clause provided. Returning a true/false for success/failure

		if ($GLOBALS['conn']){

			if (is_null($whereCondition) && ($safeguard == false)){

				if ($GLABALS['debug']){

					echo ("<li>Missing where clause or confirmation not declared");

				}

				return false;

			}else{

				$activity = "Amanding entry " . $whereCondition . " in " . $table . " with values " . implode(", ", $values);

				$uid = $this->getUID();

				$sqlQuery = "UPDATE " . $table . " SET ";

				$counter = 1;

				foreach ($values as $key => $value){

					$sqlQuery .= $key . "= '$value'";

					if ($counter != count($values)){

						$sqlQuery .= ", ";

					}

					$counter++;

				}

				$sqlQuery .= " WHERE " . $whereCondition;

				try{

					$query = $GLOBALS['conn']->prepare($sqlQuery);

					$query->execute();

					$this->activityLog("update data", $activity, $uid);

					return true;

				} catch(PDOException $e) {

					$this->display_error($e);

					return false;

				}		

				return true;

			}

		}else{

			if ($GLOBALS['debug']){

				echo ("<li>Unable to execute this command sqlUpdate as there is no active db connnection");

			}

			return false;

		}

	}

	function getRow($table, $where){

		// expand on this in the future.

		$sql = "SELECT * FROM " . $table . " WHERE " . $where;

		try{

			$query = $GLOBALS['conn']->prepare($sql);

			$query->execute();

			return $query->fetch(PDO::FETCH_ASSOC);

		}catch(PDOException $e) {

			$this->display_error($e);

			return false;

		}

	}

	function query($table, $fields = NULL, $where = NULL, $safeguard = False, $orderBy = NULL, $limit = NULL){

		// performs a SQL query and returns the results as an associated array

		// for the developer to read through and access

		// a false return is a failure to carry out the query.

		$activity = "a query was made to " . $table;

		$uid = $this->getUID();

		$sql = "SELECT ";

		$counter = 1;

		if (is_array($fields)){

			foreach ($fields as $value){

				if ($counter != 1){

					$sql .= ", ";

				}

				$sql .= $value;

				$counter++;

			}

			$activity .= " " . implode(", ", $fields);

		}else{

			if ($safeguard == True){

				$sql .= "*";

			}

			$activity .= " * ";

		}

		$sql .= " FROM " . $table;

		if (is_null($where)){

			if ($GLOBALS['debug'] && $safeguard == False){

				echo "<li>Missing where clause or confirmation not declared";

				return false;

			}

		}else{

			if ($where != NULL){

				$sql .= " WHERE " . $where;

				$activity .= " where " . $where;

			}

		}



		if (is_array($orderBy) && $orderBy != NULL) {

			$sql .= " ORDER BY ";

			$counter = 1;

			foreach ($orderBy as $key => $value){

				if ($counter != 1){

					$sql .= ", ";

				}

				$sql .= $key . " ";

				if ($value == "DESC"){

					$sql .= "DESC";

				}else{

					$sql .= "ASC";

				}

				$counter++;

			}

		}

		// amendment date: 25/03/22 adding limit records in results.

		if ($limit != NULL){

		    $sql .= " LIMIT " . $limit;

		}

		if ($GLOBALS['debug'] && $GLOBALS['debug_url']){

    		echo $sql . "<br>";

		}

		try{

			$query = $GLOBALS['conn']->prepare($sql);

			$query->execute();

			$this->activityLog("querying data", $activity, $uid);

			return $query->fetchAll(PDO::FETCH_ASSOC);

		}catch(PDOException $e) {

			$this->display_error($e);

			return false;

		}		

	}



	function clearTable($table, $safeguard=false){

		if ($safeguard){

			$sqlQuery = "DELETE FROM " . $table;

			try{

				$query = $GLOBALS['conn']->prepare($sqlQuery);

				$query->execute();

			}catch(PDOException $e) {

				$this->display_error($e);

				return false;

			}

		}else{

			if ($GLOBALS['debug']){

				echo "<li>Missing where clause or confirmation not declared";

			}

		}	

	}



	function drop($table, $safeguard = false){

		if ($safeguard){

			$sqlQuery = "DROP TABLE " . $table;

			try{

				$query = $GLOBALS['conn']->prepare($sqlQuery);

				$query->execute();

			}catch(PDOException $e) {

				$this->display_error($e);

				return false;

			}

		}else{

			if ($GLOBALS['debug']){

				echo "<li>Missing where clause or confirmation not declared";

			}

		}	

	}

}

//$old_error_handler = set_error_handler("errorLog");



?>

