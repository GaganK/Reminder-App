<?php
//-----------------------------------------------------------------------------    
//
//	defines.php
//
//	MultiReminder Apps 
//
//-----------------------------------------------------------------------------    


	// general specifications
	//-----------------------
	define( 'VERSION', '0.1 Ver' );
	define( 'WEB_URL',	'http://creatainfotech.com/multireminderapp/' );
	define( 'BRAND_NAME', 'Multi-Reminder');
	
	// database configuration
	//-----------------------
	define("DB_HOST", 				'localhost' );
	//define("DB_USERNAME", 			'creatama_root' );
	//define("DB_PASSWORD", 			'sUvAqW%TA+BX' );
	define("DB_USERNAME", 			'root' );
	define("DB_PASSWORD", 			'' );
	define("DB_NAME", 				'creatama_multireminder' );
	define("API_KEY", 				'1ad29965d80f48409a2fef4dc191d4d6' );
	
	// key constant for google firebase notification api key
	//------------------------------------------------------
 	define('FIREBASE_API_KEY', 'AAAAt9FeENU:APA91bE7Y1uPpQhE5biRLRRakb2tdpetWUjmZitL5oUvq9ek3SkcWtV96jySoqXygnYVCXWObMLRECe0qA7O-zILM8krdFPN0W1iEGCjIesJ8APvrNaUoOis6ucR2Byg6QfurLC904kQ');
	
	// firebase server url to send the curl request
	//---------------------------------------------
	define('FIREBASE_API_URL', 'https://fcm.googleapis.com/fcm/send');
	
	// firebase server url to send the curl request
	//---------------------------------------------
	//http://creatainfotech.com/multireminderapp/api/images/multireminder.png
	define('FIREBASE_NOTIFY_LOGO_URL', 'http://'.$_SERVER['HTTP_HOST'].'/multireminderapp/api/images/multireminder.png' );
	
	
	// SMS Service provider api url
	//-----------------------------
	define('SMS_SERVICE_PROVIDER_API', 'http://user.orcainfosolutions.com/vendorsms/pushsms.aspx?user=creata&password=creata123&msisdn=[[Mobile]]&sid=CREATA&msg=[[Message]]&fl=0&gwid=2');
	
	
	
?>