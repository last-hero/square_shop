<?php
/** @file SSController.php
 *  @brief Controller Class
 *
 *  Diese Klasse dient als Basis Controller
 *
 *  @author Gobi Selva
 *  @author http://www.square.ch
 *  @author https://github.com/last-hero/square_shop
 */
class SSController {
	/**
	 * Formular ID die hier eingesetzt wird: $_POST[SSForm][[FORM_ID]
	 */
	protected $FORM_ID;
	
	/**
	 * Datenbank Tabellenname
	 */
	protected $TABLE;
	
	/**
	 *@see SSSchema::SHOW_IN_DETAIL
	 *@see SSSchema::SHOW_IN_LIST
	 *@see SSSchema::SHOW_IN_X
	 */
	protected $SHOW_IN;
	
	/**
	 * Session Singleton Objekt
	 */
	protected $session;
	
	/**
	 * Formular Felder Name und deren Values
	 * die vom Benutzer eingegeben wurde.
	 */
	protected $formPropertiesAndValues;
	
	/**
	 * Fehlerhafte Values die vom Benutzer
	 * eingegeben wurde
	 */
	protected $formPropertyValueErrors;
	
	/**
	 * Eine Hilfsvariable (Boolean) um Meldungen
	 * anzuzeigen.
	 */
	protected $showMessage;
		
	/**
	 * Ein Objekt für das View
	 * @see SSView
	 */
	protected $view;
	
	/** @brief Konstruktor
	 *
	 *  Lädt Session Instanz (Singleton).
	 *
	 *  Falls POST Request gesendet wurde, 
	 *  dann Daten aus der POST Variable laden.
	 *
	 */
    public function __construct(){
		// Session Objekt (Singleton) holen
		$this->session = SSSession::getInstance();
		
		// Form Felder mit Values (User Input) laden aus POST Variable
		// Die Daten werden nach FORM_ID filtriert, damit Daten von
		// diese Formular geladen werden
		$this->formPropertiesAndValues = SSHelper::getPostByFormId($this->FORM_ID);
		
		$this->view = new SSView();
		
		$this->init();
    }
	
	/** @brief Initialisierung
	 *
	 *  Eine Möglichkeit für Subklassen
	 *  um Subklassen spezifische Funktionen
	 *  beim Objekt-Erstellung auszuführen
	 *
	 *  Diese Funktion sollte in der Subklasse
	 *  überschriben werden.
	 */
    protected function init(){
    }
	
	/** @brief Benutzereingabe überprüfen
	 *
	 *  Formular Input Dateon vom User auf Richtigkeit überprüfen
	 *
	 *
	 *  @see SSHelper::checkFromInputs
	 *  @return bool
	 */
	public function isInputValid(){
		$this->formPropertyValueErrors = SSHelper::checkFromInputs(
			$this->TABLE
			, $this->SHOW_IN
			, $this->formPropertiesAndValues
		);
		if(sizeof($this->formPropertyValueErrors) > 0){
			return false;
		}
		return true;
	}
	
	/** @brief Meldungs-Handler
	 *
	 *  Die Meldungen je nach Aktionen, werden
	 *  mit dieser Methode gehandlet.
	 *  Die Zusammensetzung aus Aktionsname und
	 *  error oder success ergibt den Platzhalter für
	 *  die Meldung.
	 */
	public function messageHandler(){
		if($this->showMessage){
			if(sizeof($this->formPropertyValueErrors) > 0){
				$params['msg_type'] = 'error';
				$this->view->displayErrorMessage(
					SSHelper::i18n('label_'.$this->formPropertiesAndValues['action'].'_error')
				);
			}else{
				$this->view->displaySuccessMessage(
					SSHelper::i18n('label_'.$this->formPropertiesAndValues['action'].'_success')
				);
			}
		}
	}
	
	/** @brief Form Action Name holen
	 *
	 *  Action-Name aus dem Formular, welche abgeschickt wurde
	 *  holen
	 *
	 *  @return $actionName
	 */
	public function getFormActionName(){
		if(isset($this->formPropertiesAndValues['action'])){
			return $this->formPropertiesAndValues['action'];
		}
		return '';
	}
	
	/** @brief compare Form Action Name
	 *
	 *  Vergleicht Action-Name vom Formular, welche abgeschickt wurde
	 *
	 *  @param ActioName zum vergleichen
	 *  @return bool
	 */
	public function isFormActionName($action){
		if(isset($this->formPropertiesAndValues['action'])){
			if($this->formPropertiesAndValues['action'] == $action){
				return true;
			}
		}
		return false;
	}
	
	/** @brief Is Benutzereingabe Unique
	 *
	 *  Überprüft ob From-Post nicht durch einen Browser-Refresh
	 *  generiert worden ist.
	 *
	 *  @param (string) $action: Formular Action 
	 *  @return bool: true = nicht generiert | false = generiert durch Browser-Refresh
	 */
	public function isUserRequestUnique($action){
		if($this->session->get($action.'SuccessUniqueId') != $this->formPropertiesAndValues['uniqueId']){
			return true;
		}
		return false;
	}
	
	/** @brief Benutzereingabe nicht mehr Unique
	 *
	 *  Benutzereingabe als nicht mehr Unique speichern.
	 *
	 *  @param (string) $action: Formular Action 
	 */
	public function userRequestIsNotMoreUnique($action){
		$this->session->set($action.'SuccessUniqueId', $this->formPropertiesAndValues['uniqueId']);
	}
	
	/** @brief store Value in Session
	 *
	 *  Session Funktion um Session innerhalb von
	 *  dieser Klasse zu verwalten.
	 *
	 *  Key + Value in Session ablegen.
	 *
	 *  @return bool
	 *
	 *  @see SSController::setSession
	 *  @see SSController::getSession
	 *  @see SSController::removeSession
	 */
	public function setSession($key, $value){
		$values = $this->session->get($this->FORM_ID);
		$values[$key] = $value;
		$this->session->set($this->FORM_ID, $values);
		return true;
	}
	
	/** @brief get Value from Session
	 *
	 *  Session Funktion um Session innerhalb von
	 *  dieser Klasse zu verwalten.
	 *
	 *  Wert nach Key in Session holen.
	 *
	 *  @return mixed
	 *
	 *  @see SSController::setSession
	 *  @see SSController::getSession
	 *  @see SSController::removeSession
	 */
	public function getSession($key){
		$values = $this->session->get($this->FORM_ID);
		return $values[$key];
	}
	
	/** @brief remove Key + Value from Session
	 *
	 *  Session Funktion um Session innerhalb von
	 *  dieser Klasse zu verwalten.
	 *
	 *  Key + Value von der Session löschen.
	 *
	 *  @return bool
	 *
	 *  @see SSController::setSession
	 *  @see SSController::getSession
	 *  @see SSController::removeSession
	 */
	public function removeSession($key){
		$values = $this->session->get($this->FORM_ID);
		$values[$key] = null;
		unset($values[$key]);
		$this->session->set($this->FORM_ID, $values);
		return true;
	}
}