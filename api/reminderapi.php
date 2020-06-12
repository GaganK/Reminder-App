<?php

//-----------------------------------------------------------------------------------

//

//	Reminder API

//

//-----------------------------------------------------------------------------------
//ini_set('allow_url_fopen', 'On');
//ini_set('allow_url_include', '1');


error_reporting(0);
//error_reporting(E_ALL|E_STRICT);

//ini_set("display_errors", "on");

require_once("defines.php");

require_once("db/db.php");

require_once("db/dbReminder.php");

require_once("db/dbReminderPersons.php");

require_once("db/dbFirebaseNotification.php");

require_once("db/dbCustomer.php");

require_once("lib/rest.inc.php");



session_start();



class ReminderServices extends Rest{



	public $resData = array();



	public function __construct(){

		parent::__construct();				// Init parent contructor

	}



	public function processApi(){

			

		try{

			if(SERVICE_ENABLE==true){

				$func = strtolower(trim(str_replace("/","",$_REQUEST['request'])));



				if((int)method_exists($this,$func) > 0)

					$this->$func();

				else

					$this->response('Sorry! Not Found.',404);

				// If the method not exist with in this class, response would be "Page not found".

			}

			else

			{

				$resData = array("Error"=>array("ResponseCode"=>"1000","ResponseDescription"=>"Service offline"));

				$this->response($this->json($resData),1000);

			}

		}//try

		catch (Exception $e)

		{

			echo $e->getMessage("'Sorry! Service not available.'");

		}

	}



	public function addNewReminder(){



		$response = array();

		$requestData = json_decode(file_get_contents('php://input'));



		try{

			

			if(!empty($requestData)){

				

						$customer_id		= $requestData->customer_id;

						$title				= $requestData->title;

						$description		= $requestData->description;

						$remind_time		= $requestData->remind_time;

						$remind_date		= $requestData->remind_date;

						$personsArr			= $requestData->persons;

						$priority			= $requestData->priority;

						$repeat_type		= $requestData->repeat_type;

						$repeat_duration	= $requestData->repeat_duration;

						$key				= $requestData->key;

				

						// validate api query string

						//--------------------------

						if(empty($customer_id) || $customer_id==0 || $customer_id<0 || !is_numeric($customer_id)){

							$response['status'] =0;

							$response['responseCode'] = "001";

							$response['msg'] = 'Empty customer id';

							$this->response($this->json($response),206);

							exit;

						}

						elseif(empty($title)){

							$response['status'] =0;

							$response['responseCode'] = "002";

							$response['msg'] = 'Empty reminder title';

							$this->response($this->json($response),206);

							exit;

						}

						elseif(empty($description)){

							$response['status'] =0;

							$response['responseCode'] = "003";

							$response['msg'] = 'Empty reminder description';

							$this->response($this->json($response),206);

							exit;

						}

						elseif(empty($remind_time)){

							$response['status'] =0;

							$response['responseCode'] = "004";

							$response['msg'] = 'Empty remind time';

							$this->response($this->json($response),206);

							exit;

						}

						elseif(empty($remind_date)){

							$response['status'] =0;

							$response['responseCode'] = "005";

							$response['msg'] = 'Empty remind date';

							$this->response($this->json($response),206);

							exit;

						}

						elseif(empty($priority)){

							$response['status'] =0;

							$response['responseCode'] = "006";

							$response['msg'] = 'Empty priority';

							$this->response($this->json($response),206);

							exit;

						}

						elseif(!is_array($personsArr)){

							$response['status'] =0;

							$response['responseCode'] = "007";

							$response['msg'] = 'Empty products list';

							$this->response($this->json($response),206);

							exit;

						}

						/*elseif(empty($repeat_type)){

							$response['status'] =0;

							$response['responseCode'] = "008";

							$response['msg'] = 'Empty repeat type';

							$this->response($this->json($response),206);

							exit;

						}

						elseif(empty($repeat_duration)){

							$response['status'] =0;

							$response['responseCode'] = "009";

							$response['msg'] = 'Empty repeat duration';

							$this->response($this->json($response),206);

							exit;

						}*/

						elseif(empty($key) || $key!=API_KEY ){

							$response['status'] =0;

							$response['responseCode'] = "010";

							$response['msg'] = 'Invalid empty key';

							$this->response($this->json($response),206);

							exit;

						}



						// Lock the access

						//----------------

						$db = new db;

						$db->lock();

						

						// reminder db

						//------------

						$dbReminder = new dbReminder;

												

						$dbReminder->customer_id 		=  $customer_id;

						$dbReminder->title				=  $title;

						$dbReminder->description 		=  $description;

						$dbReminder->remind_time 		=  $remind_time; //date('H:i', strtotime($remind_time));

						$dbReminder->remind_date 		=  $remind_date;

						$dbReminder->priority 			=  $priority;

						$dbReminder->repeat_type 		=  $repeat_type;

						$dbReminder->repeat_duration 	=  $repeat_duration;

						

						// insert records

						//---------------

						$reminderId = $dbReminder->insert();

						

						if(!empty($reminderId)){

							

							//  Bill Purchase Product Db

							//--------------------------

							$dbPerson = new dbPerson;

//							echo "<pre>";

//							print_r($personsArr);

//							exit;

							foreach($personsArr as $Personval){

							

								$dbPerson->reminder_id 		=  $reminderId;

								$dbPerson->customer_id 		=  $customer_id;

								$dbPerson->person_name		=  $Personval->person_name;

								$dbPerson->person_mobile 	=  $Personval->person_mobile;

                                $personMobiles[]			=  $Personval->person_mobile;

								$dbPerson->insert();

							}


							// customer db
							//------------
							$dbCustomer = new dbCustomer;
			
							foreach($personMobiles as $key => $mobileValue){
							
									// check already registered or not
									//--------------------------------
									$dbCustomer->getByMobile($mobileValue);
									
									if(!empty($dbCustomer->id)){
										
										if(!empty($dbCustomer->firebase_token)){
												
												########################################
												#######   send push notification #######
												########################################
														
												$messageArr = array(	'Customer Id'  			=> $customer_id,
																		'Reminder Title'  	  	=> $title,
																		'Reminder Description'  => $description,
																		'Reminder Time'       	=> $remind_time,
																		'Reminder Date'       	=> $remind_date,
																		'Priority'     		  	=> $priority,
																		'Repeat Type'     	 	=> $repeat_type,
																		'Repeat Duration'     	=> $repeat_duration
																);
																
												$postMessage = json_encode($messageArr);				
										
												// FirebaseNotification db
												//------------------------
												$dbFirebaseNotification = new FirebaseNotification;
												
												// message body to send notification
												//----------------------------------
												$messageData = array();
												$messageData['data']['title'] 	= "Reminder added";
												$messageData['data']['message'] = $postMessage;
												$messageData['data']['image'] 	= FIREBASE_NOTIFY_LOGO_URL;
												
												// token device to receive notification
												//-------------------------------------
												$tokenRegisteredId[] = $dbCustomer->firebase_token;
					//							print_r($messageData);
					//							echo "<br>\n";
					//							exit($tokenRegisteredId);
												// push notification to call firebase api
												//---------------------------------------
												$responseFirebase = $dbFirebaseNotification->send($tokenRegisteredId, $messageData);
												
												$resultResponse = json_decode($responseFirebase);
												
												if($resultResponse->success == 1){
												
													$customerGoogleFirebaseSendPushNotificationStatus = "SUCCESS";
													
												}elseif($resultResponse->failure == 1){
												
													$customerGoogleFirebaseSendPushNotificationStatus = "FAILED";
			
												}
												
												
												$arrMobile['mobile'] = $mobileValue;
												$arrMobile['googleFirebaseSendPushNotificationStatus'] = $customerGoogleFirebaseSendPushNotificationStatus;
												
												$withTokenMobileList[] = $arrMobile;
					
												########################################
											
											
										}else{
												// token is not found
												//-------------------
												$withoutTokenMobileList[] =  $mobileValue;
										
										} 
										
									}else{
											// customer is not registered
											//---------------------------
											$customerNotRegistered[] = 	$mobileValue;	
										
									}
							
							}




                           
//							// send notification by google firebase
//							//-------------------------------------
//							$apiUrl = 'http://'.$_SERVER['HTTP_HOST'].'/multireminderapp/api/customerapi.php?request=customerGoogleFirebaseSendPushNotification';
//							 
//							// FirebaseNotification db
//							//------------------------
//							$dbFirebaseNotification = new FirebaseNotification;
//							
//							$messageArr = array(	'Customer Id'  => $customer_id,
//													'Reminder Title'  	  => $title,
//													'Reminder Description'       => $description,
//													'Reminder Time'     => $remind_time,
//													'Reminder Date'     => $remind_date,
//													'Priority'     => $priority,
//													'repeat_type'     => $repeat_type,
//													'repeat_duration'     => $repeat_duration
//											);
//											
//							$postMessage = json_encode($messageArr);				
//							
//							$postData = array(	'key' 	  => $key,
//												'mobile'  => $personMobiles,
//												'title'   => "Reminder added",
//												'message' => $messageArr
//										);
//													
//							$getNotificationResponse = $dbFirebaseNotification->callPushNotificationAPI($apiUrl,$postData);


		

							$response['status'] = 0;

							$response['responseCode'] = "000";

							$response['reminderId'] = $reminderId;

							$response['addNewReminderStatus'] = "SUCCESS";
							
							$response['withTokenMobileList'] 									= $withTokenMobileList;
							
							$response['withoutTokenMobileList'] 								= json_encode($withoutTokenMobileList);
							
							$response['customerNotRegisteredList'] 								= json_encode($customerNotRegistered);

							$response['msg'] = "OK";

							$this->response($this->json($response),200);

							$db->unlock();

							exit;

							

							

						}else{

							

							$response['status'] = 0;

							$response['responseCode'] = "016";

							$response['addNewReminderStatus'] = "FAILED";

							$response['msg'] = "NOK";

							$this->response($this->json($response),200);

							$db->unlock();

							exit;

							

						}

							

				

				



			} //$requestData



		}//try

		catch (Exception $e)

		{

			$response['status'] =0;

			$response['responseCode']= "999";

			$response['msg'] = 'Invalid Request Format: '.$e;

			$this->response($this->json($response),400);

		}//catch



	} // end of addNewReminder() function

	

