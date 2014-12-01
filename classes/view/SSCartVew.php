<?php
#
#
# SSCartView
# https://github.com/last-hero/square_shop
#
# (c) Gobi Selva
# http://www.square.ch
#
# Dies Klasse enthält alle Views für den Warenkorb
#
#

class SSCartView {
	// Form Array Key Name
	const FORM_ID = 'cart';
	
	/**
	* Warenkorb Anzeige
	*   --> Full
	*/
	public function displayCartHtml($params = array()){
		$params['FORM_ID'] = self::FORM_ID;
		try{			
			echo SSGUI::parse(self::FORM_ID.'.complete.tmpl.php', $params);
		}catch(SSException $e) {
			echo $e;
		}
	}
	
	/**
	* Warenkorb Anzeige
	*   --> Short
	*/
	public function displayCartShortHtml($params = array()){
		$params['FORM_ID'] = self::FORM_ID;
		try{			
			echo SSGUI::parse(self::FORM_ID.'.short.tmpl.php', $params);
		}catch(SSException $e) {
			echo $e;
		}
	}
}

