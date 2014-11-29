<?php
class SSCustomerLoginController {
	const FN_EMAIL = 'email';
	const FN_PASSWORD = 'password';
	
	const ACTION_LOGIN = 'login';
	const ACTION_LOGOUT = 'logout';
	
	// Singleton --> Session Objekt
	private $session;
	
	// POST Var Daten
	private $formPropertiesAndValues;
	
	// Login / Logout View
	private $customerLoginView;
	
	
	/*
	* Konstruktor: lädt Session Instanz (Singleton)
	* Falls POST Request abgeschickt wurde, dann daten laden
	*/
    public function __construct(){
		// Session Objekt (Singleton) holen
		$this->session = SSSession::getInstance();
		
		// Objekt Login-View erstellen
		$this->customerLoginView = new SSCustomerLoginView();
		
		// Form Post Vars (User input) holen
		$this->formPropertiesAndValues = SSHelper::getPostByFormId(SSCustomerLoginView::FORM_ID);
    }
	
	/*
	* Login/Logout Funktion starten
	*/
	public function invoke(){
		// Login Logik
		$this->loginHandler();
		
		// Zeigt entweder Login oder Logout-Maske an
		$this->displayView();
	}
	
	/*
	* Benutzer anmelden oder abmelden
	* Falls Post Request durch Login-Maske ausgelöst wurde:
	* 	dann Benutzer daten in der DB abgleichen 
	* 	und in Session speichern und einloggen
	* Falls Post Request durch Logout-Maske:
	* 	dann Benutzer in Session löschen und ausloggen
	*/
	public function loginHandler(){
		switch($this->formPropertiesAndValues['action']){
			case self::ACTION_LOGIN:
				if(SSHelper::isTypeOf('email', $this->formPropertiesAndValues[self::FN_EMAIL])){
					$customer = new SSCustomer();
					if($customer->loadCustomerByEmailAndPassword($this->formPropertiesAndValues[self::FN_EMAIL], $this->formPropertiesAndValues[self::FN_PASSWORD])){
						$this->loginUser($customer->get('id'));
					}
				}
				break;
			case self::ACTION_LOGOUT:
				$this->logoutUser();
				break;
		}
	}
	
	/*
	* Falls User nicht angemeldet: Login-Maske anzeigen
	* Falls User angemeldet: Logout-Maske anzeigen
	*/
	public function displayView(){
		$param = array();
		
		if($this->isUserLoggedIn()){
			// User ist angemeldet
			$param['label_submit'] = SSHelper::i18l('Logout');
			$param['action'] = self::ACTION_LOGOUT;
			$param['message_success'] = SSHelper::i18l('LoginSuccess');
			
			$this->customerLoginView->displayLogoutHtml($param);
		}else{
			// User ist nicht angemeldet
			$param['action'] = self::ACTION_LOGIN;
			$param['label_email'] = SSHelper::i18l('E-Mail');
			$param['label_submit'] = SSHelper::i18l('Login');
			$param['label_password'] = SSHelper::i18l('Password');
			$param['fn_email'] = self::FN_EMAIL;
			$param['fn_password'] = self::FN_PASSWORD;
			
			$this->customerLoginView->displayLoginHtml($param);
		}
	}
	
	/*
	* Prüfen ob User angemeldet ist oder nicht
	* return bool
	*/
	public function isUserLoggedIn(){
		$userId = (int)$this->session->get('UserID');
		if($userId > 0){
			return true;
		}else{
			return false;
		}
	}
	
	/*
	* Speichert User ID in Session
	*/
	public function loginUser($userId){
		$this->session->set('UserID', $userId);
	}
	
	/*
	* löscht User ID aus dem Session
	*/
	public function logoutUser(){
		$this->session->remove('UserID');
	}
}