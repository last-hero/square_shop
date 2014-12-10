<?php
/** @file SSCheckoutView.php
 *  @brief Checkout View Klasse
 *
 *  Dies Klasse enthält alle Views für das Checkout
 *
 *  Die Templates welche benötigt werden sind
 *  im Verzeichnis /templates vorhanden.
 *
 *  @author Gobi Selva
 *  @author http://www.square.ch
 *  @author https://github.com/last-hero/square_shop
 */

class SSCheckoutView {
	// Form Array Key Name
	const FORM_ID = 'checkout';
	
	// Form Array Key Name
	const FORM_ID_ADDRESS = 'checkout_address';
	
	/**
	*
	*/
	public function displayCheckoutStepHtml($params = array()){
		$params['FORM_ID'] = self::FORM_ID;
		try{			
			echo SSGUI::parse(self::FORM_ID.'.step.navi.tmpl.php', $params);
		}catch(SSException $e) {
			echo $e;
		}
	}
	
	/**
	*
	*/ 
	public function displayCheckoutByStepHtml($step, $params = array()){
		$params['FORM_ID'] = self::FORM_ID;
		try{			
			echo SSGUI::parse(self::FORM_ID.'.step'.$step.'.tmpl.php', $params);
		}catch(SSException $e) {
			echo $e;
		}
	}
	
	/**
	*
	*/ 
	public function displayBillingAddressForm($step, $params = array()){
		$params['FORM_ID'] = self::FORM_ID;
		try{
			echo SSGUI::parse(self::FORM_ID.'.step'.$step.'.tmpl.php', $params);
		}catch(SSException $e) {
			echo $e;
		}
	}
}

