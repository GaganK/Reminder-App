<?php
define('SERVICE_ENABLE',true);
class REST {
	
	public $_allow = array();
	public $_content_type = "application/json";
	public $_request = array();
	public $data = "";
	
	private $_method = "";		
	private $_code = 0000;
	
	
	public function __construct(){
		
		$this->inputs();
	}
	
	public function get_referer(){
		return $_SERVER['HTTP_REFERER'];
	}
	
	public function response($data,$status,$contentType="application/json"){
		$this->_code = ($status)?$status:0000;
		$this->_content_type = $contentType;
		$this->set_headers();
		echo $data;
		exit;
	}
	
	private function get_status_message(){
		$status = array(
					100 => 'Continue',  
					101 => 'Switching Protocols',  
					200 => 'OK',
					201 => 'Created',  
					202 => 'Accepted',  
					203 => 'Non-Authoritative Information',  
					204 => 'No Content',  
					205 => 'Reset Content',  
					206 => 'Partial Content',  
					300 => 'Multiple Choices',  
					301 => 'Moved Permanently',  
					302 => 'Found',  
					303 => 'See Other',  
					304 => 'Not Modified',  
					305 => 'Use Proxy',  
					306 => '(Unused)',  
					307 => 'Temporary Redirect',  
					400 => 'Bad Request',  
					401 => 'Unauthorized',  
					402 => 'Payment Required',  
					403 => 'Forbidden',  
					404 => 'Not Found',  
					405 => 'Method Not Allowed',  
					406 => 'Not Acceptable',  
					407 => 'Proxy Authentication Required',  
					408 => 'Request Timeout',  
					409 => 'Conflict',  
					410 => 'Gone',  
					411 => 'Length Required',  
					412 => 'Precondition Failed',  
					413 => 'Request Entity Too Large',  
					414 => 'Request-URI Too Long',  
					415 => 'Unsupported Media Type',  
					416 => 'Requested Range Not Satisfiable',  
					417 => 'Expectation Failed',  
					500 => 'Internal Server Error',  
					501 => 'Not Implemented',  
					502 => 'Bad Gateway',  
					503 => 'Service Unavailable',  
					504 => 'Gateway Timeout',  
					505 => 'HTTP Version Not Supported');
		return ($status[$this->_code])?$status[$this->_code]:$status[500];
	}
	
	public function get_request_method(){
		return $_SERVER['REQUEST_METHOD'];
	}
	
	
	
	private function inputs(){
		switch($this->get_request_method()){
			case "POST":
				$POST = $this->sanitize($_POST);
				$this->_request = $this->cleanInputs($POST);
				break;
			case "GET":
				$this->_request = $this->sanitize($_GET);
				break;
			case "DELETE":
				$GET = $this->sanitize($_GET);
				$this->_request = $this->cleanInputs($GET);
				break;
			case "PUT":
				parse_str(file_get_contents("php://input"),$this->_request);
				$PUT = $this->sanitize($this->_request);
				$this->_request = $this->cleanInputs($PUT);
				break;
			default:
				$this->response('',406);	
				break;
		}
	}
	
	private function cleanInputs($data){
		$clean_input = array();
		if(is_array($data)){
			foreach($data as $k => $v){
				$clean_input[$k] = $this->cleanInputs($v);
			}
		}else{
			if(get_magic_quotes_gpc()){
				$data = trim(stripslashes($data));
			}
			$data = strip_tags($data);
			$clean_input = trim($data);
		}
		return $clean_input;
	}		
	
	private function set_headers(){
		header("HTTP/1.1 ".$this->_code." ".$this->get_status_message());
		header("Content-Type:".$this->_content_type);
	}

	//data sanitization
	private function sanitize( $data ) {
		foreach( $data as $key => $value ) {
			if( !is_array($value) || !is_object( $value ) ) {
				$data[$key] = mysql_real_escape_string( $value );
			} elseif ( is_object( $value )) {
				foreach( $value as $k => $v ) {
					$value->$k = mysql_real_escape_string( $v );
				}
				$data[$k] = $v;
			} elseif (is_array( $value ) ) {
				foreach( $value as $k => $v ) {
					$value[$k] = mysql_real_escape_string( $v );
				}
				$data[$k] = $v;
			}
		}
		return $data;
	}
	
	/*
	 *	Encode array into JSON
	*/
	public function json($data){
		if(is_array($data)){
			return json_encode($data);
		}
	}
}	
?>