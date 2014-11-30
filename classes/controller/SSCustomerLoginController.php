<?php
class SSCustomerLoginController {
	const FN_EMAIL = 'email';
	const FN_PASSWORD = 'password';
	
	const ACTION_LOGIN = 'login';
	const ACTION_LOGOUT = 'logout';
	
	// Singleton --> Session Objekt
	private $session;
	
	// POST Var Daten -> korrekt eingegebene von User
	private $formPropertiesAndValues;
	
	// Fehler
	private $formPropertyValueErrors;
	
	// Login / Logout View
	private $customerLoginView;
	
	// SSCustomer
	private $customer;
	
	// bool: Fehler beim Anmeldung true / false
	private $loginError;
	
	
	/*
	* Konstruktor: lädt Session Instanz (Singleton)
	* Falls POST Request abgeschickt wurde, dann daten laden
	*/
    public function __construct(){
		// Session Objekt (Singleton) holen
		$this->session = SSSession::getInstance();
		
		$this->customer = new SSCustomer();
		
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
		$this->loginLogoutHandler();
		
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
	public function loginLogoutHandler(){
		switch($this->formPropertiesAndValues['action']){
			case self::ACTION_LOGIN:
				if($this->isInputValid()){
					$userId = $this->customer->get('id');
					$userName = $this->customer->get('firstname').' '.$this->customer->get('lastname');
					$this->loginUser($userId, $userName);
				}
				break;
			case self::ACTION_LOGOUT:
				$this->logoutUser();
				break;
		}
	}
	
	/*
	* Formular Input Daten vom User auf Richtigkeit überpürfen
	* loginError Variable auf true setzen, falls username + pw falsch
	* return bool  true = user+pw korrekt     false = user+pw falsch
	*/
	public function isInputValid(){
		$error = 0;
		
		if(!SSHelper::isTypeOf('email', $this->formPropertiesAndValues[self::FN_EMAIL])){
			$error++;
		}
		
		$email = $this->formPropertiesAndValues[self::FN_EMAIL];
		$password = $this->formPropertiesAndValues[self::FN_PASSWORD];
		if(!$this->customer->loadCustomerByEmailAndPassword($email, $password)){
			$error++;
		}
		
		$this->loginError = $error > 0 ? true : false;
		
		return !$this->loginError;
	}
	
	/*
	* Falls User nicht angemeldet: Login-Maske anzeigen
	* Falls User angemeldet: Logout-Maske anzeigen
	*/
	public function displayView(){
		$param = array();
		
		if($this->isUserLoggedIn()){
			// User ist angemeldet
			$param['action'] = self::ACTION_LOGOUT;
			$param['label_submit'] = SSHelper::i18l('Logout');
			$param['label_customer'] = $this->getLoggedInUserName();
			
			$this->customerLoginView->displayLogoutHtml($param);
		}else{
			// User ist nicht angemeldet
			$param['action'] = self::ACTION_LOGIN;
			$param['label_email'] = SSHelper::i18l('E-Mail');
			$param['label_submit'] = SSHelper::i18l('Login');
			$param['label_password'] = SSHelper::i18l('Password');
			$param['fn_email'] = self::FN_EMAIL;
			$param['fn_password'] = self::FN_PASSWORD;
			if($this->loginError){
				$param['login_error'] = SSHelper::i18l(self::ACTION_LOGIN.'_error');
			}
			
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
	* param $userId
	* param $userName
	*/
	public function loginUser($userId, $userName){
		$this->session->set('UserID', $userId);
		$this->session->set('UserName', $userName);
	}
	
	/*
	* löscht User ID aus dem Session
	*/
	public function logoutUser(){
		$this->session->remove('UserID');
		$this->session->remove('UserName');
	}
	
	/*
	* Liefert Username vom eingelogten Customer 
	* return string Username
	*/
	public function getLoggedInUserName(){
		return $this->session->get('UserName');
	}
}