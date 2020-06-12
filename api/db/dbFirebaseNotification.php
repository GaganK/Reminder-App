<?php

//-----------------------------------------------------------------------------    

//

//	dbFirebaseNotification.php

//

//	Access the Google Firebase Push Notification to android devices

//

//-----------------------------------------------------------------------------    



require_once("defines.php");

require_once("db/db.php");



class FirebaseNotification {



    public function send($registration_ids, $message) {

        $fields = array(

            'registration_ids' => $registration_ids,

            'data' => $message,

        );
		
		//$jsonData = json_encode($fields);
        return $this->sendPushNotification($fields);

    }

    

    /*

    * This function will make the actuall curl request to firebase server

    * and then the message is sent 

    */

    private function sendPushNotification($fields) {

         

        //importing the constant files

        //require_once 'Config.php';

		ignore_user_abort();
   		ob_start();
        //firebase server url to send the curl request

        $url = FIREBASE_API_URL;

        //building headers for the request

        $headers = array(

            'Authorization: key=' . FIREBASE_API_KEY,

            'Content-Type: application/json'

        );



        //Initializing curl to open a connection

        $ch = curl_init();

 

        //Setting the curl url

        curl_setopt($ch, CURLOPT_URL, $url);

        

        //setting the method as post

        curl_setopt($ch, CURLOPT_POST, true);



        //adding headers 

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

 

        //disabling ssl support

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        

        //adding the fields in json format 

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

 

        //finally executing the curl request 

        $result = curl_exec($ch);

        if ($result === FALSE) {

            die('Curl failed: ' . curl_error($ch));

        }

 

        //Now close the connection

        curl_close($ch);

 

        //and return the result 

        return $result;
		ob_flush();
    }
    
    
    public function callPushNotificationAPI($apiUrl,$data){
	
			$postData = json_encode($data);
	
			$context = stream_context_create(array(
				'http' => array(
				// http://www.php.net/manual/en/context.http.php
				'method' => 'POST',
				'header' => 'Content-Type: application/json',
				'content' => $postData,
				)
			));
			
			// Send the request
			
			$responseCall = file_get_contents($apiUrl,TRUE,$context);
			
			return $responseCall;
	
	}
	
    

}





?>