	public function deleteReminder(){



		$response = array();

		$requestData = json_decode(file_get_contents('php://input'));



		try{

			

			if(!empty($requestData)){

				

						$customer_id		= $requestData->customer_id;

						$reminder_id		= $requestData->reminder_id;

						$key				= $requestData->key;

				

						// validate api query string

						//--------------------------

						if(empty($customer_id) || $customer_id==0 || $customer_id<0 || !is_numeric($customer_id)){

							$response['status'] =0;

							$response['responseCode'] = "001";

							$response['msg'] = 'Invalid customer id';

							$this->response($this->json($response),206);

							exit;

						}

						elseif(empty($reminder_id) || $reminder_id==0 || $reminder_id<0 || !is_numeric($reminder_id)){

							$response['status'] =0;

							$response['responseCode'] = "002";

							$response['msg'] = 'invalid reminder id';

							$this->response($this->json($response),206);

							exit;

						}

						elseif(empty($key) || $key!=API_KEY ){

							$response['status'] =0;

							$response['responseCode'] = "010";

							$response['msg'] = 'Invalid empty key';

							$this->response($this->json($response),206);

							exit;

						}



						// Lock the access

						//----------------

						$db = new db;

						$db->lock();

						

						// reminder db

						//------------

						$dbReminder = new dbReminder;

						

						// delete records

						//---------------

						$result = $dbReminder->getByIdDelete($reminder_id, $customer_id);

						

						if($result == 1){

		

							$response['status'] = 0;

							$response['responseCode'] = "000";

							$response['deletedReminderId'] = $reminder_id;

							$response['deleteReminderStatus'] = "SUCCESS";

							$response['msg'] = "OK";

							$this->response($this->json($response),200);

							$db->unlock();

							exit;

							

							

						}else{

							

							$response['status'] = 0;

							$response['responseCode'] = "016";

							$response['reminderId'] = $reminder_id;

							$response['deleteReminderStatus'] = "FAILED";

							$response['msg'] = "NOK";

							$this->response($this->json($response),200);

							$db->unlock();

							exit;

							

						}

							

				

				



			} //$requestData



		}//try

		catch (Exception $e)

		{

			$response['status'] =0;

			$response['responseCode']= "999";

			$response['msg'] = 'Invalid Request Format: '.$e;

			$this->response($this->json($response),400);

		}//catch



	} // end of deleteReminder() function

	

