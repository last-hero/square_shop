<?php
class SSClientRegisterController {
	const ACTION_REGISTER = 'register';
	
	// Singleton --> Session Objekt
	private $session;
	
	// POST Var Daten
	private $formPropertiesAndValues;
	
	
	/*
	* Konstruktor: lädt Session Instanz (Singleton)
	* Falls POST Request abgeschickt wurde, dann daten laden
	*/
    public function __construct(){
		$this->session = SSSession::getInstance();
		
		// hole Daten von Post Vars (User Input)
		if($_POST['SSForm'][SSClientRegisterView::FORM_ID]){
			$this->formPropertiesAndValues = SSHelper::cleanInput($_POST['SSForm'][SSClientRegisterView::FORM_ID]);
		}
    }
	
	/*
	* Login/Logout Funktion starten
	*/
	public function invoke(){
		// wenn User nicht angemeldet ist
		if(!$this->isUserLoggedIn()){
			if($this->isInputValid()){
				$this->handleForm();
			}else{
				$this->displayView();
			}
		}
	}
	
	/*
	* Formular wird abgearbeitet
	*/
	public function handleForm(){
		switch($this->formPropertiesAndValues['action']){
			case self::ACTION_REGISTER:
				if(true){
					$client = new SSClient();
					$client->set($this->getClearedUnknownProperties($this->formPropertiesAndValues));
					//$client->save();
				}
				break;
		}
	}
	
	/*
	* Formular wird abgearbeitet
	*/
	public function getClearedUnknownProperties($propertiesAndValues){
		$propertyNames = SSDBSchema::_getFields(SSClient::TABLE, array('name'), array('show_in'=>SSDBSchema::SHOW_IN_REGISTER));
		
		$propertiesAndValuesNEW = array();
		foreach($propertiesAndValues as $key => $val){
			if(array_key_exists($key, $propertyNames)){
			 //$propertiesAndValuesNEW	
			}
		}
		
		if($this->formPropertiesAndValues['action'] == self::ACTION_REGISTER){
			return true;
		}
		return false;
	}
	
	/*
	* Formular wird abgearbeitet
	*/
	public function isInputValid(){
		if($this->formPropertiesAndValues['action'] == self::ACTION_REGISTER){
			return true;
		}
		return false;
	}
	
	/*
	* Prüfen ob User angemeldet ist oder nicht
	* return bool
	*/
	public function isUserLoggedIn(){
		$clientLoginController = new SSClientLoginController();
		$clientLoginController->isUserLoggedIn();
		if($clientLoginController->isUserLoggedIn()){
			return true;
		}
		return false;
	}
	
	/*
	* Falls User nicht angemeldet: Login-Maske anzeigen
	* Falls User angemeldet: Logout-Maske anzeigen
	*/
	public function displayView(){
		// User ist nicht angemeldet
		$SSClientRegisterView = new SSClientRegisterView();
		$param = array();
		$param['label_submit'] = SSHelper::i18l('Abschicken');
		$param['action'] = self::ACTION_REGISTER;
		$param['message_success'] = SSHelper::i18l('RegisterSuccess');
		
		$param['formPropertiesAndValues'] = $this->formPropertiesAndValues;
		
		$SSClientRegisterView->displayRegisterHtml($param);
	}
}