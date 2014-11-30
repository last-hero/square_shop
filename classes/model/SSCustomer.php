<?php
#
#
# SSCustomer
# https://github.com/last-hero/square_shop
#
# (c) Gobi Selva
# http://www.square.ch
#
# Diese Klasse dient für das Modellieren von
# Käufer Daten
#
#

class SSCustomer extends SSObjectTable{
	// Tabellenname
	const TABLE = 'customer';
	protected $TABLE = self::TABLE;
	
	// Fehlermeldungs ID für falsche Feldername
	// die nicht in der DB Tabelle vorhanden
	// oder nicht erlaubt sind zu manipulieren
	const ERROR_TABLE_ATTR_DIFF = '6001';
	protected $ERROR_TABLE_ATTR_DIFF = self::ERROR_TABLE_ATTR_DIFF;
	
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
		
		$res = $this->_getWhere("email = '$email' AND password = '$password'");
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
		$res = $this->_getWhere("email = '$email'");
		if(!empty($res)){
			return true;
		}
		return false;
	}
}