	public function assignCompleteReminder(){



		$response = array();

		$requestData = json_decode(file_get_contents('php://input'));



		try{

			

			if(!empty($requestData)){

				

						$customer_id		= $requestData->customer_id;

						$reminder_id		= $requestData->reminder_id;
						
						$yourReminderId		= $requestData->yourReminderId;

						$complete			= $requestData->complete; // 0-PENDING, 1- COMPLETE
						
						$type				= $requestData->type; // USER, PERSON
						
						$remark				= $requestData->remark; 

						$key				= $requestData->key;

				

						// validate api query string

						//--------------------------

						if(!empty($customer_id)){
						
							if( $customer_id==0 || $customer_id<0 || !is_numeric($customer_id) ){

									$response['status'] =0;
		
									$response['responseCode'] = "001";
		
									$response['msg'] = 'Invalid customer id';
		
									$this->response($this->json($response),206);
		
									exit;
							}

						}

						elseif(!empty($reminder_id) ){
						
							if( $reminder_id==0 || $reminder_id<0 || !is_numeric($reminder_id) ){

									$response['status'] =0;
		
									$response['responseCode'] = "002";
		
									$response['msg'] = 'invalid reminder id';
		
									$this->response($this->json($response),206);
		
									exit;
							}

						}
						
						elseif(!empty($yourReminderId) ){
						
							if( $yourReminderId==0 || $yourReminderId<0 || !is_numeric($yourReminderId) ){

									$response['status'] =0;
		
									$response['responseCode'] = "002";
		
									$response['msg'] = 'invalid person reminder id';
		
									$this->response($this->json($response),206);
		
									exit;
							}

						}

						elseif(empty($complete) || $complete<0 || !is_numeric($complete)){

							$response['status'] =0;

							$response['responseCode'] = "003";

							$response['msg'] = 'invalid complete value';

							$this->response($this->json($response),206);

							exit;

						}
						
						elseif(empty($type)){

							$response['status'] =0;

							$response['responseCode'] = "004";

							$response['msg'] = 'invalid type value1';

							$this->response($this->json($response),206);

							exit;

						}
						
						/*elseif(!empty($type)){

                            if($type!='USER' || $type!='PERSON'){
        							$response['status'] =0;
        
        							$response['responseCode'] = "004";
        
        							$response['msg'] = 'invalid type value2';
        
        							$this->response($this->json($response),206);
        
        							exit;
                            }
        							

						}*/
						
						
						
						elseif(empty($remark)){

							$response['status'] =0;

							$response['responseCode'] = "005";

							$response['msg'] = 'Empty remark value';

							$this->response($this->json($response),206);

							exit;

						}
						
						elseif(empty($key) || $key!=API_KEY ){

							$response['status'] =0;

							$response['responseCode'] = "010";

							$response['msg'] = 'Invalid empty key';

							$this->response($this->json($response),206);

							exit;

						}



						// Lock the access

						//----------------

						$db = new db;

						$db->lock();

						if($type=="USER"){

							// reminder db
							//------------
							$dbReminder = new dbReminder;
	
							// Update records
							//---------------
							$result = $dbReminder->assignCompleteReminderById($reminder_id, $customer_id, $complete, $remark);
							
						}
						elseif($type=="PERSON"){
						
							// person db
							//------------
							$dbPerson = new dbPerson;
	                        //exit('test');
							// Update records
							//---------------
							$result = $dbPerson->assignCompleteReminderByPerson($yourReminderId, $complete, $remark);
						
						}
						
                        
						if($result == 1){

		

							$response['status'] = 0;

							$response['responseCode'] = "000";

							$response['assignCompleteReminderId'] = $reminder_id;

							$response['assignCompleteReminderStatus'] = "SUCCESS";

							$response['msg'] = "OK";

							$this->response($this->json($response),200);

							$db->unlock();

							exit;

							

							

						}else{

							

							$response['status'] = 0;

							$response['responseCode'] = "016";

							$response['reminderId'] = $reminder_id;

							$response['assignCompleteReminderStatus'] = "FAILED";

							$response['msg'] = "NOK";

							$this->response($this->json($response),200);

							$db->unlock();

							exit;

							

						}

							

				

				



			} //$requestData



		}//try

		catch (Exception $e)

		{

			$response['status'] =0;

			$response['responseCode']= "999";

			$response['msg'] = 'Invalid Request Format: '.$e;

			$this->response($this->json($response),400);

		}//catch



	} // end of assignCompleteReminder() function

