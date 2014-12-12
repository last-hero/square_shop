<?php
/** @file SSView.php
 *  @brief View Klasse
 *
 *  Diese Klasse dient als Parent für alle Subklassen
 *  die eine Maske (Html) darstellen möchten.
 *
 *  Die Templates welche benötigt werden sind
 *  im Verzeichnis /templates vorhanden.
 *
 *  @author Gobi Selva
 *  @author http://www.square.ch
 *  @author https://github.com/last-hero/square_shop
 */
 
class SSView {
	/**
	 * Formular ID die hier eingesetzt wird: $_POST[SSForm][[FORM_ID]
	 */
	protected $FORM_ID = null;
	
	/** @brief Formular anzeigen
	 *
	 *  Ein Formular (Html-Code) nach Paramater generieren
	 *  und anzeigen.
	 *
	 *  Benötigte Dateien: /templates/form.tmpl.php 
	 *  und /templates/form.field.[text|select|...].tmpl.php 
	 *
	 *  @param $params: SSHelper::getFormProperties
	 *
	 *  @see SSHelper::getFormProperties
	 */
	public function displayFormHtml($params = array()){
		$params['FORM_ID'] = $this->FORM_ID;
		try{			
			echo SSGUI::parse('form.tmpl.php', $params);
		}catch(SSException $e) {
			echo $e;
		}
	}
	
	/** @brief Meldung anzeigen
	 *
	 *  Eine Meldung (Html-Code) nach Paramater generieren
	 *  und anzeigen.
	 *
	 *  Benötigte Datei: /templates/message.tmpl.php
	 *
	 *  @param $params
	 *
	 *  @see displayMessage()
	 *  @see displaySuccessMessage()
	 *  @see displayErrorMessage()
	 *  @see displayMessageHtml()
	 */
	protected function displayMessageHtml($params = array()){
		$params['FORM_ID'] = $this->FORM_ID;
		try{			
			echo SSGUI::parse('message.tmpl.php', $params);
		}catch(SSException $e) {
			echo $e;
		}
	}
	
	/** @brief Success-Meldung anzeigen
	 *
	 *  Eine Success-Meldung (Html-Code) anzeigen
	 *
	 *  @param $message
	 *
	 *  @see displayMessage()
	 *  @see displaySuccessMessage()
	 *  @see displayErrorMessage()
	 *  @see displayMessageHtml()
	 */
	public function displaySuccessMessage($message){
		$params['msg_type'] = 'success';
		$params['label_text'] = $message;
		if(empty($params['label_text'])){
			$params['label_text'] = SSHelper::i18n($this->FORM_ID.'_success_text');
		}
		$this->displayMessageHtml($params);
	}
	
	/** @brief Failure-Meldung anzeigen
	 *
	 *  Eine Failure-Meldung (Html-Code) anzeigen
	 *
	 *  @param $message
	 *
	 *  @see displayMessage()
	 *  @see displaySuccessMessage()
	 *  @see displayErrorMessage()
	 *  @see displayMessageHtml()
	 */
	public function displayErrorMessage($message){
		$params['msg_type'] = 'error';
		$params['label_text'] = $message;
		if(empty($params['label_text'])){
			$params['label_text'] = SSHelper::i18n($this->FORM_ID.'_failure_text');
		}
		$this->displayMessageHtml($params);
	}
	
	/** @brief Einfache Meldung anzeigen
	 *
	 *  Eine einfache Meldung (Html-Code) anzeigen
	 *
	 *  @param $message
	 *
	 *  @see displayMessage()
	 *  @see displaySuccessMessage()
	 *  @see displayErrorMessage()
	 *  @see displayMessageHtml()
	 */
	public function displayMessage($message){
		$params['msg_type'] = 'normal';
		$params['label_text'] = $message;
		if(empty($params['label_text'])){
			$params['label_text'] = SSHelper::i18n($this->FORM_ID.'_failure_text');
		}
		$this->displayMessageHtml($params);
	}
}