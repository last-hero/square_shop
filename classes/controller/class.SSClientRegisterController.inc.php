<?php
class SSClientRegisterController {
	const ACTION_REGISTER = 'register';
	
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
		if($_POST['SSForm'][SSClientRegisterView::FORM_ID]){
			$this->form_data = SSHelper::cleanInput($_POST['SSForm'][SSClientRegisterView::FORM_ID]);
		}
    }
	
	/*
	* Login/Logout Funktion starten
	*/
	public function invoke(){
		$this->displayView();
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
		if(!$this->isUserLoggedIn()){
			// User ist nicht angemeldet
			$SSClientRegisterView = new SSClientRegisterView();
			$param = array();
			$param['label_submit'] = SSHelper::i18l('Logout');
			$param['action'] = self::ACTION_REGISTER;
			$param['message_success'] = SSHelper::i18l('LoginSuccess');
			
			$SSClientRegisterView->displayRegisterHtml($param);
		}
	}
}