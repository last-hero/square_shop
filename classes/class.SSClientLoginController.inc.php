<?php
class SSClientLoginController {
	const FIELDNAME_EMAIL = 'email';
	const FIELDNAME_PASSWORD = 'email';
	
	private $session;
	
    public function __construct(){
		$this->session = SSSession::getInstance();
    }
	
	public function invoke(){
		$this->checkLogin();
		$this->displayView();
	}
	
	public function checkLogin(){
		print_r($_POST);
		if(isset($_POST[FIELDNAME_EMAIL]) and isset($_POST[FIELDNAME_PASSWORD])){
			$this->session->set('Client', $client);
			echo 'gtest';
		}
	}
	
	public function displayView(){
		$clientLoginView = new SSClientLoginView();
		$param = array();
		$param['label_email'] = SSHelper::i18l('E-Mail');
		$param['label_submit'] = SSHelper::i18l('Login');
		$param['label_password'] = SSHelper::i18l('Password');
		
		$param['fieldname_email'] = self::FIELDNAME_EMAIL;
		$param['fieldname_password'] = self::FIELDNAME_PASSWORD;
		
		$param['message_success'] = SSHelper::i18l('LoginSuccess');
		
		if(!$this->session->get('client')){
			$clientLoginView->displayLoginHtml($param);
		}else{
			$clientLoginView->displayLogoutHtml($param);
		}
	}
}