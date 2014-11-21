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
	
	
	public static function i18l($_str){
		return $_str;
	}
}