	public function updateRepeatTypeDismissReminder(){



		$response = array();

		$requestData = json_decode(file_get_contents('php://input'));



		try{

			

			if(!empty($requestData)){

				

						$customer_id		= $requestData->customer_id;

						$reminder_id		= $requestData->reminder_id;
						
						$dismissStatus		= $requestData->repeat_type_dismiss; // 0-Dissmiss-OFF, 1- Dissmiss-ON

						$key				= $requestData->key;

						//exit($dismissStatus);

						// validate api query string

						//--------------------------
						if(empty($customer_id)){

									$response['status'] =0;
		
									$response['responseCode'] = "001";
		
									$response['msg'] = 'Invalid customer id';
		
									$this->response($this->json($response),206);
		
									exit;
							

						}
						elseif(!empty($customer_id)){
						
							if( $customer_id==0 || $customer_id<0 || !is_numeric($customer_id) ){

									$response['status'] =0;
		
									$response['responseCode'] = "001";
		
									$response['msg'] = 'Invalid customer id';
		
									$this->response($this->json($response),206);
		
									exit;
							}

						}
						elseif(empty($reminder_id) ){
						

									$response['status'] =0;
		
									$response['responseCode'] = "002";
		
									$response['msg'] = 'invalid reminder id';
		
									$this->response($this->json($response),206);
		
									exit;

						}
						elseif(!empty($reminder_id) ){
						
							if( $reminder_id==0 || $reminder_id<0 || !is_numeric($reminder_id) ){

									$response['status'] =0;
		
									$response['responseCode'] = "002";
		
									$response['msg'] = 'invalid reminder id';
		
									$this->response($this->json($response),206);
		
									exit;
							}

						}
						
						elseif(empty($dismissStatus) ){

								$response['status'] =0;
	
								$response['responseCode'] = "003";
	
								$response['msg'] = 'invalid dismiss status value';
	
								$this->response($this->json($response),206);
	
								exit;
						    
						}
						
						elseif(!empty($dismissStatus) ){
						
						   if( $dismissStatus<0 || !is_numeric($dismissStatus)){

								$response['status'] =0;
	
								$response['responseCode'] = "003";
	
								$response['msg'] = 'invalid dismiss status value';
	
								$this->response($this->json($response),206);
	
								exit;

						    }
						}
						
						elseif(empty($key) || $key!=API_KEY ){

							$response['status'] =0;

							$response['responseCode'] = "010";

							$response['msg'] = 'Invalid empty key';

							$this->response($this->json($response),206);

							exit;

						}



						// Lock the access
						//----------------

						$db = new db;
						$db->lock();

						// reminder db
						//------------
						$dbReminder = new dbReminder;
						
						// Update records
						//---------------
						$result = $dbReminder->updateRepeatTypeDismissReminderById($reminder_id, $customer_id, $dismissStatus);
						
                        
						if($result == 1){

							$response['status'] = 0;

							$response['responseCode'] = "000";

							$response['updateRepeatTypeDismissReminderId'] = $reminder_id;

							$response['updateRepeatTypeDismissReminderStatus'] = "SUCCESS";

							$response['msg'] = "OK";

							$this->response($this->json($response),200);

							$db->unlock();

							exit;

							

							

						}else{

							

							$response['status'] = 0;

							$response['responseCode'] = "016";

							$response['reminderId'] = $reminder_id;

							$response['updateRepeatTypeDismissReminderStatus'] = "FAILED";

							$response['msg'] = "NOK";

							$this->response($this->json($response),200);

							$db->unlock();

							exit;

							

						}

							

				

				



			} //$requestData



		}//try

		catch (Exception $e)

		{

			$response['status'] =0;

			$response['responseCode']= "999";

			$response['msg'] = 'Invalid Request Format: '.$e;

			$this->response($this->json($response),400);

		}//catch



	} // end of updateRepeatTypeDismissReminder() function

	public function editReminder(){



		$response = array();

		$requestData = json_decode(file_get_contents('php://input'));



		try{

			

			if(!empty($requestData)){

				

						// Lock the access

						//----------------

						$db = new db;

						$db->lock();

				

						$key			= $requestData->key;

						$customerId		= $requestData->customer_id;

						$reminderId		= $requestData->reminder_id;

						

						// validate api query string

						//--------------------------

						if(empty($key)){

							$response['status'] =0;

							$response['responseCode'] = "888";

							$response['msg'] = 'Empty key';

							$this->response($this->json($response),206);

							exit;

						}

						elseif($key!=API_KEY){

							$response['status'] =0;

							$response['responseCode'] = "666";

							$response['msg'] = 'Invalid key';

							$this->response($this->json($response),206);

							exit;

						}

						elseif(empty($customerId) || $customerId==0 || $customerId<0 || !is_numeric($customerId)){

							$response['status'] =0;

							$response['responseCode'] = "001";

							$response['msg'] = 'Invalid or empty customer ID';

							$this->response($this->json($response),206);

							exit;

						}

						elseif(empty($reminderId) || $reminderId==0 || $reminderId<0 || !is_numeric($reminderId)){

							$response['status'] =0;

							$response['responseCode'] = "002";

							$response['msg'] = 'Invalid or empty reminder ID';

							$this->response($this->json($response),206);

							exit;

						}

						

						// reminder db

						//------------

						$dbReminder = new dbReminder;

						$dbReminder->id			 = $reminderId;

						$dbReminder->customer_id = $customerId;

						

						if(!empty($requestData->title)) { $dbReminder->title = $requestData->title; }

						if(!empty($requestData->description)) { $dbReminder->description = $requestData->description; }

						if(!empty($requestData->remind_time)) { $dbReminder->remind_time = $requestData->remind_time; }

						if(!empty($requestData->remind_date)) { $dbReminder->remind_date = $requestData->remind_date; }

						if(!empty($requestData->priority)) { $dbReminder->priority = $requestData->priority; }

						if(!empty($requestData->repeat_type)) { $dbReminder->repeat_type = $requestData->repeat_type; }

						if(!empty($requestData->repeat_duration)) { $dbReminder->repeat_duration = $requestData->repeat_duration; }

						

						// update customer

						//----------------

						$resultUpdate = $dbReminder->update();

						//exit("checkPoint");

						if($resultUpdate == 1){

                            

							// person db
							//------------
							$dbPerson = new dbPerson;
							
							$dbPerson->getReminderAndCustomerById( $customerId, $reminderId );
							
							while(isset($dbPerson->id)){
							    $mobileArr[] = $dbPerson->person_mobile;
							    $dbPerson->getNext();   
							}
							
							
							// customer db
							//------------
							$dbCustomer = new dbCustomer;
                        
							//print_r($mobileArr);
							//exit;
							foreach($mobileArr as $key => $mobileValue){
							
									// check already registered or not
									//--------------------------------
									$dbCustomer->getByMobile($mobileValue);
									
									if(!empty($dbCustomer->id)){
										
										if(!empty($dbCustomer->firebase_token)){
												
												########################################
												#######   send push notification #######
												########################################
												
												if(!empty($requestData->title)) { $messageArr['Reminder Title'] =  $requestData->title; }

						                        if(!empty($requestData->description)) { $messageArr['Reminder Description'] = $requestData->description; }

						                        if(!empty($requestData->remind_time)) { $messageArr['Reminder Time'] = $requestData->remind_time; }

						                        if(!empty($requestData->remind_date)) { $messageArr['Reminder Date'] = $requestData->remind_date; }

						                        if(!empty($requestData->priority)) { $messageArr['Priority'] = $requestData->priority; }

						                        if(!empty($requestData->repeat_type)) { $messageArr['Repeat Type'] = $requestData->repeat_type; }

						                        if(!empty($requestData->repeat_duration)) { $messageArr['Repeat Duration'] = $requestData->repeat_duration; }


												$postMessage = json_encode($messageArr);				
										
												// FirebaseNotification db
												//------------------------
												$dbFirebaseNotification = new FirebaseNotification;
												
												// message body to send notification
												//----------------------------------
												$messageData = array();
												$messageData['data']['title'] 	= "Reminder edited";
												$messageData['data']['message'] = $postMessage;
												$messageData['data']['image'] 	= FIREBASE_NOTIFY_LOGO_URL;
												
												// token device to receive notification
												//-------------------------------------
												$tokenRegisteredId[] = $dbCustomer->firebase_token;
					//							print_r($messageData);
					//							echo "<br>\n";
					//							exit($tokenRegisteredId);
												// push notification to call firebase api
												//---------------------------------------
												$responseFirebase = $dbFirebaseNotification->send($tokenRegisteredId, $messageData);
												
												$resultResponse = json_decode($responseFirebase);
												
												if($resultResponse->success == 1){
												
													$customerGoogleFirebaseSendPushNotificationStatus = "SUCCESS";
													
												}elseif($resultResponse->failure == 1){
												
													$customerGoogleFirebaseSendPushNotificationStatus = "FAILED";
			
												}
												
												
												$arrMobile['mobile'] = $mobileValue;
												$arrMobile['googleFirebaseSendPushNotificationStatus'] = $customerGoogleFirebaseSendPushNotificationStatus;
												
												$withTokenMobileList[] = $arrMobile;
					
												########################################
											
											
										}else{
												// token is not found
												//-------------------
												$withoutTokenMobileList[] =  $mobileValue;
										
										} 
										
									}else{
											// customer is not registered
											//---------------------------
											$customerNotRegistered[] = 	$mobileValue;	
										
									}
							
							}

						

							$response['status'] = 0;

							$response['responseCode'] = "000";

							$response['reminderId'] = $reminderId;

							$response['updateReminderStatus'] = "SUCCESS";
							
							$response['withTokenMobileList'] 									= $withTokenMobileList;
							
							$response['withoutTokenMobileList'] 								= json_encode($withoutTokenMobileList);
							
							$response['customerNotRegisteredList'] 								= json_encode($customerNotRegistered);

							$response['msg'] = "Reminder is successfully updated";

							$this->response($this->json($response),200);

							$db->unlock();

							exit;

						}

						else{

							$response['status'] = 0;

							$response['responseCode'] = "016";

							$response['reminderId'] = $reminderId;

							$response['updateReminderStatus'] = "FAILED";

							$response['msg'] = "Reminder is not updated";

							$this->response($this->json($response),200);

							$db->unlock();

							exit;

						}



			} //$requestData



		}//try

		catch (Exception $e)

		{

			$response['status'] =0;

			$response['responseCode']= "999";

			$response['msg'] = 'Invalid Request Format: '.$e;

			$this->response($this->json($response),400);

		}//catch



	} // end of editReminder() function

	

