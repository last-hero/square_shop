<?php
class SSClientLoginView {
	/**
	* Login Maske anzeigen
	*/
	public static function displayLoginHtml(){
		try{			
			echo SSGUI::parse(SSClient::TABLE.'.login.form.tmpl.php');
		}catch(SSException $e) {
			echo $e;
		}
	}
	
	
	/**
	* Logout Maske anzeigen
	*/
	public static function displayLogoutHtml(){
		try{			
			echo SSGUI::parse(SSClient::TABLE.'.logout.form.tmpl.php');
		}catch(SSException $e) {
			echo $e;
		}
	}
}

