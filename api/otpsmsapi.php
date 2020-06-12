<?php

//-----------------------------------------------------------------------------------

//

//	MultiReminder OTP SMS AUTHENTICATION API

//

//	Copyright (C) 2017 MultiReminder

//

//-----------------------------------------------------------------------------------



//error_reporting(E_ALL|E_STRICT);

//ini_set("display_errors", "on");



require_once("defines.php");

require_once("db/db.php");

require_once("db/dbOtp.php");

require_once("lib/rest.inc.php");



session_start();



class OTPSMSServices extends Rest{



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



	public function sendSMS($Mobile,$smsText){

		$url 	= SMS_SERVICE_PROVIDER_API;


		$url = str_replace( "[[Mobile]]", $Mobile, $url );

		$url = str_replace( "[[Message]]", $smsText, $url );

		$url = str_replace( " ", "%20", $url );

//		$data = "[1]";
//    	$data_encoded = utf8_encode($data);

		$headers = array('Content-Type: application/json', 'Content-Length: ' . strlen($data_encoded));

		$ch = curl_init();

		if (!$ch){

			die("Couldn't initialize a cURL handle");

		}

		$ret = curl_setopt($ch, CURLOPT_URL,$url);

		curl_setopt ($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$ret = curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$ret = curl_setopt($ch, CURLOPT_TIMEOUT, 30);

		$response=curl_exec($ch);

		curl_close($ch);

		return $response;	

	}



	public function generateOTP(){



		$response = array();

		$requestData = json_decode(file_get_contents('php://input'));



		try {

			if(!empty($requestData)){



				// Lock the access

				//----------------

				$db = new db;

				$db->lock();



				$mobile	= $requestData->mobile;

				

				// validate api query string

				//--------------------------

				if(empty($mobile)){

					$response['status'] =0;

					$response['responseCode'] = 01;

					$response['msg'] = 'Empty mobile';

					$this->response($this->json($response),206);

					exit;

				}

				elseif(strlen($mobile)!=10 || strlen($mobile)>10 || !is_numeric($mobile)){

					// validate client receiver mobile number

					$response['status'] =0;

					$response['responseCode'] = 02;

					$response['msg'] = 'Invalid mobile number.';

					$this->response($this->json($response),400);

					exit;

				}

				

				//$this->destroyOTPSession();

				// generate OTP code

				//------------------

				$amountOfDigits = 6;

				$numbers = range(0,9);

				shuffle($numbers);

				$code = "";

				for($i = 0; $i<$amountOfDigits; $i++) 

				{

					$code .= $numbers[$i];

				}   

				//-----------------

				

				$txtOTPSms = $code . " is your OTP at ". BRAND_NAME;

				$this->sendSMS($mobile,$txtOTPSms);

				//exit($response);

				$timeFrom = strtotime(date("Y-m-d h:i:s"));

				

				$dbOtp = new dbOtp;

				$dbOtp->otp = $code;

				$dbOtp->time_from = $timeFrom;

				$dbOtp->mobile = $mobile;

				$dbOtp->status = "OTP_SENT";

				$dbOtp->insert();


						$response['status'] = 1;

						$response['responseCode'] = 000;

						$response['code'] 	= $code;

						$response['mobile'] = $mobile;

						$response['timeFrom'] = $timeFrom;

						$response['generateOTPSendStatus'] 	= "SUCCESS";

				$this->response($this->json($response),200);

				$db->unlock();

				exit;





			}//$requestData

		}//try

		catch (Exception $e)

		{

			$response['status'] =0;

			$response['responseCode']= 111;

			$response['msg'] = 'Invalid Inputs in Request Parameters';

			$this->response($this->json($response),400);

		}//catch

		// Unlock the table

		//---------------

		$db->unlock();



	}// function generateOTP()

	

	public function authenticateOTP(){



		$response = array();

		$requestData = json_decode(file_get_contents('php://input'));



		try {

			if(!empty($requestData)){



				// Lock the access

				//----------------

				$db = new db;

				$db->lock();



				$mobile	 		= $requestData->mobile;

				$code			= $requestData->code;

				$timeFrom		= $requestData->timeFrom;



				// validate api query string

				//--------------------------

				if(empty($mobile)){

					$response['status'] =0;

					$response['responseCode'] = 01;

					$response['msg'] = 'Empty mobile';

					$this->response($this->json($response),206);

					exit;

				}

				elseif(strlen($mobile)!=10 || strlen($mobile)>10 || !is_numeric($mobile)){

					// validate client receiver mobile number

					$response['status'] =0;

					$response['responseCode'] = 02;

					$response['msg'] = 'Invalid mobile number.';

					$this->response($this->json($response),400);

					exit;

				}

				elseif(empty($code) || !is_numeric($code)){

						

					$response['status'] =0;

					$response['responseCode'] = 03;

					$response['msg'] = 'Invalid OTP code.';

					$this->response($this->json($response),400);

					exit;

				}

				elseif(empty($timeFrom)){

						

					$response['status'] =0;

					$response['responseCode'] = 04;

					$response['msg'] = 'Empty from otp generated date.';

					$this->response($this->json($response),400);

					exit;

				}

				

				

				$dbOtp = new dbOtp;

				$dbOtp->getByOTPMobileTimeFrom($code,$mobile,$timeFrom);

				

	    		if(!empty($dbOtp->id))

				{

					//list($getCode, $getMobile, $getExpireTime) = explode("|", $_COOKIE["otp"]);

					$time_to = strtotime(date("Y-m-d h:i:s"));	    		

					$time_diff = round(($time_to - $timeFrom) / 60,60); // one hour for expiration


					if($time_diff <= 60){ 



									if ($code == $dbOtp->otp && $mobile == $dbOtp->mobile ) 

									{

									

												$dbOtpUpdate = new dbOtp;

												$dbOtpUpdate->otp  =  $code;

												$dbOtpUpdate->mobile  = $mobile;

												$dbOtpUpdate->time_from  = $timeFrom;

												$dbOtpUpdate->status = "OTP_SUCCESS";

												$dbOtpUpdate->update();



												$response['status'] = 1;

												$response['responseCode'] = 000;

												$response['code'] 	= $code;

												$response['mobile'] = $mobile;

												$response['timeFrom'] = $timeFrom;

												$response['authenticateOTPStatus'] 	= "SUCCESS";

												//$this->destroyOTPSession();

										$this->response($this->json($response),200);

										$db->unlock();

										exit;

														  

									}

									else 

									{

											$response['status'] = 1;

											$response['responseCode'] = 05;

											$response['msg'] = "Invalid OTP code ".$code."";

										$this->response($this->json($response),401);

										$db->unlock();

										exit;

										

										

									}

					}else{

					

								$dbOtpUpdate = new dbOtp;

								$dbOtpUpdate->otp  =  $code;

								$dbOtpUpdate->mobile  = $mobile;

								$dbOtpUpdate->time_from  = $timeFrom;

								$dbOtpUpdate->status = "OTP_EXPIRED";

								$dbOtpUpdate->update();

					

								$response['status'] = 1;

								$response['responseCode'] = 06;

								$response['msg'] = "OTP code ".$code." has been expired, please generate new code ";

							$this->response($this->json($response),401);

							$db->unlock();

							exit;

					

					}				

									

									

	    		}

				else 

				{

							$response['status'] = 1;

							$response['responseCode'] = 07;

							$response['msg'] = "OTP code ".$code." & Mobile ".$mobile." is not found, please generate new code ";

							//$this->destroyOTPSession();

						$this->response($this->json($response),401);

						$db->unlock();

						exit;

	    		}	

	    	

				

			}//$requestData

		}//try

		catch (Exception $e)

		{

			$response['status'] =0;

			$response['responseCode']= 111;

			$response['msg'] = 'Invalid Inputs in Request Parameters';

			$this->response($this->json($response),400);

		}//catch

		// Unlock the table

		//---------------

		$db->unlock();



	}// function authenticateOTP()

	




} // end of class





// Initiiate Library

$otpSmsServicesObj = new OTPSMSServices();

$otpSmsServicesObj->processApi();



?>

