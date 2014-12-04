<?php
/** @file SSObjectView.php
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
 *
 *  @bug Keine Bugs bekannt.
 */
 
class SSObjectView {
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
	 *  @see displaySuccessMessageHtml()
	 *  @see displayErrorMessageHtml()
	 */
	private function displayMessageHtml($params = array()){
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
	 *  @param $params
	 *
	 *  @see displayMessageHtml()
	 *  @see displayErrorMessageHtml()
	 */
	public function displaySuccessMessageHtml($params = array()){
		$params['msg_type'] = 'success';
		if(!isset($params['label_text'])){
			$params['label_text'] = SSHelper::i18l($this->FORM_ID.'_success_text');
		}
		$this->displayMessageHtml($params);
	}
	
	/** @brief Failure-Meldung anzeigen
	 *
	 *  Eine Failure-Meldung (Html-Code) anzeigen
	 *
	 *  @param $params
	 *
	 *  @see displayMessageHtml()
	 *  @see displaySuccessMessageHtml()
	 */
	public function displayErrorMessageHtml($params = array()){
		$params['msg_type'] = 'error';
		if(!isset($params['label_text'])){
			$params['label_text'] = SSHelper::i18l($this->FORM_ID.'_failure_text');
		}
		$this->displayMessageHtml($params);
	}
}