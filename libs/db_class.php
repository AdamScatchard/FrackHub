<?php

/*

	See documentation provided on shared drive.

	Use "about" method to identify the version of the code as we progress from MVP to MMP

	as well as my details for assessors during submission.

	

	Notes:

	This class is not intended to be a comprehensive database class, as its not developed to handle unions, joins, temporary tables etc.

	it is intended to assist with db interactions in a more simplistic method to get the project going.

	Should there become a need, then please contact me to develop the code for more precise please contact myself (Adam M)

*/

$conn;



class db{

	private $settings_ok = false;

	

	function about(){

		return array(

			"version:"=>"1.3", 

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

			if (is_null($where) && ($safeguard == false)){

				echo ("<li>Missing where clause or confirmation not declared");				

				return false;

			}else{

				$GLOBALS['conn']->prepare("DELETE FROM " . $table . " WHERE " . $where)->execute();

				return true;

			}

		}else{

			echo ("No Database connection");

			return false;

		}

	}



	private function activityLog($activity, $userId){

		// records an action on the website performed by the user (data overload mind)

	}

	private function errorLog($errno, $errstr, $errfile, $errline){		// store any error trigger to save the error in the database.

		$values = array(""=>"", ""=>"", ""=>"", ""=>"");

		//echo "Error " . $errno;

		return $this->insert("errors", $values);

	}



	function insert($table, $cell_and_values){

		// inserts a row into a table of the database based on an associated array of values

		// provided, and prepared statement is used.

		

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

	function query($table, $fields = NULL, $where = NULL, $safeguard = False, $orderBy = NULL){

		// performs a SQL query and returns the results as an associated array

		// for the developer to read through and access

		// a false return is a failure to carry out the query.

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

		}else{

			if ($safeguard == True){

				$sql .= "*";

			}

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

					$sql . "DESC";

				}else{

					$sql . "ASC";

				}

				$counter++;

			}

		}

		try{

			$query = $GLOBALS['conn']->prepare($sql);

			$query->execute();

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

