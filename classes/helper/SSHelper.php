<?php
/** @file SSHelper.php
 *  @brief Daten modellieren
 *
 *  Diese Klasse dient als Helper Klasse für
 *  allgemeine Funktionen
 *
 *  @author Gobi Selva
 *  @author http://www.square.ch
 *  @author https://github.com/last-hero/square_shop
 */

class SSHelper{
	/** @brief Array-Keys vergleichen
	 *
	 *  Überprüft ob 1. Array Keys in 2. Array vorhanden sind
	 *
	 *  @param $array1: 1. Array
	 *  @param $array2: 2. Array
	 *  @return boolean
	 */
	function array_keys_exists(array $array1, array $array2){
		foreach($array1 as $k => $v){
			if(!array_key_exists($k, $array2)){
				return false;
			}
		}
		return true;
	}
	
	/** @brief Internationalization and localization
	 *
	 *  Liefert String in aktuelle Sprache
	 *
	 *  @param (string) $str: String zum übersetzen
	 *  @return (string) $str
	 */
	 
	public static function i18n($str){
		global $REX;
		
		// Eintrag in der lokale Language-Datei
		// erfassen, falls nicht bereits eingetragen.
		SSHelper::seti18l($str, $str);
		
		if(class_exists('rex_string_table')){
		//if(false){
			$key = 'SQUARE_SHOP_'.$str;
			if(!rex_string_table::keyExists($key)){
				$key = 'SQUARE_SHOP_'.$key;
				$query = 'INSERT INTO rex_string_table SET keyname="'.$key.'" , value_0="'.$str.'"';
				SSDBSQL::executeSqlQuery($query, false);
			}
			$retval = rex_string_table::getString($key, $fillEmpty = false);
			if(empty($retval)){
				$retval = str_replace('label_', '', $str);
			}
			return $retval;
		}else{
			$searchpath = $REX['INCLUDE_PATH'] . '/addons/square_shop/lang/';
			$i18n = new i18n($locale = 'fe_de_de_utf8', $searchpath);
			return $i18n->msg($str);
		}
	}
	
	/** @brief 
	 *
	 *  
	 */
	public static function seti18l($_k, $_v){
		global $REX;
		$file = $REX['INCLUDE_PATH'] . '/addons/square_shop/lang/fe_de_de_utf8.lang';
		if(substr_count(file_get_contents($file),$_k) == false) {
			$fp = fopen($file, 'a+');
			fwrite($fp, "\n$_k = $_v");
			fclose ( $fp );
		}
	}
	
	/** @brief Add all Texts to String Table
	 *
	 *  Alle Texte in der String-Table Addon Tabelle
	 *  importieren, damit Übersetzung in andere
	 *  Sprachen möglich ist.
	 */
	public static function importTranslationsToStringTable(){
		global $REX;
		if(class_exists('rex_string_table')){
			$searchpath = $REX['INCLUDE_PATH'] . '/addons/square_shop/lang/';
			$i18n = new i18n($locale = 'fe_de_de_utf8', $searchpath);
			$i18n->loadTexts();
			
			foreach($i18n->text as $key => $val){
				$key = 'SQUARE_SHOP_'.$key;
				if(!rex_string_table::keyExists($key)){
					$query = 'INSERT INTO rex_string_table SET 
						keyname="'.$key.'" 
						, value_0="'.$val.'"
						';
					SSDBSQL::executeSqlQuery($query, false);
				}else{
					
				}
			}
		}
		
	}
	
	/** @brief Remove all Texts from String Table
	 *
	 *  Alle Texte von der String-Table Addon Tabelle
	 *  entfernen.
	 */
	public static function deleteTranslationsFromStringTable(){
		global $REX;
		if(class_exists('rex_string_table')){
			$query = 'DELETE FROM rex_string_table 
				WHERE keyname LIKE "SQUARE_SHOP_%"';
				
				
			$res = SSDBSQL::executeSqlQuery($query, false);
			return $res;
		}
		
	}
	
	/** @brief Addon Ordnerpfad
	 *
	 *  Liefert den Ordnerpfad des Addons
	 *
	 *  @param (string) 
	 */
	public static function getAddonPath(){
		global $REX;
		
		return $REX['INCLUDE_PATH'] . '/addons/square_shop';
	}
	
	/** @brief Addon-Daten Ordnerpfad
	 *
	 *  Liefert den Ordnerpfad der Daten des Addons
	 *  in dem Instanz spezifische Daten abgelegt werden,
	 *  die nicht zum Source-Files gehören.
	 *
	 *  @param (string) 
	 */
	public static function getAddonDataPath(){
		global $REX;
		
		return $REX['INCLUDE_PATH'] . '/data/addons/square_shop';
	}
	
	
	/** @brief Shop-Betreiber Setting Value
	 *
	 *  Liefert Backend Setting Value vom Shop-Betreiber
	 *  die er unter dem Register "Einstellungen" gespeichert hat
	 *
	 *  @param (string) $key
	 *  @return (string) $value
	 */
	public static function getSetting($key){
		global $REX;
		
		return $REX['ADDON']['square_shop']['settings'][$key];
	}
	
