<?php
class SSHelper{
	/**
	* Konstruktor
	*/
    function __construct(){
		
    }
	
	/**
	* überprüft ob (1)Array Keys in (2)Array vorhanden sind
	* param $array1: 1. Array
	* param $array2: 2. Array
	* return bool: true = alle vorhande
	*/
	function array_keys_exists(array $array1, array $array2){
		foreach($array1 as $k => $v){
			if(!array_key_exists($k, $array2)){
				return false;
			}
		}
		return true;
	}
	
	/**
	* überprüft ob (1)Array Keys in (2)Array vorhanden sind
	* param string $_str
	* param $array2: 2. Array
	*/
	public static function i18l($_str){
		return $_str;
	}
	
	/**
	* überprüft ob (1)Array Keys in (2)Array vorhanden sind
	* param string $_str
	*/
	public static function cleanVars($vars){
	   $input = mysql_real_escape_string($input);
	   $input = htmlspecialchars($input, ENT_IGNORE, 'utf-8');
	   $input = strip_tags($input);
	   $input = stripslashes($input);
	   return $input;
   
		return $_str;
	}
	
	/**
	* überprüft ob $_POST Array für die gewünschte FormId 
	* vorhanden ist und liefert sie zurück
	* param string $formId
	* return array
	*/
	public static function getPostByFormId($formId){
		if($_POST['SSForm'][$formId]){
			return SSHelper::cleanInput($_POST['SSForm'][$formId]);
		}
		return null;
	}
	
	/**
	* bereinigt $value Array und liefert sie zurück
	* param array $value
	* return array
	*/
	public static function cleanInput($value){
		//if the variable is an array, recurse into it
		if(is_array($value)){
			//for each element in the array...
			foreach($value as $key => $val){
				//...clean the content of each variable in the array
				$value[$key] = self::cleanInput($val);
			}
	
			//return clean array
			return $value;
		}
		else{
			$value = mysql_real_escape_string($value);
			$value = htmlspecialchars($value, ENT_IGNORE, 'utf-8');
			$value = strip_tags($value);
			$value = stripslashes($value);
			return $value;
			return mysql_real_escape_string(strip_tags(trim($value)), $link);
		}
	}
	
	/**
	* überprüft ob Email Valid ist
	* param string $email
	* return bool
	*/
	public static function isEmailValid($email){
		if(!ereg("^[A-Za-z0-9](([_\.\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([\.\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$",$email))
			return false;
		else
			return true;
	}
}

