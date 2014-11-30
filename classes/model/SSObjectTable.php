<?php
class SSObjectTable {
	protected $propertiesAndValues;
	protected $properties;
	
	protected $TABLE = 'SSObjectTable';
	protected $ERROR_TABLE_ATTR_DIFF;
	
	/*
	* Konstruktor
	*/
    function __construct(){
		$this->loadPropertiesAndNames();
		if(is_array($array)) $this->set($propertiesAndValues);
    }
	
	/*
	* Keys (Attribute) holen von SSDBSchema
	*/
	public function loadPropertiesAndNames(){
		//$this->properties = SSDBSchema::_getFields($this->TABLE, 'name');
		$this->properties = SSDBSchema::_getFields($this->TABLE);
		$this->propertiesAndValues = array();
		foreach($this->properties as $field){
			$this->propertiesAndValues[$field['name']] = null;
		}
	}
	
	/*
	* Formular wird abgearbeitet
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
	
	/*
	* Prüfen nach Existenz von Key(s)
	* param $keys: Attributname(n) [string|array]
	*/
	public function doPropertiesExist($keys){
		if(is_string($keys))$keys = array($keys=>$keys);
		if(SSHelper::array_keys_exists($keys, $this->propertiesAndValues)){
			return true;
		}
		return false;
	}
	
	
	/*
	* Customer Daten holen
	* param $key: Attributname
	*/
	public function get($key){
		if($this->doPropertiesExist($key)){
			return $this->propertiesAndValues[$key];
		}
		return null;
	}
	
	/*
	* Customer Daten setzen
	* param $key: Attributname
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
	
	/*
	* Customer wird in DB gespeichert
	*/
	public function save(){
		if((int)$this->get('id') > 0){
			echo 'called: save() FOR UPDATE;';
		}else{
			
			// Verschlüsseln --> z.B. Passwort
			$dbProperties = SSDBSchema::_getFields(SSCustomer::TABLE, null, array('sql_settings' => 'encrypt'));
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
	
	/*
	* Customer nach ID laden
	* param $id: Customer ID
	* return boolean
	*/
	public function loadById($id){
		//$query = SSDBSQL::_getSqlDmlQuery("id = $id", $this->TABLE, SSDBSchema::SHOW_IN_DETAIL);
		//$res = SSDBSQL::executeSql($query);
		$tableData = SSDBSchema::_getTable($this->TABLE, true);
		$table = $tableData['name'];
		$res = $this->_getWhere($table.".id = $id");
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
	
	/*
	* Artikel Daten aus Datenbank holen
	* param $where: Where Klausel
	* param $show_in: 
	* return (Array) $res: Customers oder Customer Einträge aus Datenbank
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

