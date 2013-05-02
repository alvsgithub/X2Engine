<?php

/**
 * Behavior class providing utilities for responding in a uniform yet also
 * context-sensitive manner.
 *
 * @property boolean $exitNonFatal If true, exit on non-fatal errors.
 * @property boolean $isConsole If true, run methods as though there's no HTTP request happening.
 * @property boolean $longErrorTrace Whether to print extended error traces with errors, for debugging
 * @property array $response The response, if returning a JSON
 * @package X2CRM.components
 */
class ResponseBehavior extends CBehavior {

	public static $_exitNonFatal = true;
	public static $_isConsole = true;
	public static $_longErrorTrace = false;
	public static $_response = array();

	/**
	 * Magic getter for {@link exitNonFatal}
	 * @return bool
	 */
	public function getExitNonFatal(){
		return self::$_exitNonFatal;
	}

	/**
	 * Magic setter for {@link exitNonFatal}
	 */
	public function setExitNonFatal($value){
		self::$_exitNonFatal = $value;
	}

	/**
	 * Magic getter for {@link isConsole}
	 * @return bool
	 */
	public function getIsConsole(){
		return self::$_isConsole;
	}

	/**
	 * Magic setter for {@link isConsole}
	 */
	public function setIsConsole($value){
		self::$_isConsole = $value;
	}

	/**
	 * Magic getter for {@link longErrorTrace}
	 * @return type
	 */
	public function getLongErrorTrace(){
		return self::$_longErrorTrace;
	}

	/**
	 * Magic setter for {@link longErrorTrace}
	 * @param type $long
	 */
	public function setLongErrorTrace($long){
		self::$_longErrorTrace = $long;
	}

	/**
	 * Magic getter for {@link response} 
	 */
	public function getResponse(){
		return self::$_response;
	}

	/**
	 * Magic setter for {@link response}
	 */
	public function setResponse(array $response){
		self::$_response = $response;
	}

	/**
	 * Universal, web-agnostic response function.
	 *
	 * Responds with a JSON if used in a web request; merely echoes the response
	 * message otherwise.
	 *
	 * @param type $message The message to respond with.
	 * @param type $error Indicates that an error has occurred
	 * @param type $fatal Shut down PHP thread after printing the message
	 */
	public static function respond($message, $error = false, $fatal = false){
		if(self::$_isConsole){
			echo $message;
			if($error && $fatal && !self::$_noHalt)
				Yii::app()->end();
		} else{
			$response = self::$_response;
			$response['message'] = $message;
			$response['error'] = $error;
			header("Content-type: application/json");
			echo CJSON::encode($response);
			Yii::app()->end();
		}
	}

	/**
	 * Error handler method that uses the web-agnostic response method.
	 *
	 * @param type $no
	 * @param type $st
	 * @param type $fi
	 * @param type $ln
	 */
	public static function respondWithError($no, $st, $fi = Null, $ln = Null){
		$fatal = $no === E_ERROR;
		if($fatal || self::$_exitNonFatal){
			$message = "Error [$no]: $st $fi L$ln";
			if(self::$_longErrorTrace){
				ob_start();
				debug_print_backtrace();
				$message .= ob_get_contents();
				ob_end_clean();
			}
			self::respond(self::$_isConsole ? $message : CHtml::encode($message), true);
		}
	}

	/**
	 * Shutdown function for handling fatal errors
	 *
	 * @global boolean $responding
	 */
	public static function respondFatalErrorMessage(){
		global $responding;
		$error = error_get_last();
		if($error != null && !$responding){
			$errno = $error["type"];
			$errfile = $error["file"];
			$errline = $error["line"];
			$errstr = $error["message"];
			self::respond("PHP ".($errno == E_PARSE ? 'parse' : 'fatal')." error [$errno]: $errstr in $errfile L$errline");
		}
	}

	/**
	 * @param Exception $e The uncaught exception
	 */
	public static function respondWithException($e){
		$message = 'Exception: "'.$e->getMessage().'" in '.$e->getFile().' L'.$e->getLine()."\n";
		if(self::$_longErrorTrace){
			foreach($trace as $stackLevel){
				$message .= $stackLevel['file'].' L'.$stackLevel['line'].' ';
				if($stackLevel['class'] != ''){
					$message .= $stackLevel['class'];
					$message .= '->';
				}
				$message .= $stackLevel['function'];
				$message .= "();\n";
			}
		}
		self::respond(self::$_isConsole ? $message : CHtml::encode($message), true);
	}

}

?>