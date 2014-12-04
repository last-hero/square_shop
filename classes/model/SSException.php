<?php
/** @file SSException.php
 *  @brief Exception Handler
 *
 *  Das ist ein angepasster Exception-Handler der von der 
 *  PHP-Exception-Handler Klasse alle Funktionalitäten erbt.
 *
 *  @author Gobi Selva
 *  @author http://www.square.ch
 *  @author https://github.com/last-hero/square_shop
 *
 *  @bug Keine Bugs bekannt.
 *  @todo Exceptions verfeinern
 */

class SSException extends Exception{
	/** @brief Test Daten importieren
	 *
	 *  Die Magic-Function ist ein „to String Operator“ 
	 *  und wird ausgeführt, sobald ein Objekt 
	 *  der Exception-Klasse mit echo ausgegeben wird.
	 */
	public function __toString() {
    	return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}
}
?>