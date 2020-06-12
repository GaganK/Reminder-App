<?php

//-----------------------------------------------------------------------------    

//

//	dbReminder.php

//

//	Access to the Table reminders

//

//-----------------------------------------------------------------------------    



require_once("defines.php");

require_once("db/db.php");



class dbReminder

{



//-----------------------------------------------------------------------------    

//

//	public members

//

//-----------------------------------------------------------------------------    

	public $id;

	public $customer_id;

	public $title;

	public $description;

	public $remind_time;

	public $remind_date;

	public $priority;

	public $repeat_type;

	public $repeat_duration;
	
	public $repeat_type_dismiss;

	public $complete;

	public $complete_updated_date;
	
	public $complete_remark;

	public $status;

	public $status_reason;

	public $status_updated_date;

	public $created_date;

	public $updated_date;

	public $count;

	

	// reminder persons

	//-----------------------

	public $reminder_id;

	public $person_name;

	public $person_mobile;

	

	

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

			 return "dbReminder query error: $sql -- " .  mysql_error();

		}

    }        

    

//-----------------------------------------------------------------------------    

//

//	Get a row by Id

//

//-----------------------------------------------------------------------------    

    function getById( $id )

    { 

    	$sql = "SELECT * FROM reminders"

    	. " WHERE reminders.Id = '$id'";

    	

    	$this->query( $sql );

    }



    

//-----------------------------------------------------------------------------    

//

//	Get a row by Id

//

//-----------------------------------------------------------------------------    

    function getByCustomerId( $id )

    { 

    	$sql = "SELECT * FROM reminders"

    	. " WHERE reminders.customer_id = '$id' ORDER BY reminders.priority DESC";

    	

    	$this->query( $sql );

    }

	
function getByCustomerAndReminderId( $id,$reminderId )

    { 

    	$sql = "SELECT * FROM reminders"

    	. " WHERE reminders.customer_id = '$id' AND reminders.id = '$reminderId' ORDER BY reminders.priority DESC";

    	

    	$this->query( $sql );

    }
	

//-----------------------------------------------------------------------------    

//

//	update a row by Id

//

//-----------------------------------------------------------------------------    

    function getReminderAndCustomerById( $customerId, $reminderId )

    { 

    	$sql = "SELECT * FROM reminders" 

    	. " WHERE reminders.Id = '". $reminderId . "' AND reminders.customer_id = '". $customerId . "'";

    	$result = mysql_query($sql);



		return $result;

    }

	

	

	

//-----------------------------------------------------------------------------    

//

//	Get all 

//

//-----------------------------------------------------------------------------    

    function getAll( $begin = 0, $end = 0, $sort = "", $where = "" )

    {

    	if( $sort == "" ) $sort = "ORDER BY reminders.Id DESC";

    	

		$sql = "SELECT * FROM reminders "

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

    	$sql = "SELECT COUNT(*) FROM reminders " 

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

//	Get a next row by reminders ID

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

	  	$this->save['title'] 		= $this->title = $row['title'];

	  	$this->save['customer_id'] 	= $this->customer_id = $row['customer_id'];

	  	$this->save['description'] 		= $this->description = $row['description'];

	  	$this->save['remind_time'] 	= $this->remind_time = $row['remind_time'];

	  	$this->save['remind_date'] 	= $this->remind_date =  $row['remind_date'];

	  	$this->save['priority'] = $this->priority = $row['priority'];

	  	$this->save['repeat_type'] 			= $this->repeat_type = $row['repeat_type'];		

	  	$this->save['repeat_duration'] 	= $this->repeat_duration = $row['repeat_duration'];
		
		$this->save['repeat_type_dismiss'] 			= $this->repeat_type_dismiss = $row['repeat_type_dismiss'];

 	  	$this->save['complete']= $this->complete = $row['complete'];

		$this->save['complete_updated_date']= $this->complete_updated_date = $row['complete_updated_date'];
		
		$this->save['complete_remark']= $this->complete_remark = $row['complete_remark'];
		
		$this->save['status']		= $this->status = $row['status'];

		$this->save['status_reason']		= $this->status_reason = $row['status_reason'];

		$this->save['status_updated_date']		= $this->status_updated_date = $row['status_updated_date'];

 	  	$this->save['created_date']		= $this->created_date = $row['created_date'];

		$this->save['updated_date']		= $this->updated_date = $row['updated_date'];

    }

    





//-----------------------------------------------------------------------------    

//

//	Insert the current row

//

