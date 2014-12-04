<?php
/** @file SSController.php
 *  @brief Controller Class
 *
 *  Diese Klasse dient als Basis Controller
 *
 *  @author Gobi Selva
 *  @author http://www.square.ch
 *  @author https://github.com/last-hero/square_shop
 *  @bug No known bugs.
 */
class SSController {
	/**
	 * Formular ID
	 */
	protected $FORM_ID;
	
	/**
	 * Datenbank Tabellenname
	 */
	protected $TABLE;
	
	/**
	 * siehe SSSchema
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
	
	/** @brief Is Benutzereingabe Unique
	 *
	 *  Überprüft ob From-Post nicht durch einen Browswer-Refresh
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
}