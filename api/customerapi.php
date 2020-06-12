<?php

//-----------------------------------------------------------------------------------

//

//	Customer API

//

//-----------------------------------------------------------------------------------



//error_reporting(E_ALL|E_STRICT);

//ini_set("display_errors", "on");

require_once("defines.php");

require_once("db/db.php");

require_once("db/dbCustomer.php");

require_once("db/dbFirebaseNotification.php");

require_once("lib/rest.inc.php");



session_start();



class CustomerServices extends Rest{



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



	public function customerRegistration(){



		$response = array();

		$requestData = json_decode(file_get_contents('php://input'));



		try{

			

			if(!empty($requestData)){



				$name		= $requestData->name;

				$username	= $requestData->username;

				$phone		= $requestData->phone;

				$email		= $requestData->email;

				$DOB		= $requestData->DOB;

				$key		= $requestData->key;

				$password	= $requestData->password;

				

				// validate api query string

				//--------------------------

				if(empty($name)){

					$response['status'] =0;

					$response['responseCode'] = "001";

					$response['msg'] = 'Invalid name';

					$this->response($this->json($response),206);

					exit;

				}

				elseif(empty($username)){

					$response['status'] =0;

					$response['responseCode'] = "002";

					$response['msg'] = 'Invalid username';

					$this->response($this->json($response),206);

					exit;

				}

				elseif(empty($phone) || strlen($phone)>10 || !is_numeric($phone)){

					$response['status'] =0;

					$response['responseCode'] = "003";

					$response['msg'] = 'Invalid phone no.';

					$this->response($this->json($response),206);

					exit;

				}

				elseif(empty($email)  || !filter_var($email, FILTER_VALIDATE_EMAIL)){

					$response['status'] =0;

					$response['responseCode'] = "004";

					$response['msg'] = 'Invalid email address';

					$this->response($this->json($response),206);

					exit;

				}

				elseif(empty($DOB)){

					$response['status'] =0;

					$response['responseCode'] = "005";

					$response['msg'] = 'Invalid date of birth';

					$this->response($this->json($response),206);

					exit;

				}

				elseif(empty($key) || $key!=API_KEY ){

					$response['status'] =0;

					$response['responseCode'] = "006";

					$response['msg'] = 'Invalid empty key';

					$this->response($this->json($response),206);

					exit;

				}

				elseif(empty($password)){

					$response['status'] =0;

					$response['responseCode'] = "007";

					$response['msg'] = 'Invalid password';

					$this->response($this->json($response),206);

					exit;

				}



				// Lock the access

				//----------------

				$db = new db;

				$db->lock();

				

				// customer db

				//------------

				$dbCustomer = new dbCustomer;



				// check already registered or not

				//--------------------------------

				$dbCustomer->checkUserNameEmail($username,$email,$phone);

				if(!empty($dbCustomer->id)){

					

					if($dbCustomer->username == $username){

						$response['status'] = 0;

						$response['responseCode'] = "013";

						$response['msg'] = 'Username is already used';

						$this->response($this->json($response),401);

						$db->unlock();

						exit;

					}elseif($dbCustomer->email == $email){

						$response['status'] = 0;

						$response['responseCode'] = "014";

						$response['msg'] = 'Email is already registered';

						$this->response($this->json($response),401);

						$db->unlock();

						exit;

					}elseif($dbCustomer->phone == $phone){

						$response['status'] = 0;

						$response['responseCode'] = "014";

						$response['msg'] = 'Mobile is already registered';

						$this->response($this->json($response),401);

						$db->unlock();

						exit;
					}

					

				}

				

				$dbCustomer->name 			=  $name;

				$dbCustomer->username 		=  $username;

				$dbCustomer->email 			=  $email;

				$dbCustomer->phone 			=  $phone;

				$dbCustomer->dob 			=  $DOB;

				$dbCustomer->password 		=  md5($password);

				

				// insert records

				//---------------

				$dbCustomer->insert();

				

				if(!empty($dbCustomer->id)){



					########################################

					######## Enrolled Successfully   #######

					########################################

					$customerId = $dbCustomer->id;



					$response['status'] = 0;

					$response['responseCode'] = "000";

					$response['customerId'] = $customerId;

					$response['registrationStatus'] = "SUCCESS";

					$response['msg'] = "OK";

					$this->response($this->json($response),200);

					$db->unlock();

					exit;

					########################################

					

				}else{

					

					$response['status'] = 0;

					$response['responseCode'] = "016";

					$response['registrationStatus'] = "FAILED";

					$response['msg'] = "NOK";

					$this->response($this->json($response),200);

					$db->unlock();

					exit;

					

				}



				// Unlock the table

				//---------------

				$db->unlock();

				

				} //$requestData 

			

		}//try

		catch (Exception $e)

		{

			$response['status'] =0;

			$response['responseCode']= "999";

			$response['msg'] = 'Invalid Request Format: '.$e;

			$this->response($this->json($response),400);

		}//catch



	} // end of customerRegistration() function

	