//-----------------------------------------------------------------------------       

    function insert()

    {

    	

   		// Insert the agent

		//--------------------

		

    	$sql = 	"INSERT INTO reminders SET customer_id = '". $this->customer_id ."', ";

		$sql .= " title = '".$this->title."', "; 

    	$sql .= " description = '".$this->description."', "; 

    	$sql .= " remind_time = '".$this->remind_time."', "; 

		$sql .= " remind_date = '".$this->remind_date."', "; 

    	$sql .= " priority = '".$this->priority."', "; 

    	$sql .= " repeat_type = '".$this->repeat_type."', "; 

    	$sql .= " repeat_duration = '".$this->repeat_duration."', "; 
		
		//$sql .= " repeat_type_dismiss = '".$this->repeat_type_dismiss."', "; 

    	$sql .= " complete = '".$this->complete."', "; 

		$sql .= " status = '".$this->status."', ";

		$sql .= " status_reason = '".$this->status_reason."', "; 

		$sql .= " created_date = NOW() ";  

    	

		if ( !mysql_query( $sql ) )

    	{

		 	throw new Exception("dbReminder insert error" );

		 	return false;

    	}

    	else

    	{

    		$this->id = mysql_insert_id();	    	

    	}

    	return $this->id;

    	

    }

 

//-----------------------------------------------------------------------------    

//

//	update a row by Id

//

//-----------------------------------------------------------------------------    

    function assignCompleteReminderById( $id, $customer_id, $complete, $remark )

    { 

    	$sql = "UPDATE reminders SET reminders.complete_updated_date = NOW(), reminders.complete = " . $complete

    	. ", reminders.complete_remark = '".$remark."' WHERE reminders.Id = '". $id . "' AND reminders.customer_id = '". $customer_id . "'";
        
    	$result = mysql_query($sql);
        
		return $result;

    }

	
	
//-----------------------------------------------------------------------------    

//

//	update a row by Id

//

//-----------------------------------------------------------------------------    

    function updateRepeatTypeDismissReminderById( $id, $customer_id, $dismissStatus )

    { 

    	$sql = "UPDATE reminders SET reminders.updated_date = NOW(), reminders.repeat_type_dismiss = '" . $dismissStatus

    	. "' WHERE reminders.Id = '". $id . "' AND reminders.customer_id = '". $customer_id . "'";
        
    	$result = mysql_query($sql);
        //echo($sql);
		return $result;

    }
	
	
	
	

	

//-----------------------------------------------------------------------------    

//

//	update a row by Id

//

//-----------------------------------------------------------------------------    

    function assignStatusReminderById( $id, $customer_id, $status, $statusReason="" )

    { 

    	$sql = "UPDATE reminders SET reminders.status_updated_date = NOW(), reminders.status = " . $status . ", reminders.status_reason = '" . $statusReason

    	. "' WHERE reminders.Id = '". $id . "' AND reminders.customer_id = '". $customer_id . "'";

    	$result = mysql_query($sql);



		return $result;

    }

	

	

//-----------------------------------------------------------------------------    

//

//	Delete a row by Id

//

//-----------------------------------------------------------------------------    

    function getByIdDelete( $id, $customer_id )

    { 

    	$sql = "DELETE FROM reminders"

    	. " WHERE reminders.Id = '". $id . "' AND reminders.customer_id = '". $customer_id . "'";

    	$result = mysql_query($sql);

		

    	$sql1 = "DELETE FROM persons"

    	. " WHERE persons.reminder_id = '". $id . "' ";

    	$result1 = mysql_query($sql1);



		return $result;

    	//$this->query( $sql );

    }

	

	

//-----------------------------------------------------------------------------    

//

//	Update a current row

//

//-----------------------------------------------------------------------------    

    function update()

    {

	

    		$sql = 	"UPDATE reminders SET updated_date = NOW(), ";

			if(!empty($this->title)) $sql .=	"  title = '" . $this->title . "'";

    		if(!empty($this->description)) $sql .=	", description = '" . $this->description .  "'";

			if(!empty($this->remind_time)) $sql .=	", remind_time = '" . $this->remind_time .  "'";

			if(!empty($this->remind_date)) $sql .=	", remind_date = '" . $this->remind_date .  "'";

			if(isset($this->priority)) $sql .=	", priority = '" . $this->priority .  "' ";

			if(!empty($this->repeat_type)) $sql .=	",  repeat_type = '" . $this->repeat_type . "'";

			if(!empty($this->repeat_duration)) $sql .=	",  repeat_duration = '" . $this->repeat_duration . "'";
			
			if(!empty($this->repeat_type_dismiss)) $sql .=	",  repeat_type_dismiss = '" . $this->repeat_type_dismiss . "'";

			if(!empty($this->complete)) $sql .=	",  complete = '" . $this->complete . "'";

			if(!empty($this->status)) $sql .=	",  status = '" . $this->status . "'";

			if(!empty($this->status_reason)) $sql .=	",  status_reason = '" . $this->status_reason . "'";

			

    		$sql .= " WHERE reminders.Id = " . $this->id  . " AND reminders.customer_id = " . $this->customer_id;

			 

	    	if ( mysql_query( $sql ) == false )

	    	{

			 	throw new Exception("dbReminder error update id=" . $this->id );

			 	return false;

	    	}

    	

    	return true;

    }

	

	

	

	

		    		

}

?>