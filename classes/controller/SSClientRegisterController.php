<?php
class SSClientRegisterController {
	const ACTION_REGISTER = 'register';
	
	// Singleton --> Session Objekt
	private $session;
	
	// POST Var Daten
	private $formPropertiesAndValues;
	
	// POST Var Daten -> korrekt eingegebene von User
	private $formPropertyValueErrors;
	
	
	// Client Objekt
	private $client;
	
	// ClientRegisterView Objekt
	private $clientRegisterView;
	
	
	/*
	* Konstruktor: lädt Session Instanz (Singleton)
	* Falls POST Request abgeschickt wurde, dann daten laden
	*/
    public function __construct(){
		// Session Objekt (Singleton) holen
		$this->session = SSSession::getInstance();
		
		$this->client = new SSClient();
		
		$this->clientRegisterView = new SSClientRegisterView();
		
		// Form Post Vars (User input) holen
		$this->formPropertiesAndValues = SSHelper::getPostByFormId(SSClientRegisterView::FORM_ID);
    }
	
	/*
	* Login/Logout Funktion starten
	*/
	public function invoke(){
		if(($this->formPropertiesAndValues['action']) == self::ACTION_REGISTER){
			if($this->isInputValid()){
				$this->handleRegisterLogic();
				$this->clientRegisterView->displaySuccess();
			}else{
				$this->clientRegisterView->displayErrors($this->formPropertyValueErrors);
				$this->displayView();
			}
		}else{
			$this->displayView();
		}
	}
	
	/*
	* Formular wird abgearbeitet
	*/
	public function handleRegisterLogic(){
		switch($this->formPropertiesAndValues['action']){
			case self::ACTION_REGISTER:
				if($this->isUserRequestUnique()){
					$clearedUserInputs = $this->client->getClearedUnknownProperties($this->formPropertiesAndValues);
					$this->client->set($clearedUserInputs);
					$this->client->save();
					$this->setUserRequestNoMoreUnique();
				}
				break;
		}
	}
	
	public function isInputValid(){
		$this->formPropertyValueErrors = SSHelper::checkFromInputs(SSClient::TABLE, 'register'
												, $this->formPropertiesAndValues);
		
		if($this->client->isEmailAlreadyExists($this->formPropertiesAndValues['email'])){
			$this->formPropertyValueErrors['email']['exists'] = 1;
		}
		if(sizeof($this->formPropertyValueErrors) > 0){
			return false;
		}
		return true;
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
		$params = array();
		$params['label_submit'] = SSHelper::i18l('Abschicken');
		$params['action'] = self::ACTION_REGISTER;
		$params['message_success'] = SSHelper::i18l('RegisterSuccess');
		
		$params['formPropertiesAndValues'] = $this->formPropertiesAndValues;
		
		$params['formPropertyValueErrors'] = $this->formPropertyValueErrors;
		
		$params['fields'] = SSHelper::getFormProperties(SSClientRegisterView::FORM_ID, SSClient::TABLE, 'register');
		
		$this->clientRegisterView->displayRegisterHtml($params);
	}
	
	public function isUserRequestUnique(){
		if($this->session->get(self::ACTION_REGISTER.'SuccessUniqueId') != $this->formPropertiesAndValues['uniqueId']){
			return true;
		}
		return false;
	}
	public function setUserRequestNoMoreUnique(){
		$this->session->set(self::ACTION_REGISTER.'SuccessUniqueId', $this->formPropertiesAndValues['uniqueId']);
	}
}