	public function customerLogin(){



		$response = array();

		$requestData = json_decode(file_get_contents('php://input'));



		try{

			

			if(!empty($requestData)){



				$key			= $requestData->key;

				$username		= $requestData->username;

				$password		= $requestData->password;

				

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

				elseif(empty($username)){

					$response['status'] =0;

					$response['responseCode'] = "011";

					$response['msg'] = 'Invalid username';

					$this->response($this->json($response),206);

					exit;

				}

				elseif(empty($password)){

					$response['status'] =0;

					$response['responseCode'] = "012";

					$response['msg'] = 'Invalid password';

					$this->response($this->json($response),206);

					exit;

				}



				// Lock the access

				//----------------

				$db = new db;

				$db->lock();

				

				// customer db

				//------------

				$dbCustomer = new dbCustomer;

				

				// check already registered or not

				//--------------------------------

				$dbCustomer->getByLogin($username,$password);

				

				if(!empty($dbCustomer->id)){

					

					########################################

					########## Login Successfully   ########

					########################################

					$customerId = $dbCustomer->id;



					$response['status'] = 0;

					$response['responseCode'] 			= "000";

					$response['customerId'] 			= $dbCustomer->id;

					$response['customerName'] 			= $dbCustomer->name;

					$response['customerPhone'] 			= $dbCustomer->phone;

					$response['customerUserName']		= $dbCustomer->username;

					$response['customerEmail']			= $dbCustomer->email;

					$response['customerDateOfBirth'] 	= $dbCustomer->dob;

					$response['customerRegisteredDate'] = $dbCustomer->createddate;

					$response['loginStatus'] 			= "SUCCESS";

					$response['msg'] 					= "OK";

					$this->response($this->json($response),200);

					$db->unlock();

					exit;

					########################################

					

				}else{

					

					$response['status'] = 0;

					$response['responseCode'] = "016";

					$response['loginStatus'] = "FAILED";

					$response['msg'] = "Invalid customer login detail";

					$this->response($this->json($response),200);

					$db->unlock();

					exit;

					

				}



				// Unlock the table

				//---------------

				$db->unlock();

				

				} //$requestData

			

		}//try

		catch (Exception $e)

		{

			$response['status'] =0;

			$response['responseCode']= "999";

			$response['msg'] = 'Invalid Request Format: '.$e;

			$this->response($this->json($response),400);

		}//catch



	}// end of customerLogin() function

