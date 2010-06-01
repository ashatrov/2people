<?php
/**
 * @author ashatrov@gmail.com
**/
class JsonRPCException extends Exception {}

class JsonRPC {
	
	private $_request = array();
	private $_response = array();


	public function  __construct(){
		$this->setJsonRequest($this->getInput());
	}


	public function getRequest(){
		return $this->_request;
	}


	public function setRequest($request){
		$this->_request = $request;
	}


	public function getJsonRequest(){
		return json_encode($this->_request);
	}


	public function setJsonRequest($jsonRequest){
		$this->_request = json_decode($jsonRequest, true);
	}


	public function getInput(){
		return file_get_contents('php://input');
	}


	public function resetRequestParams(array $params){
		$this->_request = array_merge($this->_request, $params);
	}

	
	public function getResponse(){
		return $this->_response;
	}


	public function getJsonResponse(){
		return json_encode($this->_response);
	}
	
	
	public function echoJsonResponse(){
		if (!$this->isNotificationsRequest()) {
			header('content-type: text/javascript');
			echo $this->getJsonResponse();
		}
	}


	public function isJsonRpcRequest(){
		if( $_SERVER['REQUEST_METHOD'] != 'POST' ||
			empty($_SERVER['CONTENT_TYPE']) ||
			$_SERVER['CONTENT_TYPE'] != 'application/json' ||
			empty($this->_request['method']) ||
			empty($this->_request['params']) ||
			!is_array($this->_request['params'])){
			return false;
		}
		return true;
	}


	public function isNotificationsRequest(){
		if(isset($this->_request['id']) && $this->_request['id'] !== ''){
			return false;
		}
		return true;
	}


	public function resetResponseParams(array $params){
		$this->_response = array_merge($this->_response, $params);
	}


	public function makeResponse($result, $id = false){
		if(!$id) $id = $this->_request['id'];
		$this->_response = array('id' => $id, 'result' => $result, 'error' => NULL);
	}

	public function makeErrorResponse($error, $id = false){
		if(!$id) $id = $this->_request['id'];

		if($error instanceof JsonRPCException){
			$error = array(
				'message' => $error->getMessage(),
				'code' => $error->getCode(),
			);
		}

		$this->_response = array('id' => $id, 'result' => NULL, 'error' => $error);
	}

	public function callMethod($object, $method, $params){
		return @call_user_func(array($object, $method), $params);
	}

	public function handle($object) {
		try {
			$result = $this->callMethod($object, $this->_request['method'], $this->_request['params']);
			if($result){
				$this->makeResponse($result);
			} else {
				throw new JsonRPCException('The procedure call is not valid');
			}
		} catch (JsonRPCException $e) {
			$this->makeErrorResponse($e);
		} catch (Exception $e) {
			$this->makeErrorResponse('Unknown error');
		}
	}
}
?>