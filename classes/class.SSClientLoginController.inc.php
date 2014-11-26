<?php
class SSClientLoginController {
	const FN_EMAIL = 'email';
	const FN_PASSWORD = 'password';
	
	const ACTION_LOGIN = 'login';
	const ACTION_LOGOUT = 'logout';
	
	// Singleton --> Session Objekt
	private $session;
	
	// POST Var Daten
	private $form_data;
	
	
	/*
	* Konstruktor: lädt Session Instanz (Singleton)
	* Falls POST Request abgeschickt wurde, dann daten laden
	*/
    public function __construct(){
		$this->session = SSSession::getInstance();
		
		// hole Daten von Post Vars (User Input)
		if($_POST['SSForm'][SSClientLoginView::FORM_ID]){
			$this->form_data = SSHelper::cleanInput($_POST['SSForm'][SSClientLoginView::FORM_ID]);
		}
    }
	
	/*
	* Login/Logout Funktion starten
	*/
	public function invoke(){
		$this->checkLogin();
		$this->displayView();
	}
	
	/*
	* Benutzer anmelden oder abmelden
	* Falls Post Request durch Login-Maske ausgelöst wurde:
	* 	dann Benutzer daten in der DB abgleichen und in Session speichern, ok
	* Falls Post Request durch Logout-Maske:
	* 	dann Benutzer in Session löschen
	*/
	public function checkLogin(){
		switch($this->form_data['action']){
			case self::ACTION_LOGIN:
				if(SSHelper::isEmailValid($this->form_data[self::FN_EMAIL])){
					$client = new SSClient();
					if($client->checkLogin($this->form_data[self::FN_EMAIL], $this->form_data[self::FN_PASSWORD])){
						$this->session->set('SSClientLogin', $client);
					}
				}
				break;
			case self::ACTION_LOGOUT:
				$this->session->remove('SSClientLogin');
				break;
		}
	}
	
	/*
	* Falls User nicht angemeldet: Login-Maske anzeigen
	* Falls User angemeldet: Logout-Maske anzeigen
	*/
	public function displayView(){
		$clientLoginView = new SSClientLoginView();
		$param = array();
		
		if(self::isUser()){
			// User ist angemeldet
			$param['label_submit'] = SSHelper::i18l('Logout');
			$param['action'] = self::ACTION_LOGOUT;
			$param['message_success'] = SSHelper::i18l('LoginSuccess');
			
			$clientLoginView->displayLogoutHtml($param);
		}else{
			// User ist nicht angemeldet
			$param['action'] = self::ACTION_LOGIN;
			$param['label_email'] = SSHelper::i18l('E-Mail');
			$param['label_submit'] = SSHelper::i18l('Login');
			$param['label_password'] = SSHelper::i18l('Password');
			$param['fn_email'] = self::FN_EMAIL;
			$param['fn_password'] = self::FN_PASSWORD;
			
			$clientLoginView->displayLoginHtml($param);
		}
	}
	
	/*
	* Prüfen ob User angemeldet ist oder nicht
	* return bool
	*/
	public function isUser(){
		if(is_object($this->session->get('SSClientLogin'))){
			return true;
		}else{
			return false;
		}
	}
}