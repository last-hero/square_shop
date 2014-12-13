<?php
/** @file SSCustomerRegisterController.php
 *  @brief Registrieren - Controller
 *
 *  Mit dieser Klasse wird das Registrieren ermöglicht
 *
 *  @todo Registrierung verifizieren: So
 *  dass der User sich erst duch öffne der Besätitungsurl,
 *  welche per Mail an User verschickt wurde,
 *  sich auf der Seite anmelden kann.
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
	
	/**
	 * Eine Hilfsvariable (Boolean) um das
	 * Registrierungsformular anzuzeigen.
	 */
	protected $showRegisterForm;
	
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
		
		$this->showRegisterForm = true;
    }
	
	/** @brief Registrieren Starten
	 *
	 *  Registrierungsmaske wird eingeblendet.
	 *  Sobald das Formular ausgefüllt und abgeschickt
	 *  wird, werden die eingegebene Daten überprüft
	 *  (z.B. ob die E-Mail Adresse den Norm entspricht)
	 *  und in der DB gespeichert. Am Schluss erscheint
	 *  eine Bestätigungsmeldung.
	 */
	public function invoke(){
		if($this->customerLoginController->isUserLoggedIn()){
			$this->customerLoginController->displayView();
		}else{
			$this->registerHandler();
			$this->messageHandler();
			$this->viewHandler();
		}
	}
	
	/** @brief Formular Daten speichern
	 *
	 *  Die Benutzerdaten, welche mit dem 
	 *  Registrierungsformular ausgefüllt wurden,
	 *  werden in der Datenbank gespeichert.
	 *
	 *  Es wird vorher noch überprüft ob der
	 *  POST-Request durch Browser-Refresh oder
	 *  durch Betätigen der Submit-Button
	 *  abgeschickt wurde.
	 */
	public function registerHandler(){
		switch($this->formPropertiesAndValues['action']){
			case self::ACTION_REGISTER:
				if($this->isUserRequestUnique()){
					if($this->isInputValid()){
						$clearedUserInputs = $this->customer->getClearedUnknownProperties($this->formPropertiesAndValues);
						$this->customer->set($clearedUserInputs);
						$this->customer->save();
						$this->userRequestIsNotMoreUnique();
						$this->showRegisterForm = false;
					}
				}else{
					$this->showRegisterForm = false;
				}
				$this->showMessage = true;
				break;
		}
	}
		
	/** @brief Prüfen ob User eingeloggt ist
	 *
	 *  @return bool
	 */
	public function isUserLoggedIn(){
		$this->customerLoginController = new SSCustomerLoginController();
		$this->customerLoginController->isUserLoggedIn();
		if($this->customerLoginController->isUserLoggedIn()){
			return true;
		}
		return false;
	}
		
	/** @brief Registrierungsmaske anzeigen
	 *
	 *  Das Formular für das Registrieren anzeigen
	 */
	public function displayView(){
		$params = array();
		$params['label_submit'] = SSHelper::i18n('label_submit');
		$params['action'] = self::ACTION_REGISTER;
		
		$params['formPropertiesAndValues'] = $this->formPropertiesAndValues;
		$params['formPropertyValueErrors'] = $this->formPropertyValueErrors;
		$params['show_required_fields_info'] = true;
		
		/*
		$params['label_errors'] = array();
		foreach($params['formPropertyValueErrors'] as $f){
			foreach($f as $name => $val){
				$params['label_errors'][$name] = SSHelper::i18n('label_error_'.$name);
			}
		}
		*/
		
		$params['fields'] = SSHelper::getFormProperties(
			SSCustomerRegisterView::FORM_ID
			, SSCustomer::TABLE
			, SSDBSchema::SHOW_IN_REGISTER
		);		
		
		$this->customerRegisterView->displayFormHtml($params);
	}
	
	/** @brief Formular-Anzeige-Handler
	 *
	 *  Das RegistrierungsFomrular wird angezeigt
	 */
	public function viewHandler(){
		if($this->showRegisterForm){
			$this->displayView();
		}
	}
}