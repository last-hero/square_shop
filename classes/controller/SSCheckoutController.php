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
	protected $FORM_ID = SSCheckoutView::FORM_ID;
	
	/**
	 * @see SSArticle::TABLE
	 */
	protected $TABLE = 'order';
	
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
		if(!$this->customerLoginController->isUserLoggedIn()){
			$this->dropBillDeliverAddrFromSession();
		}elseif($this->customerLoginController->isUserLoggedIn()
			and $this->isBillDeliverAddrInSession()){
				$this->step = 3;
		}elseif($this->customerLoginController->isUserLoggedIn()){
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
				if($this->formPropertiesAndValues['action'] == self::ACTION_STEP2){
					$errorsBillAddr = SSHelper::checkFromInputs(
						$this->TABLE
						, SSDBSchema::SHOW_IN_BILL_ADDRESS
						, $this->formPropertiesAndValues
					);
					$errorsDeliverAddr = SSHelper::checkFromInputs(
						$this->TABLE
						, SSDBSchema::SHOW_IN_BILL_ADDRESS
						, $this->formPropertiesAndValues
					);
					$this->formPropertyValueErrors = array_merge($errorsBillAddr, $errorsDeliverAddr);
					if(sizeof($this->formPropertyValueErrors) < 1){
						$this->storeBillDeliverAddrInSession($this->formPropertiesAndValues);
					}
				}else{
					if($this->isBillDeliverAddrInSession()){
						$this->formPropertiesAndValues = $this->getBillDeliverAddrFromSession();
					}
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
				
				
				$userId = $this->customerLoginController->getLoggedInUserId();
				$this->customer->loadById($userId);
				
				/*
				$this->customer->propertiesAndValues
				d($this->customer->propertiesAndValues);
				*/
				
				$params['label_submit'] = SSHelper::i18l('label_next');
				$params['action'] = self::ACTION_STEP2;
				$params['label_billing_address'] = SSHelper::i18l('label_billing_address');
				$params['label_delivery_address'] = SSHelper::i18l('label_delivery_address');
				
				
				
				
				// Billing Adresse: Formular Felder holen
				$params['fields_bill'] = SSHelper::getFormProperties(
					SSCheckoutView::FORM_ID
					, 'order'
					, SSDBSchema::SHOW_IN_BILL_ADDRESS
				);
				
				// Delivery Adresse: Formular Felder holen
				$params['fields_deliver'] = SSHelper::getFormProperties(
					SSCheckoutView::FORM_ID
					, 'order'
					, SSDBSchema::SHOW_IN_DELIVER_ADDRESS
				);
				
				/*
				// Default Values setzen: Delivery Adresse = Benuzter Adresse
				foreach($params['fields_deliver'] as $field){
					$params['formPropertiesAndValues'][$field['name']]
						= $this->customer->get(str_replace('delivery_', '', $field['name']));
				}
				*/
				
				if($this->isBillDeliverAddrInSession()){
					$params['formPropertiesAndValues'] = $this->getBillDeliverAddrFromSession();
				}else{
					// Billing Adresse: Default Values = Benuzter Adresse
					foreach($params['fields_bill'] as $field){
						$params['formPropertiesAndValues'][$field['name']]
							= $this->customer->get(str_replace('billing_', '', $field['name']));
					}
					
					// Delivery Adresse: Default Values = Benuzter Adresse
					foreach($params['fields_deliver'] as $field){
						$params['formPropertiesAndValues'][$field['name']]
							= $this->customer->get(str_replace('delivery_', '', $field['name']));
					}
				}
				
				// Fromular für Billing + Delivery Adresse anzeigen
				$this->checkoutView->displayBillingAddressForm($this->step, $params);
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
	public function storeBillDeliverAddrInSession($address){
		$this->session->set('UserBillDeliverAddress', $address);
	}
	public function getBillDeliverAddrFromSession(){
		return $this->session->get('UserBillDeliverAddress');
	}
	public function dropBillDeliverAddrFromSession(){
		$this->session->remove('UserBillDeliverAddress');
	}
	public function isBillDeliverAddrInSession(){
		$array = $this->session->get('UserBillDeliverAddress');
		if(count($array)>5){
			return true;
		}else{
			return false;	
		}
	}
}