	public function addPersonReminder(){



		$response = array();

		$requestData = json_decode(file_get_contents('php://input'));



		try{

			

			if(!empty($requestData)){

				

						$customerId		= $requestData->customer_id;

						$reminderId		= $requestData->reminder_id;

						$personsArr		= $requestData->persons;

						$key			= $requestData->key;

				

						// validate api query string

						//--------------------------

						if(empty($customerId) || $customerId==0 || $customerId<0 || !is_numeric($customerId)){

							$response['status'] =0;

							$response['responseCode'] = "001";

							$response['msg'] = 'Empty customer id';

							$this->response($this->json($response),206);

							exit;

						}

						elseif(empty($reminderId) || $reminderId==0 || $reminderId<0 || !is_numeric($reminderId)){

							$response['status'] =0;

							$response['responseCode'] = "002";

							$response['msg'] = 'Invalid or empty reminder ID';

							$this->response($this->json($response),206);

							exit;

						}

						elseif(!is_array($personsArr)){

							$response['status'] =0;

							$response['responseCode'] = "003";

							$response['msg'] = 'Empty products list';

							$this->response($this->json($response),206);

							exit;

						}

						elseif(empty($key) || $key!=API_KEY ){

							$response['status'] =0;

							$response['responseCode'] = "010";

							$response['msg'] = 'Invalid empty key';

							$this->response($this->json($response),206);

							exit;

						}



						// Lock the access

						//----------------

						$db = new db;

						$db->lock();

						

						// reminder db

						//------------

						$dbReminder = new dbReminder;

						

						// search records

						//---------------

						$result = $dbReminder->getReminderAndCustomerById( $customerId, $reminderId );

						

						if(!empty($result)){

							

							//  Person Db

							//-----------

							$dbPerson = new dbPerson;

							

							foreach($personsArr as $Personval){

							

								$dbPerson->reminder_id 		=  $reminderId;

								$dbPerson->customer_id 		=  $customerId;

								$dbPerson->person_name		=  $Personval->person_name;

								$dbPerson->person_mobile 	=  $Personval->person_mobile;



								$dbPerson->insert();

							}



							$response['status'] = 0;

							$response['responseCode'] = "000";

							$response['reminderId'] = $reminderId;

							$response['addPersonReminder'] = "SUCCESS";

							$response['msg'] = "OK";

							$this->response($this->json($response),200);

							$db->unlock();

							exit;

							

						}else{

							

							$response['status'] = 0;

							$response['responseCode'] = "004";

							$response['msg'] = "customer id or reminder id does not exist";

							$this->response($this->json($response),200);

							$db->unlock();

							exit;

							

						}

							

				

				



			} //$requestData



		}//try

		catch (Exception $e)

		{

			$response['status'] =0;

			$response['responseCode']= "999";

			$response['msg'] = 'Invalid Request Format: '.$e;

			$this->response($this->json($response),400);

		}//catch



	} // end of addPersonReminder() function

	

