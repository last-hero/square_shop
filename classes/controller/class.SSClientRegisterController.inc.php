<?php
class SSClientRegisterController {
	const ACTION_REGISTER = 'register';
	
	// Singleton --> Session Objekt
	private $session;
	
	// POST Var Daten
	private $formPropertiesAndValues;
	
	
	private $client;
	
	
	/*
	* Konstruktor: lädt Session Instanz (Singleton)
	* Falls POST Request abgeschickt wurde, dann daten laden
	*/
    public function __construct(){
		// Session Objekt (Singleton) holen
		$this->session = SSSession::getInstance();
		
		$this->client = new SSClient();
		
		// Form Post Vars (User input) holen
		$this->formPropertiesAndValues = SSHelper::getPostByFormId(SSClientRegisterView::FORM_ID);
    }
	
	/*
	* Login/Logout Funktion starten
	*/
	public function invoke(){
		// wenn User nicht angemeldet ist
		if(!$this->isUserLoggedIn()){
			if($this->isInputValid()){
				$this->handleRegister();
			}else{
				$this->displayView();
			}
		}
	}
	
	/*
	* Formular wird abgearbeitet
	*/
	public function handleRegister(){
		switch($this->formPropertiesAndValues['action']){
			case self::ACTION_REGISTER:
					$clearedUserInputs = $this->client->getClearedUnknownProperties($this->formPropertiesAndValues);
					$this->client->set($clearedUserInputs);
					$this->client->save();
				break;
		}
	}
	
	/*
	* Formular wird abgearbeitet
	*/
	public function isInputValid(){
		if($this->client->isEmailAlreadyExists($this->formPropertiesAndValues['email'])){
			return false;	
		}
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
		$this->clientLoginController = new SSClientLoginController();
		$this->clientLoginController->isUserLoggedIn();
		if($this->clientLoginController->isUserLoggedIn()){
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