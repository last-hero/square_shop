<?php
class SSClientLoginView {	
	// Form Array Key Name
	const FORM_ID = 'login';
	
	/**
	* Login Maske anzeigen
	*/
	public function displayLoginHtml($params = array()){
		$params['FORM_ID'] = self::FORM_ID;
		try{			
			echo SSGUI::parse(SSClient::TABLE.'.login.form.tmpl.php', $params);
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
			echo SSGUI::parse(SSClient::TABLE.'.logout.form.tmpl.php', $params);
		}catch(SSException $e) {
			echo $e;
		}
	}
}

