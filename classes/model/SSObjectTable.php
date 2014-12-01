<?php
#
#
# SSObjectTable
# https://github.com/last-hero/square_shop
#
# (c) Gobi Selva
# http://www.square.ch
#
# Diese Klasse dient als Parent für alle Subklassen
# die Daten aus DB modellieren
#
#

class SSObjectTable {
	// Datenbank Felder mit Values (User Input) 
	protected $propertiesAndValues;
	
	// alle (erlaubten) Datenbank Felder und deren Eigenschaften
	protected $properties;
	
	// Tabellenname
	protected $TABLE = 'SSObjectTable';
	
	// Fehlermeldungs ID für falsche Attribute
	// die nicht in der DB Tabelle vorhanden
	// oder nicht erlaubt sind zum manipulieren
	protected $ERROR_TABLE_ATTR_DIFF;
	
	protected $ERROR_TO_MANY_FOREIGN_KEYS;
	
	protected $ERROR_NO_FOREIGN_KEYS;
	
	/**
	* Konstruktor
	* initialisiert die DB Table Felder
	*/
    function __construct(){
		$this->loadPropertiesAndNames();
		if(is_array($array)) $this->set($propertiesAndValues);
    }
	
	/**
	* DB Tabellenfelder vom DB-Schema holen
	*/
	public function loadPropertiesAndNames(){
		//$this->properties = SSDBSchema::_getFields($this->TABLE, 'name');
		$this->properties = SSDBSchema::_getFields($this->TABLE);
		$this->propertiesAndValues = array();
		foreach($this->properties as $field){
			$this->propertiesAndValues[$field['name']] = null;
		}
	}
	
	/**
	* Felder raus kicken, die nicht erlaubt oder
	* in der DB-Schema definiert sind
	* param $propertiesAndValues: Felder und Values
	* return $propertiesAndValuesNew
	*/
	public function getClearedUnknownProperties($propertiesAndValues){
		$propertyNames = SSDBSchema::_getFieldsAsSingleArray($this->TABLE, array('name'));
		$propertiesAndValuesNew = array();
		foreach($propertiesAndValues as $key => $val){
			if(in_array($key, $propertyNames)){
				$propertiesAndValuesNew[$key] = $val;
			}
		}
		return $propertiesAndValuesNew;
	}
	
	/**
	* Prüfen nach Existenz von DB-Table Felder
	* param $keys: Attributname(n) [string|array]
	* return bool: mind. 1 Feld nicht existiert dann false
	*/
	public function doPropertiesExist($keys){
		if(is_string($keys))$keys = array($keys=>$keys);
		if(SSHelper::array_keys_exists($keys, $this->propertiesAndValues)){
			return true;
		}
		return false;
	}
	
	
	/**
	* Getter für DB-Table Felder Value
	* param $key: Feldname
	* return string
	*/
	public function get($key){
		if($this->doPropertiesExist($key)){
			return $this->propertiesAndValues[$key];
		}
		return null;
	}
	
	/**
	* Setter für DB-Table Felder Value
	* param $key: DB-Table Feldname
	* param $val: Wert
	*/
	public function set($keyOrData, $val = ''){
		if(is_array($keyOrData)){
			$data = $keyOrData;
			if($this->doPropertiesExist($data)){
				$this->propertiesAndValues = $data;
			}else{
				throw new SSException('Table '.$this->TABLE.' Attr is/are different', $this->ERROR_TABLE_ATTR_DIFF);
			}
		}else{
			$key = $keyOrData;
			if($this->doPropertiesExist($key)){
				$this->propertiesAndValues[$key] = $val;
			}else{
				throw new SSException('Table '.$this->TABLE.' Attr is/are different', $this->ERROR_TABLE_ATTR_DIFF);
			}
		}
	}
	
