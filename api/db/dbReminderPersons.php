<?php
//-----------------------------------------------------------------------------    
//
//	dbReminderPersons.php
//
//	Access to the Table persons
//
//-----------------------------------------------------------------------------    

require_once("defines.php");
require_once("db/db.php");

class dbPerson
{

//-----------------------------------------------------------------------------    
//
//	public members
//
//-----------------------------------------------------------------------------    
	public $id;
	public $reminder_id;
	public $customer_id;
	public $person_name;
	public $person_mobile;
	public $complete;
	public $complete_updated_date;
	public $complete_remark;
	public $total;
	
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
			 return "dbPerson query error: $sql -- " .  mysql_error();
		}
    }        
    
//-----------------------------------------------------------------------------    
//
//	Get a row by Id
//
//-----------------------------------------------------------------------------    
    function getById( $id )
    { 
    	$sql = "SELECT * FROM persons"
    	. " WHERE persons.Id = '$id'";
    	
    	$this->query( $sql );
    }

//-----------------------------------------------------------------------------    
//
//	Get a row by reminder Id
//
//-----------------------------------------------------------------------------    
    function getByReminderId( $id )
    { 
    	$sql = "SELECT * FROM persons"
    	. " WHERE persons.reminder_id = '$id' ORDER BY persons.Id ASC";
    	
    	$this->query( $sql );
    }
    
	
//-----------------------------------------------------------------------------    
//
//	Get a row by reminder Id
//
//-----------------------------------------------------------------------------    
    function getByPersonMobile( $mobile )
    { 
    	$sql = "SELECT * FROM persons"
    	. " WHERE persons.person_mobile = '$mobile' GROUP BY persons.customer_id ORDER BY persons.customer_id ASC";
    	
    	$this->query( $sql );
    }
    
    function getByPersonMobileAndCustomerId($customerId,$personMobile){
        
        $sql = "SELECT * FROM persons"
    	. " WHERE persons.person_mobile = '$personMobile' AND persons.customer_id ='$customerId' ";
    	//exit($sql);
    	$this->query( $sql );
        
    }
	
	
//-----------------------------------------------------------------------------    
//
//	Get all 
//
//-----------------------------------------------------------------------------    
    function getAll( $begin = 0, $end = 0, $sort = "", $where = "" )
    {
    	if ( $sort == "" ) $sort = "ORDER BY persons.Id ASC";
    	
		$sql = "SELECT * FROM persons "
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
    	$sql = "SELECT COUNT(*) FROM persons " 
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
//	Get a next row by persons ID
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
    	
    	$this->save['Id'] 			= $this->id = $row['Id'];
	  	$this->save['reminder_id'] 		= $this->reminder_id = $row['reminder_id'];
		$this->save['customer_id'] 		= $this->customer_id = $row['customer_id'];
	  	$this->save['person_name'] 	= $this->person_name = $row['person_name'];
	  	$this->save['person_mobile'] 		= $this->person_mobile = $row['person_mobile'];
 	  	$this->save['complete']= $this->complete = $row['complete'];
		$this->save['complete_updated_date']= $this->complete_updated_date = $row['complete_updated_date'];
		$this->save['complete_remark']= $this->complete_remark = $row['complete_remark'];
    }
    

//-----------------------------------------------------------------------------    
//
//	Update a current row
//
//-----------------------------------------------------------------------------    
/*    function update()
    {
	
    		$sql = 	"UPDATE persons SET ";
			if(!empty($this->reminder_id)) $sql .=	"  reminder_id = '" . $this->reminder_id . "'";
    		if(!empty($this->person_name)) $sql .=	", person_name = '" . $this->person_name .  "'";
			if(!empty($this->person_mobile)) $sql .=	", person_mobile = '" . $this->person_mobile .  "'";
			if(!empty($this->product_rate)) $sql .=	", product_rate = '" . $this->product_rate .  "'";
			if(!empty($this->making_charge)) $sql .=	", making_charge = '" . $this->making_charge .  "'";
			if(!empty($this->total)) $sql .=	", total = '" . $this->total .  "'";
			
    		$sql .= " WHERE persons.Id = " . $this->id;
    		
			//echo $sql;
			 
	    	if ( mysql_query( $sql ) == false )
	    	{
			 	throw new Exception("dbPerson error update id=" . $this->id );
			 	return false;
	    	}
    	
    	return true;
    }
*/
//-----------------------------------------------------------------------------    
//
//	Insert the current row
//
//-----------------------------------------------------------------------------       
    function insert()
    {
    	
   		// Insert the agent
		//--------------------    	
    	$sql = 	"INSERT INTO persons SET reminder_id = '". $this->reminder_id ."', ";
		$sql .= " customer_id = '".$this->customer_id."', "; 
    	$sql .= " person_name = '".$this->person_name."', "; 
    	$sql .= " person_mobile = '".$this->person_mobile."' "; 
    	
		if ( !mysql_query( $sql ) )
    	{
		 	throw new Exception("dbPerson insert error" );
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
    	$sql = "DELETE FROM persons"
    	. " WHERE persons.Id = '". $id . "' ";
    	$result = mysql_query($sql);
		return $result;
    	//$this->query( $sql );
    }
	
	
    function getReminderAndCustomerById( $customerId, $reminderId )
    { 

    	$sql = "SELECT * FROM persons" 

    	. " WHERE persons.reminder_id = '". $reminderId . "' AND persons.customer_id = '". $customerId . "'";

    	$this->query( $sql );
		

    }
	
    function assignCompleteReminderByPerson( $id, $complete, $remark )

    { 
         
    	$sql = "UPDATE persons SET persons.complete_updated_date = NOW(), persons.complete = " . $complete

    	. ", persons.complete_remark = '".$remark."' WHERE persons.Id = '". $id . "'  ";
    	
        
    	$result = mysql_query($sql);



		return $result;

    }
	
		    		
}
?>