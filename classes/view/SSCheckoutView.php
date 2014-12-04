<?php
#
#
# SSCheckoutView
# https://github.com/last-hero/square_shop
#
# (c) Gobi Selva
# http://www.square.ch
#
# Dies Klasse enthält alle Views für das Checkout
#
#

class SSCheckoutView {
	// Form Array Key Name
	const FORM_ID = 'checkout';
	
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
	
	public function displayCheckoutMessageHtml($params = array()){
		$params['FORM_ID'] = self::FORM_ID;
		try{			
			echo SSGUI::parse('message.tmpl.php', $params);
		}catch(SSException $e) {
			echo $e;
		}
	}
}

