<?php
#
#
# SSCustomerLoginController
# https://github.com/last-hero/square_shop
#
# (c) Gobi Selva
# http://www.square.ch
#
# Diese Klasse dient als Helper Klasse für
# allgemeine Funktionen
#
#

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
	* liefert text in aktuelle sprache
	* param string $str
	* param string: übersetzte text
	*/
	public static function i18l($str){
		$str = str_replace('register_label_', '', $str);
		$str = str_replace('login_label_', '', $str);
		$str = str_replace('label_', '', $str);
		$str = '#'.$str;
		return $str;
	}
	
	/**
	* liefert Backend Setting Value vom Shop-Betreiber
	* param string $str
	*/
	public static function getSetting($str){
		global $REX;
		
		return $REX['ADDON']['square_shop']['settings'][$str];
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
	* Überprüft ob die angegebene $Value
	* dem Typ enstpricht
	* param string $type
	* param string $value
	* return bool
	*/
	public static function isTypeOf($type, $value){
		switch (trim($type)) {
			case 'int':
				$regEx_int = "/^[0-9]+$/i";
				if (preg_match($regEx_int, $value) == 0) {
					$w = true;
				}
				break;
			case 'float':
				$regEx_float = "/^([0-9]+|([0-9]+\.[0-9]+))$/i";
				if (preg_match($regEx_float, $value) == 0) {
					$w = true;
				}
				break;
			case 'numeric':
				if (!is_numeric($value)) {
					$w = true;
				}
				break;
			case 'string':
					break;
			case 'email':
				$regEx_email = "#^[\w.+-]{2,}\@\w[\w.-]*\.\w+$#u";
				if (preg_match($regEx_email, $value) == 0) {
					$w = true;
				}
				break;
			case 'url':
				$regEx_url = '/^(?:http:\/\/)[a-zA-Z0-9][a-zA-Z0-9._-]*\.(?:[a-zA-Z0-9][a-zA-Z0-9._-]*\.)*[a-zA-Z]{2,5}(?:\/[^\\/\:\*\?\"<>\|]*)*(?:\/[a-zA-Z0-9_%,\.\=\?\-#&]*)*$' . "/'";
				if (preg_match($regEx_url, $value) == 0) {
					$w = true;
				}
				break;
			case 'time':
				$w = true;
				$ex = explode(':', $value);
				if (count($ex) == 3 && $ex[0] > -839 && $ex[0] < 839 && $ex[1] >= 0 && $ex[1] < 60  && $ex[2] >= 0 && $ex[2] < 60) {
						$w = false;
				}
				break;
			case 'date':
				$w = true;
				if (preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $value, $matches)) {
					if (checkdate($matches[2], $matches[3], $matches[1])) {
						$w = false;
					}
				}
				break;
			case 'datetime':
				$w = true;
				if (preg_match("/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $value, $matches)) {
					if (checkdate($matches[2], $matches[3], $matches[1])) {
						$w = false;
					}
				}
				break;
			case "hex":
				$regEx_hex = "/^[0-9a-fA-F]+$/i";
				if(preg_match($regEx_hex, $value)==0)
				$w = true;
			break;
			case '':
				break;
			default:
				// Typ nicht definiert
				$w = true;
				break;
		}
		if($w){
			return false;
		}
		return true;
	}
	
	/*
	* Formular Input Daten werden auf Richtigkeit geprüft
	* und liefert alle Felder mit Fehler zurück
	* param $formProperties
	* param $values
	* return array $errors
	*/
	public static function getFormProperties($formId, $table, $show_in){
		try{
			$fields = SSDBSchema::_getFields($table, null, array('show_in'=>$show_in));
		}catch(SSException $e) {
			echo $e;
		}
		// automatisch Formular generieren
		// Anhand von vordefinierten Variablen in der SSDBSchema Klasse
		$properties = array();
		foreach($fields as $f){
			$name = $f['name'];
			if(isset($f['input'])){
				$settings = $f['input_settings'];
				$label_values = array();
				foreach($settings['values'] as $v){
					$label_values[] = self::i18l($formId.'_label_'.$name.'_'.$v);
				}
				$properties[] = array(
					'name' => $name
					, 'label' => self::i18l($formId.'_label_'.$name)
					, 'values' => $settings['values']
					, 'value_type' => $settings['type']
					, 'label_values' => $label_values
					, 'required' => $settings['required']
					, 'max' => $settings['max']
					, 'min' => $settings['min']
					, 'type' => $f['input']
				);
			}
		}
		return $properties;
	}
	
	/*
	* Formular Input Daten werden auf Richtigkeit geprüft
	* und liefert alle Felder mit Fehler zurück
	* param $table
	* param $show_in
	* param $values
	* return array $errors
	*/
	public static function checkFromInputs($table, $show_in, $values){
		$errors = array();
		try{
			$fields = SSDBSchema::_getFields($table, null, array('show_in'=>$show_in));
		}catch(SSException $e) {
			echo $e;
		}
		foreach($fields as $property){
			$settings = $property['input_settings'];
			$settingsByShowIn = $property['input_settings_by_show_in'][$show_in];
			$settingsByShowIn = is_array($settingsByShowIn)?$settingsByShowIn:array($settingsByShowIn);
			$settings = array_merge($settings, $settingsByShowIn);
			$name = $property['name'];
			$value = $values[$name];
			$type = $settings['type'];
			$required = $settings['required'];
			$min = $settings['min'];
			$max = $settings['max'];
			$notexists = $settings['notexists'];
			$exists = $settings['exists'];
			if(!empty($type) and $type == 'password'){
				// To Do -> eine bessere lösung
				//if($values[$name.'_re'] and $value != $values[$name.'_re']){
				if($show_in == SSDBSchema::SHOW_IN_REGISTER and $value != $values[$name.'_re']){
					$errors[$name]['equal'] = true;
				}
			}elseif(!empty($type) and !SSHelper::isTypeOf($type, $value)){
				$errors[$name][$type] = true;
			}
			if($required and strlen(trim($value)) == 0){
				$errors[$name]['required'] = true;
			}
			if((int)$min and strlen($value) < (int)$min){
				$errors[$name]['min'] = true;
			}elseif((int)$max and strlen($value) > (int)$max){
				$errors[$name]['max'] = true;
			}
			if($exists or $notexists){
				$tableData = SSDBSchema::_getTable($table, true);
				$table_fullname = $tableData['name'];
				$where = $table_fullname.".".$name." = '".$value."' ";
				$query = SSDBSQL::_getSqlDmlQuery($where, $table, $show_in);
				$res = SSDBSQL::executeSql($query);
				if($notexists){
					if(count($res) < 1){
						$errors[$name]['notexists'] = true;
					}
				}
				if($exists){
					if(!empty($res)){
						$errors[$name]['exists'] = true;
					}
				}
			}
		}
		return $errors;
	}
}

