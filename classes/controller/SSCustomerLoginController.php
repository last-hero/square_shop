<?php
/** @file SSCustomerLoginController.php
 *  @brief Login/Logout Verwalten
 *
 *  Mit dieser Klasse wird das Login- / Logout-Verfahren
 *  vewaltet
 *
 *  @author Gobi Selva
 *  @author http://www.square.ch
 *  @author https://github.com/last-hero/square_shop
 */

class SSCustomerLoginController extends SSController{
	/**
	 * siehe Parent
	 */
	protected $FORM_ID = SSCustomerLoginView::FORM_ID;
	/**
	 * siehe Parent
	 */
	protected $TABLE = SSCustomer::TABLE;
	/**
	 * siehe Parent
	 */
	protected $SHOW_IN = SSDBSchema::SHOW_IN_LOGIN;
	
	/**
	 * Formular Action Variable:
	 * Der User versucht anzumelden.
	 */
	const ACTION_LOGIN = 'login';
	
	/**
	 * Formular Action Variable:
	 * Der User versucht abzumelden.
	 */
	const ACTION_LOGOUT = 'logout';
	
	/**
	 * Login / Logout View
	 */
	private $customerLoginView;
	
	/**
	 * SSCustomer
	 */
	private $customer;
	
	/**
	 * bool: Fehler beim Anmeldung true / false
	 */
	private $loginError;
	
	/** @brief Initialisierung
	 *
	 *  Wird von der Parent Konstruktor
	 *  aufgerufen.
	 *
	 *  Benötigte Objekte erstellen.
	 *
	 */
    protected function init(){
		$this->customer = new SSCustomer();
		// Objekt Login-View erstellen
		$this->customerLoginView = new SSCustomerLoginView();
    }
	
	/** @brief Start
	 *
	 *  Login/Logout Funktion starten
	 *
	 */
	public function invoke(){
		// Login Logik
		$this->loginLogoutHandler();
		
		// Zeigt entweder Login oder Logout-Maske an
		$this->displayView();
	}
	
	/** @brief Login / Logout Logik
	 *
	 *  Benutzer anmelden oder abmelden!
	 *
	 *  Falls Post Request durch Login-Maske ausgelöst wurde:
	 *  	dann Benutzer daten in der DB abgleichen
	 *  	und in Session speichern und einloggen
	 *
	 *  Falls Post Request durch Logout-Maske:
	 *  	dann Benutzer in Session löschen und ausloggen
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
	
	/** @brief Benutzereingabe überprüfen
	 *
	 *  Formular Input Dateon vom User auf Richtigkeit überprüfen
	 *
	 *  loginError Variable auf true setzen, falls username + pw falsch
	 *
	 *  @return bool  true = user+pw korrekt     false = user+pw falsch
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
	
	/** @brief Login / Logout Maske anzeigen
	 *
	 *  Falls User nicht angemeldet: Login-Maske anzeigen
	 *
	 *  Falls User angemeldet: Logout-Maske anzeigen
	 */
	public function displayView(){
		$params = array();
		if($this->isUserLoggedIn()){
			$params['action'] = self::ACTION_LOGOUT;
			$params['label_submit'] = SSHelper::i18n('label_logout');
			$params['label_customer'] = $this->getLoggedInUserName();
			$this->customerLoginView->displayLogoutHtml($params);
		}else{
			$params['action'] = self::ACTION_LOGIN;
			$params['fields'] = SSHelper::getFormProperties(
				SSCustomerLoginView::FORM_ID
				, SSCustomer::TABLE
				, SSDBSchema::SHOW_IN_LOGIN
			);
			$params['label_submit'] = SSHelper::i18n('label_login');
			if($this->loginError){
				$this->customerLoginView->displayErrorMessage(
					SSHelper::i18n(self::ACTION_LOGIN.'_error')
				);
			}
			$this->customerLoginView->displayFormHtml($params);
		}
	}
	
	/** @brief User ID holen
	 *
	 *  User ID vom angemeldeten Benutzer holen
	 *
	 *  @return bool
	 */
	public function getLoggedInUserId(){
		$userId = (int)$this->session->get('UserID');
		return $userId;
	}
	
	/** @brief Ist Benutzer angemeldet
	 *
	 *  Prüfen ob User angemeldet ist oder nicht
	 *
	 *  @return bool
	 */
	public function isUserLoggedIn(){
		$userId = (int)$this->session->get('UserID');
		if($userId > 0){
			return true;
		}else{
			return false;
		}
	}
	
	/** @brief Benutzer anmelden
	 *
	 *  Speichert User ID in Session
	 */
	public function loginUser($userId, $userName){
		$this->session->set('UserID', $userId);
		$this->session->set('UserName', $userName);
	}
	
	/** @brief Benutzer abmelden
	 *
	 *  löscht User ID aus dem Session
	 */
	public function logoutUser(){
		$this->session->remove('UserID');
		$this->session->remove('UserName');
	}
	
	/** @brief Name vom User
	 *
	 *  Liefert Vorname/Name vom eingelogten Customer 
	 *
	 *  @return string
	 */
	public function getLoggedInUserName(){
		return $this->session->get('UserName');
	}
}