	public function customerGoogleFirebaseToken(){

		$response = array();
		$requestData = json_decode(file_get_contents('php://input'));

		try{
			
			if(!empty($requestData)){

				$key			= $requestData->key;
				$customerId		= $requestData->customerId;
				$token			= $requestData->token;
				
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
				elseif(empty($customerId)){
					$response['status'] =0;
					$response['responseCode'] = "011";
					$response['msg'] = 'Invalid customer id';
					$this->response($this->json($response),206);
					exit;
				}
				elseif(empty($token)){
					$response['status'] =0;
					$response['responseCode'] = "012";
					$response['msg'] = 'Empty google firebase token';
					$this->response($this->json($response),206);
					exit;
				}

				// Lock the access
				//----------------
				$db = new db;
				$db->lock();
				
				// customer db
				//------------
				$dbCustomer = new dbCustomer;
				
				// check already registered or not
				//--------------------------------
				$dbCustomer->getById($customerId);
				
				if(!empty($dbCustomer->id)){
					
					########################################
					####### Update Firebase Token   ########
					########################################
					
					$resultUpdate = $dbCustomer->updateGoogleFirebaseTokenByCustomerId($customerId,$token);
					
					if($resultUpdate == 1){
					
						$response['status'] = 1;
						$response['responseCode'] 			= "000";
						$response['customerId'] 			= $dbCustomer->id;
						$response['customerGoogleFirebaseTokenStatus'] 		= "SUCCESS";
						$response['msg'] 					= "OK";
						$this->response($this->json($response),200);
						$db->unlock();
						exit;
						
					}else{
						$response['status'] = 1;
						$response['responseCode'] 			= "016";
						$response['customerId'] 			= $dbCustomer->id;
						$response['customerGoogleFirebaseTokenStatus'] 		= "FAILED";
						$response['msg'] 					= "failed to update";
						$this->response($this->json($response),200);
						$db->unlock();
						exit;
					}
					########################################
					
				}else{
					
					$response['status'] = 0;
					$response['responseCode'] = "016";
					$response['customerGoogleFirebaseTokenStatus'] = "FAILED";
					$response['msg'] = "Invalid customer Id ";
					$this->response($this->json($response),200);
					$db->unlock();
					exit;
					
				}

				// Unlock the table
				//---------------
				$db->unlock();
				
				} //$requestData
			
		}//try
		catch (Exception $e)
		{
			$response['status'] =0;
			$response['responseCode']= "999";
			$response['msg'] = 'Invalid Request Format: '.$e;
			$this->response($this->json($response),400);
		}//catch

	}// end of customerGoogleFirebaseToken() function