	/** @brief Formular Daten holen
	 *
	 *  Überprüft ob $_POST Array für die gewünschte FormId
	 *  vorhanden ist und liefert sie zurück
	 *
	 *  @param (string) $formId
	 *  @return (array) User Inputs von einem Formular
	 */
	public static function getPostByFormId($formId){
		if($_POST['SSForm'][$formId]){
			return SSHelper::cleanInput($_POST['SSForm'][$formId]);
		}
		return null;
	}
	
	/** @brief User Input Values bereinigen
	 *
	 *  Bereinigt $value und liefert sie zurück.
	 *  Mit dieser Bereinigung wird eine
	 *  mögliche SQL Injection Attack beseitigt.
	 *
	 *  @param (string|array) $value
	 *  @return (string|array): bereinigte Value(s)
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
			$value = htmlspecialchars($value, ENT_IGNORE, 'utf-8');
			$value = strip_tags($value);
			$value = stripslashes($value);
			$value = mysql_real_escape_string($value);
			return $value;
			//return mysql_real_escape_string(strip_tags(trim($value)), $link);
		}
	}
	
	/** @brief Value nach Typ überprüfen
	 *
	 *  Überprüft ob die angegebene $Value
	 *  dem Typ enstpricht
	 *
	 *  @param (string) $type: z.B. email
	 *  @param (string) $value: z.B. example@domain.com
	 *  @return bool
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
	
	/** @brief Formular Felder Setting generien
	 *
	 *  Formular Input Felder Einstellungen werden generiert
	 *  um aus diese Einstellungen Formular-Felder (Html) zu generieren.
	 *
	 *  @param (string) $formId: Form ID
	 *  @param (string) $table: Tabellenname
	 *  @param (string) $show_in: siehe SSSchema
	 *  @return bool
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
				$settingsByShowIn = $f['input_settings_by_show_in'][$show_in];
				$settingsByShowIn = is_array($settingsByShowIn)?$settingsByShowIn:array($settingsByShowIn);
				$settings = array_merge($settings, $settingsByShowIn);
				$label_values = array();
				foreach($settings['values'] as $v){
					//$label_values[] = self::i18n($formId.'_label_'.$name.'_'.$v);
					if(isset($settings['label']) and strlen($settings['label'])){
						$label_values[] = self::i18n('label_'.$settings['label'].'_'.$v);
					}else{
						$label_values[] = self::i18n('label_'.$name.'_'.$v);
					}
				}
				/*
				$label = self::i18n($formId.'_label_'.$name);
				if(isset($settings['label']) and strlen($settings['label'])){
					$label = self::i18n($formId.'_label_'.$settings['label']);
				}
				*/
				if(isset($settings['label']) and strlen($settings['label'])){
					$label = self::i18n('label_'.$settings['label']);
				}else{
					$label = self::i18n('label_'.$name);
				}
				$label_equalto = '';
				if($settings['equalto']){
					$label_equalto = self::i18n('label_'.$settings['equalto']);
				}
				$properties[] = array(
					'name' => $name
					, 'label' => $label
					, 'label_equalto' => $label_equalto
					, 'values' => $settings['values']
					, 'value_type' => $settings['type']
					, 'label_values' => $label_values
					, 'required' => $settings['required']
					, 'max' => $settings['max']
					, 'min' => $settings['min']
					, 'type' => $f['input']
					, 'equalto' => $settings['equalto']
				);
			}
		}
		return $properties;
	}
	
	/** @brief Hash
	 *
	 *  Hash Wert generieren
	 *
	 *  @return string
	 */
	function generateHash(){
		$result = "";
		$charPool = '0123456789abcdefghijklmnopqrstuvwxyz';
		for($p = 0; $p<15; $p++)
			$result .= $charPool[mt_rand(0,strlen($charPool)-1)];
		return sha1(md5(sha1($result)));
	}
	
	/** @brief Formular Inputs überprüfen
	 *
	 *  Die vom Käufer eingegebene Daten werden auf Richtigkeit
	 *  überprüft und Fehler zurückgegeben.
	 *
	 *  @param (string) $formId: Form ID
	 *  @param (string) $table: Tabellenname
	 *  @param (string) $show_in: siehe SSSchema
	 *  @param (string) $values: Werte die der Käufer eingegeben hat
	 *  @return bool
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
			
			$min_value = (float)$settings['min_value'];
			$max_value = (float)$settings['max_value'];
			
			if($required and strlen(trim($value)) == 0 or strlen(trim($value)) > 0){
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
				if($min_value and (float)$value < $min_value){
					$errors[$name]['min_value'] = true;
				}
				if($max_value and (float)$value > $max_value){
					$errors[$name]['max_value'] = true;
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
		}
		return $errors;
	}
}

