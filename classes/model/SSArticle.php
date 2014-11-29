<?php
class SSArticle {
	const TABLE = 'article';
	const TABLE_FULL = 'square_shop_article';
	
	const ERROR_ARTICLE_ATTR_DIFF = '7001';
	
	private $propertiesAndValues;
	private $properties;
	
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
				throw new SSException('Customer Attr is/are different', self::ERROR_CUSTOMER_ATTR_DIFF);
			}
		}else{
			$key = $keyOrData;
			if($this->doPropertiesExist($key)){
				$this->propertiesAndValues[$key] = $val;
			}else{
				throw new SSException('Customer Attr is different', self::ERROR_CUSTOMER_ATTR_DIFF);
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
			
			$query = SSDBSQL::_getSqlInsertQuery($this->propertiesAndValues, self::TABLE);
			$res = SSDBSQL::executeSql($query);
		}
	}
	
	/*
	* Customer nach ID laden
	* param $id: Customer ID
	* return boolean
	*/
	public function loadCustomerById($id){
		//$query = SSDBSQL::_getSqlDmlQuery("id = $id", self::TABLE, SSDBSchema::SHOW_IN_DETAIL);
		//$res = SSDBSQL::executeSql($query);
		$res = $this->_getCustomerWhere("id = $id");
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
	* Customer nach E-Mail und Passwort laden
	* param $email string: Email
	* param $password string: Passwort
	* return boolean
	*/
	public function loadCustomerByEmailAndPassword($email, $password){
		// Passwort verschlüsseln
		$dbProperties = SSDBSchema::_getFields(SSCustomer::TABLE, null, array('sql_settings' => 'encrypt'));
		foreach($dbProperties as $dbProperty){
			if($dbProperty['name'] == 'password'){
				$encrypt = $dbProperty['sql_settings']['encrypt'];
				if($encrypt){
					$password = hash($encrypt, $password);
				}
			}
		}
		//$res = $this->_getCustomerWhere("email = '$email'");
		$res = $this->_getCustomerWhere("email = '$email' AND password = '$password'");
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
		$res = $this->_getCustomerWhere("email = '$email'");
		if(!empty($res)){
			return true;
		}
		return false;
	}
	
	/*
	* Artikel Daten aus Datenbank holen
	* param $where: Where Klausel
	* param $show_in: 
	* return (Array) $res: Customers oder Customer Einträge aus Datenbank
	*/
	private function _getArticleWhere($where, $show_in = null){
		if(!$show_in){
			$show_in = SSDBSchema::SHOW_IN_DETAIL;
		}
		$query = SSDBSQL::_getSqlDmlQuery($where, self::TABLE, SSDBSchema::SHOW_IN_DETAIL);
		$res = SSDBSQL::executeSql($query);
		
		return $res;
	}
}

