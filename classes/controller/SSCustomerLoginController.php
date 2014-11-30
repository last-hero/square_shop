<?php
#
#
# SSCustomerLoginController
# https://github.com/last-hero/square_shop
#
# (c) Gobi Selva
# http://www.square.ch
#
# Mit dieser Klasse wird das Login- / Logout-Verfahren
# vewaltet
#
#

class SSCustomerLoginController {
	// Form Action Variablen	
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
		$this->loginError = false;
		
		
		
		$this->formPropertyValueErrors = SSHelper::checkFromInputs(SSCustomer::TABLE, 'login'
											, $this->formPropertiesAndValues);
												
		$email = $this->formPropertiesAndValues['email'];
		$password = $this->formPropertiesAndValues['password'];
		if(!$this->customer->loadCustomerByEmailAndPassword($email, $password)){
			$this->formPropertyValueErrors['auth']['incorrect'] = 1;
		}
		if(sizeof($this->formPropertyValueErrors) > 0){
			$this->loginError = true;
		}
		
		return !$this->loginError;
	}
	
	/*
	* Falls User nicht angemeldet: Login-Maske anzeigen
	* Falls User angemeldet: Logout-Maske anzeigen
	*/
	public function displayView(){
		$params = array();
		
		
		if($this->isUserLoggedIn()){
			$params['action'] = self::ACTION_LOGOUT;
			$params['label_submit'] = SSHelper::i18l('label_logout');
			$params['label_customer'] = $this->getLoggedInUserName();
			$this->customerLoginView->displayLogoutHtml($params);
		}else{
			$params['action'] = self::ACTION_LOGIN;
			$params['fields'] = SSHelper::getFormProperties(SSCustomerLoginView::FORM_ID, SSCustomer::TABLE, 'login');
			$params['label_submit'] = SSHelper::i18l('label_login');
			if($this->loginError){
				$params['login_error'] = SSHelper::i18l(self::ACTION_LOGIN.'_error');
			}
			$this->customerLoginView->displayLoginHtml($params);
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