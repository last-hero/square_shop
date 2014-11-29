<?php
class SSClient {
	const TABLE = 'client';
	const TABLE_FULL = 'square_shop_client';
	
	const ERROR_CLIENT_ATTR_DIFF = '6001';
	
	private $propertiesAndValues;
	private $properties;
	
	/*
	* Konstruktor
	*/
    function __construct(array $propertiesAndValues = null){
		$this->loadPropertiesAndNames();
		if(is_array($array)) $this->set($propertiesAndValues);
    }
	
	/*
	* Keys (Attribute) holen von SSDBSchema
	*/
	public function loadPropertiesAndNames(){
		//$this->properties = SSDBSchema::_getFields(self::TABLE, 'name');
		$this->properties = SSDBSchema::_getFields(self::TABLE);
		$this->propertiesAndValues = array();
		foreach($this->properties as $field){
			$this->propertiesAndValues[$field['name']] = null;
		}
	}
	
	/*
	* Formular wird abgearbeitet
	*/
	public function getClearedUnknownProperties($propertiesAndValues){
		$propertyNames = SSDBSchema::_getFieldsAsSingleArray(self::TABLE, array('name'));
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
	* Client Daten holen
	* param $key: Attributname
	*/
	public function get($key){
		if($this->doPropertiesExist($key)){
			return $this->propertiesAndValues[$key];
		}
		return null;
	}
	
	/*
	* Client Daten setzen
	* param $key: Attributname
	* param $val: Wert
	*/
	public function set($keyOrData, $val = ''){
		if(is_array($keyOrData)){
			$data = $keyOrData;
			
			if($this->doPropertiesExist($data)){
				$this->propertiesAndValues = $data;
			}else{
				throw new SSException('Client Attr is/are different', self::ERROR_CLIENT_ATTR_DIFF);
			}
		}else{
			$key = $keyOrData;
			if($this->doPropertiesExist($key)){
				$this->propertiesAndValues[$key] = $val;
			}else{
				throw new SSException('Client Attr is different', self::ERROR_CLIENT_ATTR_DIFF);
			}
		}
	}
	
	/*
	* Client wird in DB gespeichert
	*/
	public function save(){
		if((int)$this->get('id') > 0){
			echo 'called: save() FOR UPDATE;';
		}else{
			
			// Verschlüsseln --> z.B. Passwort
			$dbProperties = SSDBSchema::_getFields(SSClient::TABLE, null, array('sql_settings' => 'encrypt'));
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
			
			$query = SSDBSQL::_getSqlInsertQuery($this->propertiesAndValues, self::TABLE);
			$res = SSDBSQL::executeSql($query);
		}
	}
	
	/*
	* Client nach ID laden
	* param $id: Client ID
	* return boolean
	*/
	public function loadClientById($id){
		//$query = SSDBSQL::_getSqlDmlQuery("id = $id", self::TABLE, SSDBSchema::SHOW_IN_DETAIL);
		//$res = SSDBSQL::executeSql($query);
		$res = $this->_getClientWhere("id = $id");
		if(count($res) == 1){
			try{
				$this->set($res[0]);
				return true;
			}catch(SSException $e) {
				echo $e;
			}
		}
		return false;
	}
	
	/*
	* Client nach E-Mail und Passwort laden
	* param $email string: Email
	* param $password string: Passwort
	* return boolean
	*/
	public function loadClientByEmailAndPassword($email, $password){
		// Passwort verschlüsseln
		$dbProperties = SSDBSchema::_getFields(SSClient::TABLE, null, array('sql_settings' => 'encrypt'));
		foreach($dbProperties as $dbProperty){
			if($dbProperty['name'] == 'password'){
				$encrypt = $dbProperty['sql_settings']['encrypt'];
				if($encrypt){
					$password = hash($encrypt, $password);
				}
			}
		}
		//$res = $this->_getClientWhere("email = '$email'");
		$res = $this->_getClientWhere("email = '$email' AND password = '$password'");
		if(count($res) == 1){
			try{
				$this->set($res[0]);
				return true;
			}catch(SSException $e) {
				echo $e;
			}
		}
		return false;
	}
	
	/*
	* Überprüft in der DB, ob Email Adresse bereits
	* vergeben ist
	* param $email: 
	* return bool
	*/
	public function isEmailAlreadyExists($email){
		$res = $this->_getClientWhere("email = '$email'");
		if(!empty($res)){
			return true;
		}
		return false;
	}
	
	/*
	* Client Daten aus Datenbank holen
	* param $where: Where Klausel
	* param $show_in: 
	* return (Array) $res: Clients oder Client Einträge aus Datenbank
	*/
	private function _getClientWhere($where, $show_in = null){
		if(!$show_in){
			$show_in = SSDBSchema::SHOW_IN_DETAIL;
		}
		$query = SSDBSQL::_getSqlDmlQuery($where, self::TABLE, SSDBSchema::SHOW_IN_DETAIL);
		$res = SSDBSQL::executeSql($query);
		
		return $res;
	}
}