	public function assignStatusReminder(){



		$response = array();

		$requestData = json_decode(file_get_contents('php://input'));



		try{

			

			if(!empty($requestData)){

				

						$customer_id		= $requestData->customer_id;

						$reminder_id		= $requestData->reminder_id;

						$status				= $requestData->status;

						$statusReason		= $requestData->statusReason;

						$key				= $requestData->key;

				

						// validate api query string

						//--------------------------

						if(empty($customer_id) || $customer_id==0 || $customer_id<0 || !is_numeric($customer_id)){

							$response['status'] =0;

							$response['responseCode'] = "001";

							$response['msg'] = 'Invalid customer id';

							$this->response($this->json($response),206);

							exit;

						}

						elseif(empty($reminder_id) || $reminder_id==0 || $reminder_id<0 || !is_numeric($reminder_id)){

							$response['status'] =0;

							$response['responseCode'] = "002";

							$response['msg'] = 'invalid reminder id';

							$this->response($this->json($response),206);

							exit;

						}

						elseif(empty($status) || $status<0 || !is_numeric($status)){

							$response['status'] =0;

							$response['responseCode'] = "003";

							$response['msg'] = 'invalid status value';

							$this->response($this->json($response),206);

							exit;

						}

						elseif(empty($key) || $key!=API_KEY ){

							$response['status'] =0;

							$response['responseCode'] = "010";

							$response['msg'] = 'Invalid empty key';

							$this->response($this->json($response),206);

							exit;

						}



						// Lock the access

						//----------------

						$db = new db;

						$db->lock();

						

						// reminder db

						//------------

						$dbReminder = new dbReminder;

						

						// update records

						//---------------

						$result = $dbReminder->assignStatusReminderById($reminder_id, $customer_id, $status, $statusReason);

						

						if($result == 1){

		

							$response['status'] = 0;

							$response['responseCode'] = "000";

							$response['assignStatusReminderId'] = $reminder_id;

							$response['assignStatusReminderStatus'] = "SUCCESS";

							$response['msg'] = "OK";

							$this->response($this->json($response),200);

							$db->unlock();

							exit;

							

							

						}else{

							

							$response['status'] = 0;

							$response['responseCode'] = "016";

							$response['assignStatusReminderId'] = $reminder_id;

							$response['assignStatusReminderStatus'] = "FAILED";

							$response['msg'] = "NOK";

							$this->response($this->json($response),200);

							$db->unlock();

							exit;

							

						}

							

				

				



			} //$requestData



		}//try

		catch (Exception $e)

		{

			$response['status'] =0;

			$response['responseCode']= "999";

			$response['msg'] = 'Invalid Request Format: '.$e;

			$this->response($this->json($response),400);

		}//catch



	} // end of assignStatusReminder() function

	

