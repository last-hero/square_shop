<?php
class SSClientLoginView {	
	
	/**
	* Login Maske anzeigen
	*/
	public function displayLoginHtml($params = array()){
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
		try{			
			echo SSGUI::parse(SSClient::TABLE.'.logout.form.tmpl.php', $params);
		}catch(SSException $e) {
			echo $e;
		}
	}
}

