<?php
//-----------------------------------------------------------------------------    
//
//	db.php
//
//	Access to the Data Base
//
//-----------------------------------------------------------------------------    

//require_once("../defines.php");


class db
{
	protected $connection;

//-----------------------------------------------------------------------------    
//
//	Constructor : Connect to the DataBase
//
//-----------------------------------------------------------------------------  	
	
	function __construct() 
	{
		$this->connection = @mysql_connect( DB_HOST, DB_USERNAME, DB_PASSWORD );
		if ( $this->connection == false )
		{
		 	TppTrace( "TPPDb Could not connect " .  mysql_error() );
		
	
		 	throw new Exception("TPPDb Could not connect: ".mysql_error());
		 	return;
		}
		if ( @mysql_select_db( DB_NAME, $this->connection ) == false )
		{
		 	TppTrace( "TPPDb Could not select database". mysql_error() );
		 	throw new Exception("TPPDb Could not select database");
		 	return;
		}
		@mysql_query( "SET NAMES UTF8" );
    }
	
//-----------------------------------------------------------------------------    
//
//	Destructor : Close the access to the DataBase
//
//-----------------------------------------------------------------------------  	
    function __destruct() 
    {
    	if ( $this->connection == true )
    	{
    		mysql_close($this->connection);
    	}
    }
    
//-----------------------------------------------------------------------------    
//
//	Lock & Unlock the access
//
//-----------------------------------------------------------------------------       
    function lock()
    {
    	$sql = "SELECT GET_LOCK( '" . DB_NAME . "', 10 )";
    	$result = mysql_query( $sql );
    	if ( ! $result )
    	{
    		TppTrace( "database lock error". mysql_error() );
    	}
    	else 
    	{
    		$row = mysql_fetch_array( $result );
    		if ( empty( $row[0] ) )  TppTrace( "database lock timeout 10 error" );
    	}    
    }
    
    function unlock()
    {
    	$sql = "SELECT RELEASE_LOCK( '" . DB_NAME . "' )";
    	$result = mysql_query( $sql );
    	if ( ! $result )
    	{
    		TppTrace( "database unlock error". mysql_error() );
    	}    
		
    }
}
?>