	public function getAllReminderByUser(){



		$response = array();

		$requestData = json_decode(file_get_contents('php://input'));



		try{

			

					// validate api query string 

					//--------------------------

					if(count($_GET) != 3){

							$response['status'] =0;

							$response['responseCode']= "999";

							$response['msg'] =  "Invalid request url";

							$this->response($this->json($response),400);

							exit;

					}



					foreach($_GET as $kay => $value){



						if(array_key_exists($kay, array('request'=>$value,'userId'=>$value,'key'=>$value) )) {

								continue;

						}else{

								$response['status'] =0;

								$response['responseCode']= 777;

								$response['msg'] =  "Invalid request GET parameter";

								$this->response($this->json($response),400);

								break;

								exit;

						}



					}



					$userId		 = $_GET['userId'];

					$key		 = $_GET['key'];

					

					// validate customer 

					//------------------

					if(empty($key)){

						$response['status'] =0;

						$response['responseCode'] = "888";

						$response['msg'] = 'Empty key';

						$this->response($this->json($response),206);

						exit;

					}

					elseif($key!=API_KEY){

						$response['status'] =0;

						$response['responseCode'] = "666";

						$response['msg'] = 'Invalid key';

						$this->response($this->json($response),206);

						exit;

					}

					elseif(empty($userId) || $userId==0 || $userId<0 || !is_numeric($userId)){

						$response['status'] =0;

						$response['responseCode'] = "017";

						$response['msg'] = 'Invalid or empty user ID';

						$this->response($this->json($response),206);

						exit;

					}

					

					// Lock the access

					//----------------

					$db = new db;

					$db->lock();

					

					// reminder db

					//------------

					$dbReminder = new dbReminder;

					

					$dbReminder->getByCustomerId($userId);

					$totalReminderCount = $dbReminder->getCount();

					

					// check already exist or not

					//---------------------------

					if(empty($dbReminder->id)){
					
					$arrReminderAll = "You did not created any reminder yet";
					
					}

						$dbPerson = new dbPerson;

							

						while($dbReminder->id){	

							

									// get all person by reminderID

									//-----------------------------

									$dbPerson->getByReminderId($dbReminder->id);

									$personReminderList = "";

									

									if(!empty($dbPerson->id)){

									

											while($dbPerson->id){

												

													$personReminderList[] = array(  "PersonName"		=> $dbPerson->person_name,

																					"PersonMobile" 		=> $dbPerson->person_mobile,

																					"PersonId"			=> $dbPerson->id,
																					
																					"completeStatus"	=> $dbPerson->complete,
																					
																					"completeRemark"	=> $dbPerson->complete_remark,
																					
																					"completeUpdatedDate"	=> $dbPerson->complete_updated_date

																		);

						

													$dbPerson->getNext();

											}

									}else{

									

													$personReminderList = "";

										

									}

									//-------------------------------

									

									

									$arrReminderAll[] = array(  "reminderId"			=> $dbReminder->id,

																"reminderTitle" 		=> $dbReminder->title,

																"reminderDescription" 	=> $dbReminder->description,

																"reminderRemindTime" 	=> $dbReminder->remind_time,

																"reminderRemindDate" 	=> $dbReminder->remind_date,

																"reminderPriority" 		=> $dbReminder->priority,

																"reminderRepeatType" 	=> $dbReminder->repeat_type,

																"reminderRepeatDuration" => $dbReminder->repeat_duration,
																
																"reminderRepeatTypeDismiss"     => $dbReminder->repeat_type_dismiss,

																"reminderCompleteStatus" => $dbReminder->complete,

																"reminderPersons" 		=> $personReminderList,

																"reminderCompleteUpdatedDate" 	=> $dbReminder->complete_updated_date,

																"reminderStatus" 		=> $dbReminder->status,

																"reminderStatusReason" 	=> $dbReminder->status_reason,

																"reminderStatusUpdatedDate" => $dbReminder->status_updated_date,

																"reminderCreatedDate" 		=> $dbReminder->created_date,

																"reminderUpdatedDate" 		=> $dbReminder->updated_date,

														);

		

									$dbReminder->getNext();

									

									

									

									

									

						}

						#######################################################################################################

//						// customer db
//						//------------
//						$dbCustomer = new dbCustomer;
//						$dbCustomer->getById($userId);
//						$mobile = $dbCustomer->phone;
//						
//						//$apiUrl = 'http://'.$_SERVER['HTTP_HOST'].'/multireminderapp/api/reminderapi.php';
//						$apiUrl = 'http://'.$_SERVER['HTTP_HOST'].'/multireminderapp/api/reminderapi.php?request=getAllReminderByAddedByOtherUser&key='.API_KEY.'&mobile='.$mobile;
//						
//						$context = stream_context_create(array(
//							'http' => array(
//							// http://www.php.net/manual/en/context.http.php
//							'method' => 'GET',
//							//'request' => 'getAllReminderByAddedByOtherUser',
//							//'key' => API_KEY,
//							//'mobile' => $mobile,
//							)
//						));
//						
//						// Send the request
//						$responseCall = file_get_contents($apiUrl,TRUE,$context);
//
//						$r = json_decode($responseCall, true);
//						$arrayReminderAllAddedByOtherUser = $r['arrayReminderAllAddedByOtherUser']['REMINDER_ALL_ADDED_BY_OTHER_USER'];



					// customer db
					//------------
					$dbCustomer = new dbCustomer;
					$dbCustomer->getById($userId);
					$mobile = $dbCustomer->phone;


					// person db
					//----------
					$dbPersonOtherUser = new dbPerson;
					$dbPersonOtherUser->getByPersonMobile($mobile); // unique personwise get data

					// check already exist or not
					//---------------------------
					if(!empty($dbPersonOtherUser->id)){

					

						$dbReminderOtherUser = new dbReminder;

							

						while($dbPersonOtherUser->id){	

							

									// get all reminder by customer_id

									//-------------------------------
									
									//$dbReminderOtherUser->getByCustomerId($dbPersonOtherUser->customer_id);
                                    //$dbReminderOtherUser->getByCustomerAndReminderId($dbPersonOtherUser->customer_id,$dbPersonOtherUser->reminder_id);
                                    $dbPersonOtherUser1 = new dbPerson;
                                    $dbPersonOtherUser1->getByPersonMobileAndCustomerId($dbPersonOtherUser->customer_id,$mobile);
									$reminderList = "";
                                    
                                    
                                    
									//echo "<pre>";
									//print_r($dbPersonOtherUser1);
									//exit;

									

									if(!empty($dbPersonOtherUser1->id)){

									         
                                            
											while($dbPersonOtherUser1->id){
											     
											        
                                                    $dbReminderOtherUser1 = new dbReminder;
												    $dbReminderOtherUser1->getById($dbPersonOtherUser1->reminder_id);
												    
												        if(!empty($dbReminderOtherUser1->id)){
												           
												            while($dbReminderOtherUser1->id){
												                
                                                                    
                                                                    
                													$reminderList[] = array(    "reminderId"			=> $dbReminderOtherUser1->id,
                
                																				"reminderTitle" 		=> $dbReminderOtherUser1->title,
                
                																				"reminderDescription" 	=> $dbReminderOtherUser1->description,
                
                																				"reminderRemindTime" 	=> $dbReminderOtherUser1->remind_time,
                
                																				"reminderRemindDate" 	=> $dbReminderOtherUser1->remind_date,
                
                																				"reminderPriority" 		=> $dbReminderOtherUser1->priority,
                
                																				"reminderRepeatType" 	=> $dbReminderOtherUser1->repeat_type,
                
                																				"reminderRepeatDuration" => $dbReminderOtherUser1->repeat_duration,
																								
																								"reminderRepeatTypeDismiss"     => $dbReminderOtherUser1->repeat_type_dismiss,
                
                																				"reminderCompleteStatus" => $dbReminderOtherUser1->complete,
                
                																				"reminderCompleteUpdatedDate" 	=> $dbReminderOtherUser1->complete_updated_date,
                
                																				"reminderStatus" 		=> $dbReminderOtherUser1->status,
                
                																				"reminderStatusReason" 	=> $dbReminderOtherUser1->status_reason,
                
                																				"reminderStatusUpdatedDate" => $dbReminderOtherUser1->status_updated_date,
                
                																				"reminderCreatedDate" 		=> $dbReminderOtherUser1->created_date,
                
                																				"reminderUpdatedDate" 		=> $dbReminderOtherUser1->updated_date,
																								
																								"yourReminderId"  			=> $dbPersonOtherUser1->id,
																								
																								"yourReminderCompleteStatus"  => $dbPersonOtherUser1->complete,
																								
																								"yourReminderRemark"  => $dbPersonOtherUser1->complete_remark,
																								
																								"yourReminderCompleteUpdatedDate"  => $dbPersonOtherUser1->complete_updated_date, 
                
                														);
                
                		
                
                													$dbReminderOtherUser1->getNext();
                												
												            } //end of reminder while list
												            
												            
												        }else{
												            $reminderList = "";
												        }
														
												$dbPersonOtherUser1->getNext();		  
											} // end of while
											
													  /*echo "<pre>";	
												      print_r($dbPersonOtherUser1);  
													  exit();*/
									        
									}else{

									

													$reminderList = "";

										

									}

									//-------------------------------

									

									// customer db

									//------------

									#$dbCustomer = new dbCustomer;
                                        
									$dbCustomer->getById($dbPersonOtherUser->customer_id);
                                    
									

									$arrOtherCustomerAll[] = array( "otherCustomerId"	 => $dbCustomer->id,

																	"otherCustomerName"  => $dbCustomer->name,

																	"otherCustomerPhone" => $dbCustomer->phone,

																	"otherCustomerEmail" => $dbCustomer->email,

																	"otherCustomerDOB" 	 => $dbCustomer->dob,

																	"reminderList" 	 => $reminderList

														);

							$dbPersonOtherUser->getNext();

									

						}


						$getReminderAllAddedYouByOtherUser = $arrOtherCustomerAll;

					

					}

					else{

					
						$getReminderAllAddedYouByOtherUser = "Reminder data is not found by other user";

					}







						#######################################################################################################



						$arrayReminderAll = array( "CUSTOMER_ID" => $userId, "REMINDER_ALL_RECORDS_COUNT" => $totalReminderCount, "REMINDER_ALL_RECORDS" => $arrReminderAll, "REMINDER_ALL_ADDED_YOU_BY_OTHER_USER" => $getReminderAllAddedYouByOtherUser);

													$response['status'] =1;

													$response['responseCode'] = "000";

													$response['reminderAll'] = $arrayReminderAll;

													$response['getAllReminderByUserStatus'] = "SUCCESS";

													$response['format'] = "JSON";

													$response['msg'] = "OK";

						$this->response($this->json($response),200);

						$db->unlock();

						exit;

					

				/*	}

					else{

					

							$response['status'] = 0;

							$response['responseCode'] = "016";

							$response['getAllReminderByUserStatus'] = "FAILED";

							$response['msg'] = "reminder data is not found by user";

							$this->response($this->json($response),200);

							$db->unlock();

							exit;

					

					}*/

						

					

			

			 // end of post else

			

		}//try

		catch (Exception $e)

		{

			$response['status'] =0;

			$response['responseCode']= "999";

			$response['msg'] = 'Invalid Request Format: '.$e;

			$this->response($this->json($response),400);

		}//catch



	} // end of getAllReminderByUser() function

	

