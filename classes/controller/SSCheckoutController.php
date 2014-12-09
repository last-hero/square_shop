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

class SSCheckoutController extends SSController{
	const ACTION_STEP1 = 'checkout_step1';
	const ACTION_STEP2 = 'checkout_step2';
	const ACTION_STEP3 = 'checkout_step3';
	const ACTION_STEP4 = 'checkout_step4';
	
	/**
	 * @see SSArticleView::FORM_ID
	 */
	protected $FORM_ID = SSCartView::FORM_ID;
	
	/**
	 * @see SSArticle::TABLE
	 */
	protected $TABLE = SSArticle::TABLE;
	
	/**
	 * @see SSDBSchema::SHOW_IN_DETAIL
	 */
	protected $SHOW_IN = SSDBSchema::SHOW_IN_CART_ITEM;
	
	// SSCartView Object
	private $cartView;
	
	// SSCheckoutView Object
	private $checkoutView;
	
	// SSCustomer Object
	private $customer;
	
	// SSCustomerLoginController Object
	private $customerLoginController;
	
	// SSCustomerRegisterController Object
	private $customerRegisterController;
	
	private $step = 1;
	
	/*
	* Konstruktor: lädt Session Instanz (Singleton)
	* Falls POST Request abgeschickt wurde, dann daten laden
	*/
    public function init(){
		
		$this->cartView = new SSCartView();
		
		$this->customerLoginController = new SSCustomerLoginController();
		
		$this->customer = new SSCustomer();
		
		$this->customerRegisterController = new SSCustomerRegisterController();
		
		$this->checkoutView = new SSCheckoutView();
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
		if($this->customerLoginController->isUserLoggedIn()){
			$this->step = 2;
		}
		switch($this->step){
			case 1:
				$this->customerLoginController->loginLogoutHandler();
				$this->customerRegisterController->registerHandler();
				
				/*
				if($this->customerRegisterController->isInputValid()){
					$email = $this->customerRegisterController
									->formPropertiesAndValues['email'];
					$password = $this->customerRegisterController
									->formPropertiesAndValues['password'];
									
					if($this->customer->loadCustomerByEmailAndPassword($email, $password)){
						$userId = $this->customer->get('id');
						$userName = $this->customer->get('firstname').' '.$this->customer->get('lastname');
						$this->customerLoginController->loginUser($userId, $userName);
						
						$this->step = 2;
					}
				}
				*/
				break;
			case 2:
				echo 'LOGIK STEP 2';
				break;
			default:
				break;
		}
	}
	public function checkoutViewHandler(){
		switch($this->step){
			case 1:
				if(!$this->customerLoginController->isUserLoggedIn()){
					$this->cartView->displayMessage(
						SSHelper::i18l(self::ACTION_STEP1.'_text')
					);
					$this->customerLoginController->displayView();
					/*
					$this->customerRegisterController->displayView();
					*/
					
					
					$this->customerRegisterController->messageHandler();
					$this->customerRegisterController->viewHandler();
					
					$this->displayViewByStep();
				}
				break;
			case 2:
				$this->cartView->displayMessage(
					SSHelper::i18l(self::ACTION_STEP2.'_text')
				);
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
}