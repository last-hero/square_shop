<?php
class SSCustomerLoginView extends SSObjectView{
	// Form Array Key Name
	const FORM_ID = 'login';
	protected $FORM_ID = self::FORM_ID;
	
	/**
	* Login Maske anzeigen
	*/
	public function displayLoginHtml($params = array()){
		$params['FORM_ID'] = self::FORM_ID;
		try{			
			echo SSGUI::parse(SSCustomer::TABLE.'.login.form.tmpl.php', $params);
		}catch(SSException $e) {
			echo $e;
		}
	}
	
	/**
	* Logout Maske anzeigen
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

