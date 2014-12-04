<?php
/** @file SSCustomerRegisterView.php
 *  @brief Registrieren von Käufer
 *
 *  Diese Klasse dient zum Erstellen von 
 *  Registrierungsmaske
 *
 *  @author Gobi Selva
 *  @author http://www.square.ch
 *  @author https://github.com/last-hero/square_shop
 *
 *  @bug Keine Bugs bekannt.
 */
 
class SSCustomerRegisterView extends SSObjectView{
	/**
	* siehe Parent
	*/
	const FORM_ID = 'register';
	protected $FORM_ID = self::FORM_ID;
	
	
	public function displaySuccessMessage(){
		$this->displaySuccessMessageHtml(array(
			'label_text' => SSHelper::i18l(self::FORM_ID.'_success_text')
		));
	}
}

