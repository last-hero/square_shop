<?php
/** @file SSCustomerLoginView.php
 *  @brief View Klasse
 *
 *  Diese Klasse dient zum Erstellen von
 *  Masken für User Login und Logout
 *
 *  Die Templates welche benötigt werden sind
 *  im Verzeichnis /templates vorhanden.
 *
 *  @author Gobi Selva
 *  @author http://www.square.ch
 *  @author https://github.com/last-hero/square_shop
 */
 
class SSCustomerLoginView extends SSObjectView{
	/**
	* siehe Parent
	*/
	const FORM_ID = 'login';
	protected $FORM_ID = self::FORM_ID;
	
	/** @brief Logout Maske anzeigen
	 *
	 *  Eine Logout Maske (Html-Code) anzeigen.
	 *
	 *  Benötigte Dateien: /templates/customer.logout.form.tmpl.php
	 *
	 *  @param $params
	 */
	public function displayLogoutHtml($params = array()){
		$params['FORM_ID'] = self::FORM_ID;
		try{			
			echo SSGUI::parse(SSCustomer::TABLE.'.logout.form.tmpl.php', $params);
		}catch(SSException $e) {
			echo $e;
		}
	}
}