	/**
	* Die DB-Table Felder-Values in DB speichern
	*/
	public function save(){
		if((int)$this->get('id') > 0){
			echo 'called: save() FOR UPDATE;';
		}else{
			
			// Verschlüsseln --> z.B. Passwort
			$dbProperties = SSDBSchema::_getFields($this->TABLE, null, array('sql_settings' => 'encrypt'));
			foreach($this->propertiesAndValues as $name => $value){
				foreach($dbProperties as $dbProperty){
					if($name == $dbProperty['name']){
						$encrypt = $dbProperty['sql_settings']['encrypt'];
						if($encrypt){
							$this->propertiesAndValues[$name] = hash($encrypt, $value);
						}
					}
				}
			}
			
			$query = SSDBSQL::_getSqlInsertQuery($this->propertiesAndValues, $this->TABLE);
			$res = SSDBSQL::executeSql($query);
		}
	}
	
	/**
	* Eintrag aus der DB nach ID holen
	* und zum Objekt zuweisen mit $this->set
	* param $id
	* return boolean
	*/
	public function loadById($id){
		//$query = SSDBSQL::_getSqlDmlQuery("id = $id", $this->TABLE, SSDBSchema::SHOW_IN_DETAIL);
		//$res = SSDBSQL::executeSql($query);
		
		$tableData = SSDBSchema::_getTable($this->TABLE, true);
		$table = $tableData['name'];
		
		$where = $table.".id = $id";
		
		$res = $this->_getWhere($where);
		if(count($res) == 1){
			try{
				$result = $this->getClearedUnknownProperties($res[0]);
				$this->set($result);
				return true;
			}catch(SSException $e) {
				echo $e;
			}
		}
		return false;
	}
	
	/**
	* Eintrag aus der DB nach ID holen
	* und zum Objekt zuweisen mit $this->set
	* param $id
	* return boolean
	*/
	public function getByIds($ids){
		$tableData = SSDBSchema::_getTable($this->TABLE, true);
		$table = $tableData['name'];
		
		$ids = !is_array($ids)?array($ids):$ids;
		$where = $table.".id IN (".implode(',', $ids).")";
		
		$res = $this->_getWhere($where);
		if(count($res) > 0){
			try{
				return $res;
			}catch(SSException $e) {
				echo $e;
			}
		}
		return false;
	}
	
	/*
	* Einträge aus der DB nach ForeignID holen
	*  -> funktioniert nur wenn ein ForeignId in
	*     der Tabelle vorhanden ist
	* param $foreignId
	* param $foreignTable
	* return array|bool: Datensätze oder false
	*/
	public function getByForeignId($foreignId, $foreignTable){
		// Foreign Keys holen
		$foreignKeyNames = SSDBSchema::_getForeignKeyNamesByForeignTable($this->TABLE, $foreignTable);
		
		if(sizeof($foreignKeyNames) == 1){
			$propertyName = $foreignKeyNames[0];
		}elseif(sizeof($foreignKeyNames) > 1){
			throw new SSException('Too many Foreign Keys for Table '.$this->TABLE.'', $this->ERROR_TO_MANY_FOREIGN_KEYS);
		}else{
			throw new SSException('No Foreign Keys for Table '.$this->TABLE.'', $this->ERROR_NO_FOREIGN_KEYS);
		}
		
		$tableData = SSDBSchema::_getTable($this->TABLE, true);
		$table = $tableData['name'];
		$res = $this->_getWhere($table.".".$propertyName." = $foreignId");
		if(count($res) > 0){
			try{
				$result = $this->getClearedUnknownProperties($res[0]);
				return $res;
			}catch(SSException $e) {
				echo $e;
			}
		}
		return false;
	}
	
	/**
	* Datensätze aus der DB holen
	* param $where: Where Klausel
	* param $show_in: 
	* return (Array) $res: 1 oder mehrere Einträge aus Datenbank
	*/
	protected function _getWhere($where, $show_in = null){
		if(!$show_in){
			$show_in = SSDBSchema::SHOW_IN_DETAIL;
		}
		$query = SSDBSQL::_getSqlDmlQuery($where, $this->TABLE, SSDBSchema::SHOW_IN_DETAIL);
		$res = SSDBSQL::executeSql($query);
		return $res;
	}
}