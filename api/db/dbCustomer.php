<?php

//-----------------------------------------------------------------------------    

//

//	dbCustomer.php

//

//	Access to the Table customer

//

//-----------------------------------------------------------------------------    



require_once("defines.php");

require_once("db/db.php");



class dbCustomer

{



//-----------------------------------------------------------------------------    

//

//	public members

//

//-----------------------------------------------------------------------------    

	public $id;

	public $name;

	public $phone;

	public $email;

	public $dob;

	public $createddate;

	public $username;

	public $password;

	public $firebase_token;
	

	

	

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

			 return "dbCustomer query error: $sql -- " .  mysql_error();

		}

    }        

    

//-----------------------------------------------------------------------------    

//

//	Get a row by Id

//

//-----------------------------------------------------------------------------    

    function getById( $id )

    { 

    	$sql = "SELECT * FROM customers"

    	. " WHERE customers.Id = '$id'";

    	

    	$this->query( $sql );

    }



//-----------------------------------------------------------------------------    

//

//	Get a row by Id

//

//-----------------------------------------------------------------------------    

    function checkUserNameEmail( $username, $email, $phone )

    { 

		

    	$sql = "SELECT * FROM customers"

    	. " WHERE customers.username = '$username' OR customers.email = '$email' OR customers.phone = '$phone'";

    	

    	$this->query( $sql );

    }





//-----------------------------------------------------------------------------    

//

//	Get a Agent by its login/password

//

//-----------------------------------------------------------------------------    

    function getByLogin( $username, $password )

    {



    	if ( !empty($username) && !empty($password))

    	{

		

			$a = strpbrk($username,"@");

	

			if($a){

				$qString = " customers.email = '" . $username  . "' ";

			}else{

				$qString = " customers.username = '" . $username  . "' ";

			}

		

    		$this->query( "SELECT * FROM customers"

	    		. " WHERE $qString AND customers.password = '" . md5($password)  . "' " );

    	}

    }

    

    

//-----------------------------------------------------------------------------    

//

//	Get all 

//

//-----------------------------------------------------------------------------    

    function getAll( $begin = 0, $end = 0, $sort = "", $where = "" )

    {

    	if ( $sort == "" ) $sort = "ORDER BY customers.Id ASC";

    	

		$sql = "SELECT * FROM customers "

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

    	$sql = "SELECT COUNT(*) FROM customers " 

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

    	

    	$this->save['Id'] 			= $this->id = $row['Id'];

	  	$this->save['name'] 		= $this->name = $row['name'];

	  	$this->save['phone'] 		= $this->phone = $row['phone'];

	  	$this->save['email'] 		= $this->email = $row['email'];

	  	$this->save['DOB'] 			= $this->dob =  $row['DOB'];

		$this->save['created_date']	= $this->createddate = $row['created_date'];

 	  	$this->save['username']		= $this->username = $row['username'];

		$this->save['firebase_token']		= $this->firebase_token = $row['firebase_token'];

    }

    



//-----------------------------------------------------------------------------    

//

//	Update a current row

//

//-----------------------------------------------------------------------------    

    function update()

    {

	

    		$sql = 	"UPDATE customers SET ";

			if(!empty($this->name)) $sql .=	"  name = '" . $this->name . "'";

    		if(!empty($this->phone)) $sql .=	", phone = '" . $this->phone .  "'";

			if(!empty($this->email)) $sql .=	", email = '" . $this->email .  "'";

			if(!empty($this->dob)) $sql .=	", DOB = '" . $this->dob .  "'";

			if(!empty($this->username)) $sql .=	", username = '" . $this->username .  "' ";

			

    		$sql .= " WHERE customers.Id = " . $this->id;

    		

			//echo $sql;

			 

	    	if ( mysql_query( $sql ) == false )

	    	{

			 	throw new Exception("dbcustomers error update id=" . $this->id );

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

    	

    	$this->createddate = date( "Y-m-d H:i:s" ); 

    	

   		// Insert the agent

		//--------------------    	

    	$sql = 	"INSERT INTO customers SET name = '". $this->name ."', ";

    	$sql .= " phone = '".$this->phone."', "; 

    	$sql .= " email = '".$this->email."', "; 

    	$sql .= " DOB = '".$this->dob."', "; 

    	$sql .= " created_date = now(), "; 

    	$sql .= " username = '".$this->username."', "; 

    	$sql .= " password = '".$this->password."' "; 

    	

		if ( !mysql_query( $sql ) )

    	{

		 	throw new Exception("dbCustomer insert error" );

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

    	$sql = "DELETE FROM customers"

    	. " WHERE customers.Id = '". $id . "' ";

    	$result = mysql_query($sql);

		return $result;

    	//$this->query( $sql );

    }

	
//-----------------------------------------------------------------------------    
//
//	update token a row by Id
//
//-----------------------------------------------------------------------------    
    function updateGoogleFirebaseTokenByCustomerId( $id, $token )
    { 
    	$sql = "UPDATE customers SET customers.firebase_token = '" . $token . "' WHERE customers.Id = '". $id . "' ";
    	$result = mysql_query($sql);

		return $result;
    }
		    		
//-----------------------------------------------------------------------------    
//
//	Get a row by Id
//
//-----------------------------------------------------------------------------    
    function getByMobile( $mobile )
    { 
    	$sql = "SELECT * FROM customers"
    	. " WHERE customers.phone = '$mobile'";
    	
    	$this->query( $sql );
    }
	
	// getting all tokens to send push to all devices
	//----------------------------------------------
    public function getAllTokens(){
        $sql = "SELECT token FROM customers";
        $this->query( $sql );
        $tokens = array(); 
        while($token = $this->firebase_token){
            array_push($tokens, $this->firebase_token);
        }
        return $tokens; 
    }	
    
    public function forgotPassword( $id, $password )
    {
				
    	$sql = "UPDATE customers SET customers.password = '" . md5($password)  . "' WHERE customers.phone = '". $id . "' ";
    	$result = mysql_query($sql);

		return $result;

    }

}

?>