	public function getAllReminderByAddedByOtherUser(){



		$response = array();

		$requestData = json_decode(file_get_contents('php://input'));



		try{

			

					// validate api query string 

					//--------------------------

					if(count($_GET) != 3){

							$response['status'] =0;

							$response['responseCode']= "999";

							$response['msg'] =  "Invalid request url";

							$this->response($this->json($response),400);

							exit;

					}



					foreach($_GET as $kay => $value){



						if(array_key_exists($kay, array('request'=>$value,'mobile'=>$value,'key'=>$value) )) {

								continue;

						}else{

								$response['status'] =0;

								$response['responseCode']= 777;

								$response['msg'] =  "Invalid request GET parameter";

								$this->response($this->json($response),400);

								break;

								exit;

						}



					}



					$mobile		 = $_GET['mobile'];

					$key		 = $_GET['key'];

					

					// validate customer 

					//------------------

					if(empty($key)){

						$response['status'] =0;

						$response['responseCode'] = "888";

						$response['msg'] = 'Empty key';

						$this->response($this->json($response),206);

						exit;

					}

					elseif($key!=API_KEY){

						$response['status'] =0;

						$response['responseCode'] = "666";

						$response['msg'] = 'Invalid key';

						$this->response($this->json($response),206);

						exit;

					}

					elseif(empty($mobile) || strlen($mobile)!=10 || !is_numeric($mobile)){

						$response['status'] =0;

						$response['responseCode'] = "017";

						$response['msg'] = 'Invalid or empty mobile number';

						$this->response($this->json($response),206);

						exit;

					}

					

					// Lock the access

					//----------------

					$db = new db;

					$db->lock();

					

					// person db

					//----------

					$dbPerson = new dbPerson;

					

					$dbPerson->getByPersonMobile($mobile);

					

					// check already exist or not

					//---------------------------

					if(!empty($dbPerson->id)){

					

						$dbReminder = new dbReminder;

							

						while($dbPerson->id){	

							

									// get all reminder by customer_id

									//-------------------------------

									//$dbReminder->getByCustomerId($dbPerson->customer_id);
                                    $dbReminder->getByCustomerAndReminderId($dbPerson->customer_id,$dbPerson->reminder_id);
									$reminderList = "";

									

//									echo "<pre>";

//									print_r($dbPerson);

//									exit;

									

									if(!empty($dbReminder->id)){

									

											while($dbReminder->id){

												

													$reminderList[] = array(    "reminderId"			=> $dbReminder->id,

																				"reminderTitle" 		=> $dbReminder->title,

																				"reminderDescription" 	=> $dbReminder->description,

																				"reminderRemindTime" 	=> $dbReminder->remind_time,

																				"reminderRemindDate" 	=> $dbReminder->remind_date,

																				"reminderPriority" 		=> $dbReminder->priority,

																				"reminderRepeatType" 	=> $dbReminder->repeat_type,

																				"reminderRepeatDuration" => $dbReminder->repeat_duration,
																				
																				"reminderRepeatTypeDismiss" => $dbReminder->repeat_type_dismiss,

																				"reminderCompleteStatus" => $dbReminder->complete,

																				"reminderCompleteUpdatedDate" 	=> $dbReminder->complete_updated_date,

																				"reminderStatus" 		=> $dbReminder->status,

																				"reminderStatusReason" 	=> $dbReminder->status_reason,

																				"reminderStatusUpdatedDate" => $dbReminder->status_updated_date,

																				"reminderCreatedDate" 		=> $dbReminder->created_date,

																				"reminderUpdatedDate" 		=> $dbReminder->updated_date

														);

		

													$dbReminder->getNext();

											}

									}else{

									

													$reminderList = "";

										

									}

									//-------------------------------

									

									// customer db

									//------------

									$dbCustomer = new dbCustomer;

									$dbCustomer->getById($dbPerson->customer_id);

									

									$arrOtherCustomerAll[] = array( "otherCustomerId"	 => $dbCustomer->id,

																	"otherCustomerName"  => $dbCustomer->name,

																	"otherCustomerPhone" => $dbCustomer->phone,

																	"otherCustomerEmail" => $dbCustomer->email,

																	"otherCustomerDOB" 	 => $dbCustomer->dob,

																	"reminderList" 	 => $reminderList

														);

							$dbPerson->getNext();

									

						}





						$arrayReminderAllAddedByOtherUser = array(  "REMINDER_ALL_ADDED_BY_OTHER_USER" => $arrOtherCustomerAll);

													$response['status'] =1;

													$response['responseCode'] = "000";

													$response['arrayReminderAllAddedByOtherUser'] = $arrayReminderAllAddedByOtherUser;

													$response['getReminderAllAddedByOtherUserStatus'] = "SUCCESS";

													$response['format'] = "JSON";

													$response['msg'] = "OK";

						$this->response($this->json($response),200);

						$db->unlock();

						exit;

					

					}

					else{

					

							$response['status'] = 0;

							$response['responseCode'] = "016";

							$response['getReminderAllAddedByOtherUserStatus'] = "FAILED";

							$response['msg'] = "reminder data is not found by other user";

							$this->response($this->json($response),200);

							$db->unlock();

							exit;

					

					}

						

					

			

			 // end of post else

			

		}//try

		catch (Exception $e)

		{

			$response['status'] =0;

			$response['responseCode']= "999";

			$response['msg'] = 'Invalid Request Format: '.$e;

			$this->response($this->json($response),400);

		}//catch



	} // end of getAllReminderByAddedByOtherUser() function

	

} // end of class





// Initiiate Library

$reminderServicesObj = new ReminderServices();

$reminderServicesObj->processApi();



?>
