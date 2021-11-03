<?php
@require_once("./secure.php");
Class SQL {
	/*var $link;
	var $row;
	var $id;
	var $dbhost;
	var $dbname;
	var $dbuser;
	var $dbpass;*/
	
	
	function SQL($dbhost, $dbname, $dbuser, $dbpass) {
		if ($this->link = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname)) {
			$this->dbhost=$dbhost;
			$this->dbname=$dbname;
			$this->dbuser=$dbuser;
			$this->dbpass=$dbpass;
		} else{
			echo mysqli_errno();
			echo $dbhost;
			echo 'NO';
		}
		return $this->link;
	}

    function change_db($db) {
        @mysql_select_db($db, $this->link);
    }
	
/*	function queryIns($tabla, $campos, $values) {
		if (strstr($campos, ":")) $campos_query=implode(", ", explode(":", $campos));
		else $campos_query=trim($campos);
		$sql="INSERT INTO " . $tabla . " (" . $campos_query . ") VALUES (";
		$values_query=explode(":", $values);
		foreach ($values_query as $key => $clave) {
			$sql.="'" . $clave . "',";
		}							
		$sql=substr($sql, 0, -1);
		$this->link=mysql_query($sql);
		return ($this->link) ? $this->link : mysql_error($this->link);
	}*/
	
	function query($query) {
		$this->id = mysqli_query($this->link, $query);
		return $this->id;
	}
	
	function fetch_array($id) {
		return ($this->row=mysqli_fetch_array($id)) ? $this->row : mysqli_error($this->link);
	}
	
	function num_rows($id) {
     $total=mysqli_num_rows($id);
		return $total;
	}
	
	function free_result($id) {
		return mysqli_free_result($id);
	}
	function getOne($sql) {
		$result = $this->query($sql);
		$row = $this->fetchRow($result);
		return $row[0];
	}
	function getAll($sql) {
		$result = $this->query($sql);
		$results = [];
		while ($data = mysqli_fetch_assoc($result)) {
			$results[] = $data;
		}

		return $results;
	}
	function fetchRow($result, $row = NULL) {
		return mysqli_fetch_row($result);
	}	


}
?>