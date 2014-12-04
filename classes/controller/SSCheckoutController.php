<?php
#
#
# SSCheckoutController
# https://github.com/last-hero/square_shop
#
# (c) Gobi Selva
# http://www.square.ch
#
# Diese Klasse dient für die Bestellung vom Warenkorb bis zur
# Bestellbestätigung
#
#

class SSCheckoutController {	
	const ACTION_STEP1 = 'checkout_step1';
	const ACTION_STEP2 = 'checkout_step2';
	const ACTION_STEP3 = 'checkout_step3';
	const ACTION_STEP4 = 'checkout_step4';
	
	// Singleton --> Session Objekt
	private $session;
	
	// POST Var Daten -> korrekt eingegebene von User
	private $formPropertiesAndValues;
	
	// Fehler
	private $formPropertyValueErrors;
	
	// SSCartView Object
	private $cartView;
	
	// SSCheckoutView Object
	private $checkoutView;
	
	// SSCustomerLoginController Object
	private $customerLoginController;
	
	// SSCustomerRegisterController Object
	private $customerRegisterController;
	
	private $showMessage;
	
	private $step = 1;
	
	/*
	* Konstruktor: lädt Session Instanz (Singleton)
	* Falls POST Request abgeschickt wurde, dann daten laden
	*/
    public function __construct(){
		// Session Objekt (Singleton) holen
		$this->session = SSSession::getInstance();
		
		$this->cartView = new SSCartView();
		
		$this->customerLoginController = new SSCustomerLoginController();
		
		$this->customerRegisterController = new SSCustomerRegisterController();
		
		$this->checkoutView = new SSCheckoutView();
		
		// Form Post Vars (User input) holen
		$this->formPropertiesAndValues = SSHelper::getPostByFormId(SSCartView::FORM_ID);
    }
	
	/*
	* Warenkorb starten
	*/
	public function invoke(){
		$this->checkoutHandler();
		$this->displayStepView();
		$this->checkoutViewHandler();
	}
	
	/*
	* Warenkorb Handler: Add to Cart, Remove from Cart, Menge ändern
	*/
	public function checkoutHandler(){
		switch($this->step){
			case 1:
				if($this->customerLoginController->isUserLoggedIn()){
					$this->step = 2;
				}
				break;
			default:
				break;
		}
	}
	public function checkoutViewHandler(){
		switch($this->step){
			case 1:
				if(!$this->customerLoginController->isUserLoggedIn()){
					$this->customerLoginController->displayView();
					//$this->customerRegisterController->displayView();
					$this->displayViewByStep();
				}
				break;
			case 1:
				if(!$this->customerLoginController->isUserLoggedIn()){
					$this->customerLoginController->displayView();
					$this->customerRegisterController->displayView();
				}
				break;
			default:
				break;
		}
	}
	public function displayView(){
		$params = array();
		for($x=1; $x<=5; $x++){
			$params['label_steps'][$x] = SSHelper::i18l('label_step'.$x);
		}
		$params['step_active'] = $this->step;
	}
	public function displayViewByStep(){
		
		$params = array();
		for($x=1; $x<=5; $x++){
			$params['label_steps'][$x] = SSHelper::i18l('label_step'.$x);
		}
		$params['step_active'] = $this->step;
		$this->checkoutView->displayCheckoutByStepHtml($this->step, $params);
	}
	public function displayStepView(){
		$params = array();
		for($x=1; $x<=5; $x++){
			$params['label_steps'][$x] = SSHelper::i18l('label_step'.$x);
		}
		$params['step_active'] = $this->step;
		$this->checkoutView->displayCheckoutStepHtml($params);
	}
	public function messageHandler(){
		if($this->showMessage){
			$params = array();
			$params['msg_type'] = 'success';
			if(sizeof($this->formPropertyValueErrors) > 0){
				$params['msg_type'] = 'error';
			}
			
			$params['label_text'] = SSHelper::i18l($this->formPropertiesAndValues['action'].'_'.$params['msg_type']);
			
			$this->cartView->displayCartMessageHtml($params);
		}
	}
}