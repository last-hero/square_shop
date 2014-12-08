<?php
/** @file SSCustomerRegisterController.php
 *  @brief Registrieren - Controller
 *
 *  Mit dieser Klasse wird das Registrieren ermöglicht
 *
 *  @author Gobi Selva
 *  @author http://www.square.ch
 *  @author https://github.com/last-hero/square_shop
 */
class SSCustomerRegisterController extends SSController{
	/**
	 * Jedes Formular hat ein Action
	 * zum identifizieren, um welche
	 * Aktion ausgeführt werden soll.
	 * Hier wird "register" d.h. ein
	 * Benutzer schickt ein Request, 
	 * um sich anzumelden.
	 */
	const ACTION_REGISTER = 'register';
	
	/**
	 * @see SSCustomerRegisterView::FORM_ID
	 */
	protected $FORM_ID = SSCustomerRegisterView::FORM_ID;
	
	/**
	 * @see SSArticle::TABLE
	 */
	protected $TABLE = SSCustomer::TABLE;
	
	/**
	 * @see SSDBSchema::SHOW_IN_REGISTER
	 */
	protected $SHOW_IN = SSDBSchema::SHOW_IN_REGISTER;
	
	/**
	 * Ein Customer-Objekt
	 * @see Customer
	 */
	private $customer;
	
	/**
	 * Ein CustomerRegisterView-Objekt
	 * @see CustomerRegisterView
	 */
	private $customerRegisterView;
	
	/**
	 * Ein SSCustomerLoginController-Objekt
	 * @see SSCustomerLoginController
	 */
	private $customerLoginController;
	
	/** @brief Initialisierung
	 *
	 *  Erstellen der benötigten Objekte.
	 *  Customer Daten, RegistrierungsMaske
	 *  und Login/Logout Maske.
	 */
    protected function init(){
		$this->customer = new SSCustomer();
		
		$this->customerRegisterView = new SSCustomerRegisterView();
		
		$this->customerLoginController = new SSCustomerLoginController();
    }
	
	/*
	* Registrieren Starten
	*/
	public function invoke(){
		if($this->customerLoginController->isUserLoggedIn()){
			$this->customerLoginController->displayView();
		}else{
			if(($this->formPropertiesAndValues['action']) == self::ACTION_REGISTER){
				if($this->isInputValid()){
					$this->registerHandler();
					$this->customerRegisterView->displaySuccessMessage(
						SSHelper::i18l(SSCustomerRegisterView::FORM_ID.'_success_text')
					);
				}else{
					$this->displayView();
				}
			}else{
				$this->displayView();
			}
		}
	}
	
	/*
	* Formular wird abgearbeitet
	* Benutzer speichern falls alle User-Inputs valid
	*/
	public function registerHandler(){
		switch($this->formPropertiesAndValues['action']){
			case self::ACTION_REGISTER:
				if($this->isUserRequestUnique()){
					$clearedUserInputs = $this->customer->getClearedUnknownProperties($this->formPropertiesAndValues);
					$this->customer->set($clearedUserInputs);
					$this->customer->save();
					$this->userRequestIsNotMoreUnique();
				}
				break;
		}
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
		
		$params['formPropertiesAndValues'] = $this->formPropertiesAndValues;
		
		$params['formPropertyValueErrors'] = $this->formPropertyValueErrors;
		
		
		$params['label_errors'] = array();
		foreach($params['formPropertyValueErrors'] as $f){
			foreach($f as $name => $val){
				$params['label_errors'][$name] = SSHelper::i18l('label_error_'.$name);
			}
		}
		
		$params['fields'] = SSHelper::getFormProperties(SSCustomerRegisterView::FORM_ID, SSCustomer::TABLE, 'register');
		
		$this->customerRegisterView->displayFormHtml($params);
	}
}