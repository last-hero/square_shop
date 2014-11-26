<?php
class SSClient {
	const TABLE = 'client';
	const TABLE_FULL = 'square_shop_client';
	
	const ERROR_CLIENT_ATTR_DIFF = '6001';
	
	private $data;
	private $fields;
	
	/**
	* Konstruktor
	*/
    function __construct(array $data = null){
		$this->loadKeys();
		if(is_array($array)) $this->putDataAll($data);
    }
	
	/**
	* Keys (Attribute) holen von SSDBSchema
	*/
	public function loadKeys(){
		//$this->fields = SSDBSchema::_getFields(self::TABLE, 'name');
		$this->fields = SSDBSchema::_getFields(self::TABLE);
		$this->data = array();
		foreach($this->fields as $field){
			$this->data[$field['name']] = null;
		}
	}
	
	/**
	* Prüfen nach Existenz von Key(s)
	* param $keys: Attributname(n) [string|array]
	*/
	public function isValidKeys($keys){
		if(is_string($keys))$keys = array($keys);
		if(SSHelper::array_keys_exists($keys, $this->data)){
			return true;
		}
		return false;
	}
	
	/**
	* Client Daten setzen
	* param $key: Attributname
	* param $val: Wert
	*/
	public function putData($key, $val){
		if($this->isValidKeys($key)){
			$this->data[$key] = $val;
		}
	}
	
	/**
	* Client Daten setzen (Alle --> array)
	* param $data: Array --> Attribut mit Wert
	*/
	public function putDataAll(array $data){
		if($this->isValidKeys($data)){
			$this->data = $data;
		}else{
			throw new SSException('Client Attr is/are different', self::ERROR_CLIENT_ATTR_DIFF);
		}
	}
	
	/**
	* holt Client Daten aus dem Datenbank
	* param $id: Client ID
	*/
	public function loadClientById($id){
		$query = SSDBSQL::_getSqlDmlQuery("id = $id", self::TABLE, SSDBSchema::SHOW_IN_DETAIL);
		$res = SSDBSQL::executeSql($query);
		if(count($res) == 1){
			try{
				$this->putDataAll($res[0]);
			}catch(SSException $e) {
				echo $e;
			}
		}
	}
	
	
	/**
	* Gibt die gesamte Tabelle in Form von rex_list aus
	* param $table: Tabellenname
	* param $filter_key: Felder in welche gesucht werden soll
	* param $filter_value: Suchstring zum Filtern der Lister
	*/
	private function _getClientWhere($where, $table = null, $fields = null, $default = null){
	}
	
	
	/**
	* 
	* param $email string: Email
	* param $password string: Passwort
	*/
	public function checkLogin($email, $password){
		$query = SSDBSQL::_getSqlDmlQuery("email = '$email' AND password = md5('$password')", self::TABLE, SSDBSchema::SHOW_IN_DETAIL);
		$query = SSDBSQL::_getSqlDmlQuery("email = '$email'", self::TABLE, SSDBSchema::SHOW_IN_DETAIL);
		$res = SSDBSQL::executeSql($query);
		if(is_array($res) and count($res) == 1){
			try{
				$this->putDataAll($res[0]);
			}catch(SSException $e) {
				echo $e;
			}
			return true;
		}
		return false;
	}
}

