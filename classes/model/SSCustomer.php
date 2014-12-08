<?php
/** @file SSCustomer.php
 *  @brief Käufer modellieren
 *
 *  Diese Klasse dient für das Modellieren von
 *  Käufer Daten
 *
 *  @author Gobi Selva
 *  @author http://www.square.ch
 *  @author https://github.com/last-hero/square_shop
 *
 *  @bug Keine Bugs bekannt.
 */

class SSCustomer extends SSModel{
	/**
	 * @see SSModel::$TABLE
	 */
	const TABLE = 'customer';
	protected $TABLE = self::TABLE;
	
	/**
	 * @see SSModel::$ERROR_TABLE_ATTR_DIFF
	 */
	const ERROR_TABLE_ATTR_DIFF = '6001';
	protected $ERROR_TABLE_ATTR_DIFF = self::ERROR_TABLE_ATTR_DIFF;
	
	/**
	 * @see SSModel::$ERROR_TO_MANY_FOREIGN_KEYS
	 */
	const ERROR_TO_MANY_FOREIGN_KEYS = '6002';
	protected $ERROR_TO_MANY_FOREIGN_KEYS;
	
	/**
	 * @see SSModel::$ERROR_NO_FOREIGN_KEYS
	 */
	const ERROR_NO_FOREIGN_KEYS = '6003';
	protected $ERROR_NO_FOREIGN_KEYS;
	
	/** @brief Authetifizierung
	 *
	 *  Customer nach E-Mail und Passwort laden
	 *
	 *  @param (string) $email: Email
	 *  @param (string) $password: Passwort
	 *  @return bool
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
}