	public function customerGoogleFirebaseSendPushNotification(){

		$response = array();
		$requestData = json_decode(file_get_contents('php://input'));

		try{
			
			if(!empty($requestData)){

				$key			= $requestData->key;
				$mobile			= $requestData->mobile;
				$title			= $requestData->title;
				$message		= $requestData->message;
				
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
				elseif(!is_array($mobile)){
					$response['status'] =0;
					$response['responseCode'] = "011";
					$response['msg'] = 'Empty mobile number';
					$this->response($this->json($response),206);
					exit;
				}
				elseif(empty($title)){
					$response['status'] =0;
					$response['responseCode'] = "012";
					$response['msg'] = 'Empty title';
					$this->response($this->json($response),206);
					exit;
				}
				elseif(empty($message)){
					$response['status'] =0;
					$response['responseCode'] = "013";
					$response['msg'] = 'Empty message';
					$this->response($this->json($response),206);
					exit;
				}

				// Lock the access
				//----------------
				$db = new db;
				$db->lock();
				
				// customer db
				//------------
				$dbCustomer = new dbCustomer;
				
				foreach($mobile as $key => $mobileValue){
				
						// check already registered or not
						//--------------------------------
						$dbCustomer->getByMobile($mobileValue);
						
						if(!empty($dbCustomer->id)){
							
							if(!empty($dbCustomer->firebase_token)){
							
									########################################
									#######   send push notification #######
									########################################
									
									// FirebaseNotification db
									//------------------------
									$dbFirebaseNotification = new FirebaseNotification;
									
									// message body to send notification
									//----------------------------------
									$messageData = array();
									$messageData['data']['title'] 	= $title;
									$messageData['data']['message'] = $message;
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
				

				// Final Response
				//---------------
				$response['status'] = 1;
				$response['responseCode'] 											= "000";
				$response['withTokenMobileList'] 									= $withTokenMobileList;
				$response['withoutTokenMobileList'] 								= $withoutTokenMobileList;
				$response['customerNotRegisteredList'] 								= $customerNotRegistered;
				$response['customerGoogleFirebaseSendPushNotificationStatus'] 		= "SUCCESS";
				//$response['customerGoogleFirebaseSendPushNotificationResponse'] 	= $resultResponse;
				$response['msg'] 					= "OK";
				$this->response($this->json($response),200);
				$db->unlock();
				exit;



				// Unlock the table
				//---------------
				$db->unlock();
				
				} //$requestData
			
		}//try
		catch (Exception $e)
		{
			$response['status'] =0;
			$response['responseCode']= "999";
			$response['msg'] = 'Invalid Request Format: '.$e;
			$this->response($this->json($response),400);
		}//catch

	}// end of customerGoogleFirebaseSendPushNotification() function
	
	public function forgotPassword(){



		$response = array();

		$requestData = json_decode(file_get_contents('php://input'));



		try{

			

			if(!empty($requestData)){



				$key			= $requestData->key;

				$customerId		= $requestData->customerId;

				$password		= $requestData->password;

				

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

				elseif(empty($customerId)){

					$response['status'] =0;

					$response['responseCode'] = "011";

					$response['msg'] = 'Empty customer id';

					$this->response($this->json($response),206);

					exit;

				}

				elseif(empty($password)){

					$response['status'] =0;

					$response['responseCode'] = "012";

					$response['msg'] = 'Invalid password';

					$this->response($this->json($response),206);

					exit;

				}



				// Lock the access

				//----------------

				$db = new db;

				$db->lock();

				

				// customer db
				//------------
				$dbCustomer = new dbCustomer;

				$dbCustomer->forgotPassword( $customerId, $password );

				$response['status'] = 0;

				$response['responseCode'] 			= "000";

				$response['customerId'] 			= $customerId;

				$response['forgotPasswordStatus'] 		= "SUCCESS";

				$response['msg'] 					= "OK";

				$this->response($this->json($response),200);

				$db->unlock();

				exit;

					########################################

					

				



				// Unlock the table

				//---------------

				$db->unlock();

				

				} //$requestData

			

		}//try

		catch (Exception $e)

		{

			$response['status'] =0;

			$response['responseCode']= "999";

			$response['msg'] = 'Invalid Request Format: '.$e;

			$this->response($this->json($response),400);

		}//catch



	}// end of forgotPassword() function
	
	
	
	
	
	
	
	
	public function getAllUser(){



		$response = array();

		$requestData = json_decode(file_get_contents('php://input'));



		try{

			

					// validate api query string 

					//--------------------------

					if(count($_GET) != 2){

							$response['status'] =0;

							$response['responseCode']= "999";

							$response['msg'] =  "Invalid request url";

							$this->response($this->json($response),400);

							exit;

					}



					foreach($_GET as $kay => $value){



						if(array_key_exists($kay, array('request'=>$value,'key'=>$value) )) {

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


					

					// Lock the access

					//----------------

					$db = new db;

					$db->lock();

					

					// reminder db

					//------------

					$dbCustomer = new dbCustomer;

					$totalCustomerCount = $dbCustomer->getCount();
					$dbCustomer->getAll();
					

					// check already exist or not

					//---------------------------

					if(!empty($dbCustomer->id)){

						//$dbPerson = new dbPerson;

							

						while($dbCustomer->id){	

							

									// get all person by reminderID
									//-----------------------------
									//$dbPerson->getByReminderId($dbCustomer->id);

									//$personReminderList = "";

									

//									if(!empty($dbPerson->id)){
//
//									
//
//											while($dbPerson->id){
//
//												
//
//													$personReminderList[] = array(  "PersonName"		=> $dbPerson->person_name,
//
//																					"PersonMobile" 		=> $dbPerson->person_mobile,
//
//																					"PersonId"			=> $dbPerson->id
//
//																		);
//
//						
//
//													$dbPerson->getNext();
//
//											}
//
//									}else{
//
//									
//
//													$personReminderList = "";
//
//										
//
//									}

									//-------------------------------

									

									

									$arrCustomerAll[] = array(  "userId"		=> $dbCustomer->id,

																"userName" 			=> $dbCustomer->name,

																"userPhone" 		=> $dbCustomer->phone,

																"userEmail" 		=> $dbCustomer->email,

																"userDateOfBirth" 	=> $dbCustomer->dob,

																"userFirebaseToken" => $dbCustomer->firebase_token,

																"userCreatedDate" 	=> $dbCustomer->created_date,


														);

		

									$dbCustomer->getNext();

									

						}





						$arrayCustomerAll = array( "USER_ALL_RECORDS_COUNT" => $totalCustomerCount, "USER_ALL_RECORDS" => $arrCustomerAll);

													$response['status'] =1;

													$response['responseCode'] = "000";

													$response['userAll'] = $arrayCustomerAll;

													$response['getAllUserStatus'] = "SUCCESS";

													$response['format'] = "JSON";

													$response['msg'] = "OK";

						$this->response($this->json($response),200);

						$db->unlock();

						exit;

					

					}

					else{

					

							$response['status'] = 0;

							$response['responseCode'] = "016";

							$response['getAllUserStatus'] = "FAILED";

							$response['msg'] = "user data is not found by user";

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



	} // end of getAllUser() function

	
	
	
	

} // end of class





// Initiiate Library

$customerServicesObj = new CustomerServices();

$customerServicesObj->processApi();



?>

