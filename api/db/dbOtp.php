<?php
//-----------------------------------------------------------------------------    
//
//	dbOtp.php
//
//	Access to the Table tbl_otp
//
//-----------------------------------------------------------------------------    

require_once("defines.php");

require_once("db/db.php");

class dbOtp
{

//-----------------------------------------------------------------------------    
//
//	public members
//
//-----------------------------------------------------------------------------    
	public $id;
	public $otp;
	public $time_from;
	public $mobile;
	public $status;
	public $createddate;
	public $updateddate;
	
	
	
//-----------------------------------------------------------------------------    
//
//	Constructor & Destructor
//
//-----------------------------------------------------------------------------    
	function __construct() 
	{
    }
	
    function __destruct() 
    {
    }
    
//-----------------------------------------------------------------------------    
//
//	Query 
//
//-----------------------------------------------------------------------------    
    function query( $sql )
    {
	

    	// clear the current record
    	unset( $this->id );

    	// query the SQL
    	$this->queryResult = @mysql_query( $sql );

    	// if no SQL error 
    	if ( $this->queryResult )
		{
			// fetch the first record
			$row = mysql_fetch_array( $this->queryResult ); 
			if ( $row )
			{
				// set all values
				$this->setAll( $row );
			}
		}
    	else
		{
			 return "dbOtp query error: $sql -- " .  mysql_error();
		}
    }        
    
//-----------------------------------------------------------------------------    
//
//	Get a row by Id
//
//-----------------------------------------------------------------------------    
    function getById( $id )
    { 
    	$sql = "SELECT * FROM tbl_otp"
    	. " WHERE tbl_otp.id = '$id'";
    	
    	$this->query( $sql );
    }


//-----------------------------------------------------------------------------    
//
//	Get a row by Id
//
//-----------------------------------------------------------------------------    
    function getByOTPMobileTimeFrom( $otp, $mobile, $timeFrom )
    { 
    	$sql = "SELECT * FROM tbl_otp"
    	. " WHERE tbl_otp.otp = '$otp' AND tbl_otp.mobile = '$mobile' AND tbl_otp.time_from = '$timeFrom' ";
    	
    	$this->query( $sql );
    }
    
    
//-----------------------------------------------------------------------------    
//
//	Get all 
//
//-----------------------------------------------------------------------------    
    function getAll( $begin = 0, $end = 0, $sort = "", $where = "" )
    {
    	if ( $sort == "" ) $sort = "ORDER BY tbl_otp.id ASC";
    	
		$sql = "SELECT * FROM tbl_otp "
	    		. $where
				. " $sort";
			
		if ( $end != 0 ) $sql .= ' LIMIT ' . $begin . ' , ' . $end;	
		
    	$this->query( $sql );
    }               

    
//-----------------------------------------------------------------------------    
//
//	Get count 
//
//-----------------------------------------------------------------------------    
    function getCount( $where = "" )
    {
    	$this->count = 0;
    	$sql = "SELECT COUNT(*) FROM tbl_otp " 
    		. $where;

    	$result = mysql_query( $sql );
		if ( $result )
		{ 
			$row = mysql_fetch_array( $result ); 
			if ( $row )
			{
				$this->count = $row[0];
			}
		}
		return $this->count;
    }   
    
    
    
//-----------------------------------------------------------------------------    
//
//	Get a next row by Customer ID
//
//-----------------------------------------------------------------------------    
    function getNext( $trace = true )
    {
    	// clear the current record
    	unset($this->id);
    	
    	// get the next record
		$row = mysql_fetch_array( $this->queryResult ); 
		if ( $row )
		{
			$this->setAll( $row, $trace );		
		}
    }
     
    
//-----------------------------------------------------------------------------    
//
//	copy all fields in the members
//
//-----------------------------------------------------------------------------    
    function setAll( $row , $trace = true )
    {
    	
    	$this->save['id'] 			= $this->id = $row['id'];
		$this->save['otp']			= $this->otp = $row['otp'];
	  	$this->save['time_from'] 	= $this->time_from = $row['time_from'];
	  	$this->save['mobile'] 		= $this->mobile = $row['mobile'];
	  	$this->save['status'] 		= $this->status = $row['status'];
		$this->save['created_date']	= $this->createddate = $row['created_date'];
 	  	$this->save['updated_date']	= $this->updateddate = $row['updated_date'];
		
    }
    

//-----------------------------------------------------------------------------    
//
//	Update a current row
//
//-----------------------------------------------------------------------------    
    function update()
    {
	
    		$sql = 	"UPDATE tbl_otp SET updated_date = NOW() ";
			if(!empty($this->status)) $sql .=	", status = '" . $this->status .  "'";
			
    		$sql .= " WHERE tbl_otp.otp = " . $this->otp . " AND tbl_otp.mobile = "  . $this->mobile . " AND tbl_otp.time_from = "  . $this->time_from . " ";
    					 
	    	if ( mysql_query( $sql ) == false )
	    	{
			 	throw new Exception("dbOtp error update otp=" . $this->otp );
			 	return false;
	    	}
    	
    	return true;
    }

//-----------------------------------------------------------------------------    
//
//	Insert the current row
//
//-----------------------------------------------------------------------------       
    function insert()
    {
    	
   		// Insert the otp
		//---------------	
    	$sql = 	"INSERT INTO tbl_otp SET time_from = '". $this->time_from ."', ";
		$sql .= " otp = '".$this->otp."', ";
    	$sql .= " mobile = '".$this->mobile."', "; 
    	$sql .= " status = '".$this->status."', "; 
    	$sql .= " created_date = NOW() "; 
    	
		if ( !mysql_query( $sql ) )
    	{
		 	throw new Exception("dbOtp insert error" );
		 	return false;
    	}
    	else
    	{
    		$this->id = mysql_insert_id();	    	
    	}
    	return true;
    }
    
	
//-----------------------------------------------------------------------------    
//
//	Get a row by Id
//
//-----------------------------------------------------------------------------    
    function getByIdDelete( $id )
    { 
    	$sql = "DELETE FROM tbl_otp"
    	. " WHERE tbl_otp.id = '". $id . "' ";
    	$result = mysql_query($sql);
		return $result;
    	//$this->query( $sql );
    }
	
		    		
}
?>