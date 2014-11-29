<?php
class SSCustomerRegisterController {
	const ACTION_REGISTER = 'register';
	
	// Singleton --> Session Objekt
	private $session;
	
	// POST Var Daten -> korrekt eingegebene von User
	private $formPropertiesAndValues;
	
	// Fehler
	private $formPropertyValueErrors;
	
	
	// Customer Objekt
	private $customer;
	
	// CustomerRegisterView Objekt
	private $customerRegisterView;
	
	// SSCustomerLoginController Objekt
	private $customerLoginController;
	
	
	/*
	* Konstruktor: lädt Session Instanz (Singleton)
	* Falls POST Request abgeschickt wurde, dann daten laden
	*/
    public function __construct(){
		// Session Objekt (Singleton) holen
		$this->session = SSSession::getInstance();
		
		$this->customer = new SSCustomer();
		
		$this->customerRegisterView = new SSCustomerRegisterView();
		
		$this->customerLoginController = new SSCustomerLoginController();
		
		// Form Post Vars (User input) holen
		$this->formPropertiesAndValues = SSHelper::getPostByFormId(SSCustomerRegisterView::FORM_ID);
    }
	
	/*
	* Login/Logout Funktion starten
	*/
	public function invoke(){
		if($this->customerLoginController->isUserLoggedIn()){
			$this->customerLoginController->displayView();
		}else{
			if(($this->formPropertiesAndValues['action']) == self::ACTION_REGISTER){
				if($this->isInputValid()){
					$this->registerHandler();
					$this->customerRegisterView->displaySuccess();
				}else{
					$this->customerRegisterView->displayErrors($this->formPropertyValueErrors);
					$this->displayView();
				}
			}else{
				$this->displayView();
			}
		}
	}
	
	/*
	* Formular wird abgearbeitet
	*/
	public function registerHandler(){
		switch($this->formPropertiesAndValues['action']){
			case self::ACTION_REGISTER:
				if($this->isUserRequestUnique()){
					$clearedUserInputs = $this->customer->getClearedUnknownProperties($this->formPropertiesAndValues);
					$this->customer->set($clearedUserInputs);
					$this->customer->save();
					$this->setUserRequestNoMoreUnique();
				}
				break;
		}
	}
	
	/*
	* Formular Input Dateon vom User auf Richtigkeit überpürfen
	* return bool
	*/
	public function isInputValid(){
		$this->formPropertyValueErrors = SSHelper::checkFromInputs(SSCustomer::TABLE, 'register'
												, $this->formPropertiesAndValues);
		
		if($this->customer->isEmailAlreadyExists($this->formPropertiesAndValues['email'])){
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
		$this->customerLoginController = new SSCustomerLoginController();
		$this->customerLoginController->isUserLoggedIn();
		if($this->customerLoginController->isUserLoggedIn()){
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
		
		$params['fields'] = SSHelper::getFormProperties(SSCustomerRegisterView::FORM_ID, SSCustomer::TABLE, 'register');
		
		$this->customerRegisterView->displayRegisterHtml($params);
	}
	
	/*
	* überprüft ob From-Post nicht durch einen Browswer-Refresh
	* generiert worden ist.
	* return bool: true = nicht generiert    false = generiert durch Browser-Refresh
	*/
	public function isUserRequestUnique(){
		if($this->session->get(self::ACTION_REGISTER.'SuccessUniqueId') != $this->formPropertiesAndValues['uniqueId']){
			return true;
		}
		return false;
	}
	
	/*
	* Speichert From unique id in Session
	*/
	public function setUserRequestNoMoreUnique(){
		$this->session->set(self::ACTION_REGISTER.'SuccessUniqueId', $this->formPropertiesAndValues['uniqueId']);
	}
}