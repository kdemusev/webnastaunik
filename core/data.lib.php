<?php

// Model module for mysql data providing
// Author: Kiril Demusev
// e-mail: kiril [dot] demusev [at] gmail [dot] com
// Year: 2009
// Version: 0.9

class CData
{
	var $query_result = null;
	var $tpl = null;
    var $db = null;

	// Constructor
	// Purpose: connect to db and set global settings
	function __construct($tpl = null)
	{
		include("settings/sql.set.php");

		$this->db = mysqli_connect($_sqlhost, $_sqluser, $_sqlpass, $_sqldb);
                
		mysqli_query($this->db, 'set names utf8');
		mysqli_query($this->db, "set character_set_client='utf8'");
		mysqli_query($this->db, "set character_set_results='utf8'");
		mysqli_query($this->db, "set collation_connection='utf8_general_ci'");
		
		$this->tpl = $tpl;
	}
	
	function __destruct()
	{
        $this->sql_close();
	}
    
    function safe($val) {
        return mysqli_real_escape_string($this->db, $val);
    }
    
    function unsafe($val) {
        return $val;
    }
	// Function: query
	// Purpose: pass the sql query to the server and get
	// 			the answer from it if it was a select query
	function query($sql)
	{
		$this->query_result = mysqli_query($this->db, $sql) or $this->query_error($sql);
		
		if(stristr(substr($sql,0,8), "SELECT "))
		{
			// if was a select statement
			$data = array();
			while($row = mysqli_fetch_assoc($this->query_result))
			{
				foreach($row as $k => $v) {
					$row[$k] = $this->unsafe($v);
                }
				array_push($data,$row);
			}
			
			return $data;
		}
		else
		{
			// delete, update, insert
			return array();
		}
	}
	
	function scalar($sql)
	{
		$this->query_result = mysqli_query($this->db, $sql) or $this->query_error($sql);
		$row = mysqli_fetch_row($this->query_result);
		if($row)
            return $this->unsafe($row[0]);
		else
			return null;
	}
	
    function notaffected() {
        if(mysqli_affected_rows($this->db) == 0) {
            return true;
        }
        return false;
    }
    
    function affect() {
        return mysqli_affected_rows($this->db);
    }
    
	// Function: select_limit
	// Purpose: select data from db page by page
	// Parameters: query, result for pages count, result for number of current page,
	//			   count of items per page, number of item to start from
	function select_limit($sql, $start = 0, $size = 0, &$pages = null, &$curpage = null )
	{
   	if($size == 0)
			$size = 10; //CUtils::getSetting("pageitems", $this);
		
		// ������ � ����������� �������
		// ���������� ������ ��������� ������
		if(isset($_GET['start']))
			$start = $_GET['start'];
		if(isset($_SESSION['pagestart']))
			$start = $_SESSION['pagestart'];

		$start *= $size;
		$sql = "SELECT SQL_CALC_FOUND_ROWS ".substr($sql, 7);
		$sql .= " LIMIT $start,$size";
		$data = $this->query($sql);

		// ��������� ����� ���������� �������
        
		$count = $this->scalar("SELECT FOUND_ROWS() AS fr");
		$pages = floor(($count ? $count-1 : $count)/$size);
		$curpage = floor($start/$size);

		if($this->tpl)
		{
			$this->tpl->assign("totalcount", $count);
			$this->tpl->assign("pages", $pages);
			$this->tpl->assign("curpage", $curpage);
		}

		return $data;
	}
	
	// Function: query_error
	// Purpose: display an error after it was occured
	function query_error($sql = '') {
		trigger_error("mysql query error: ".$sql." ".mysqli_errno($this->db).": ".mysqli_error($this->db));
	}
	
	function last_id()
	{
		return mysqli_insert_id($this->db);
	}
	
	// Function: sql_close
	// Purpose: closes an sql connection
	function sql_close()
	{
		mysqli_close($this->db);
	}
}

?>
