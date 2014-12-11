<?php
/** @file SSCheckoutController.php
 *  @brief Einkauf Verwalten
 *
 *  Diese Klasse verwaltet den ganzen Einkauf
 *
 *  @author Gobi Selva
 *  @author http://www.square.ch
 *  @author https://github.com/last-hero/square_shop
 */

class SSCheckoutController extends SSController{
	const ACTION_STEP  = 'checkout_step';
	const ACTION_STEP1 = 'checkout_step1';
	const ACTION_STEP2 = 'checkout_step2';
	const ACTION_STEP3 = 'checkout_step3';
	const ACTION_STEP4 = 'checkout_step4';
	
	const ACTION_LOGIN = 'login';
	
	const ACTION_GO_FOR_REGISTER = 'checkout_go_for_register';
	
	const ACTION_REGISTER = 'register';
	
	const ACTION_BILLING = 'billing';
	
	const ACTION_DELIVERY = 'delivery';
	
	const ACTION_PAYMENT = 'payment';
	
	const ACTION_ORDER = 'order';
	
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
		if(!$this->isLoginStepOK()){
			$this->setStep(1);
		}elseif(!$this->isRegisterStepOK()){
			$this->setStep(2);
		}elseif(!$this->isDeliveryStepOK()){
			$this->setStep(3);
		}elseif(!$this->isPaymentStepOK()){
			$this->setStep(4);
		}elseif(!$this->isOrderStepOK()){
			$this->setStep(5);
		}
		switch($this->getStep()){
			case 1:
				$this->handleLoginStep();
				break;
			case 2:
				$this->handleRegisterStep();
				break;
			case 3:
				$this->handleDeliveryStep();
				break;
			case 4:
				$this->handlePaymentStep();
				break;
			case 5:
				$this->handleOrderStep();
				break;
			default:
				break;
		}
	}
	public function checkoutViewHandler(){
		switch($this->getStep()){
			case 1:
				$this->displayLoginStep();
				break;
			case 2:
				$this->displayRegisterStep();
				break;
			case 3:
				$this->displayDeliveryStep();
				break;
			case 4:
				$this->displayPaymentStep();
				break;
			case 5:
				$this->displayOrderStep();
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
		$params['step_active'] = $this->getStep();
	}
	public function displayViewByStep(){
		
		$params = array();
		for($x=1; $x<=5; $x++){
			$params['label_steps'][$x] = SSHelper::i18l('label_step'.$x);
		}
		$params['step_active'] = $this->getStep();
		$this->checkoutView->displayCheckoutByStepHtml($this->getStep(), $params);
	}
	public function displayStepView(){
		$params = array();
		for($x=1; $x<=5; $x++){
			$params['label_steps'][$x] = SSHelper::i18l('label_step'.$x);
		}
		$params['step_active'] = $this->getStep();
		$this->checkoutView->displayCheckoutStepHtml($params);
	}	
	
	
	
	/** @brief Prüfen ob Zahlungsart ausgewählt
	 *
	 *  
	 *  @see SSCheckoutController::displayOrderStep();
	 *  @see SSCheckoutController::handleOrderStep();
	 *  @see SSCheckoutController::isOrderStepOK();
	 */
	public function isOrderStepOK(){
		return false;
	}
	
	/** @brief Title
	 *
	 *  
	 *  @see SSCheckoutController::displayOrderStep();
	 *  @see SSCheckoutController::handleOrderStep();
	 *  @see SSCheckoutController::isOrderStepOK();
	 */
	public function displayOrderStep(){
		$this->view->displayMessage(
			SSHelper::i18l(self::ACTION_STEP.'_'.self::ACTION_ORDER)
		);
		$cartCtrl = new SSCartController();
		$cartCtrl->simpleView = 1;
		$cartCtrl->displayView();
		
		
		$payment = $this->session->get('checkoutPayment');
		
		$params = array();
		
		$params['label_submit'] = SSHelper::i18l('label_checkout_confirm');
		$params['action'] = self::ACTION_ORDER;
		
		$this->checkoutView->displayCheckoutByTmpl(self::ACTION_ORDER, $params);
	}
	
	/** @brief Title
	 *
	 *  
	 *  @see SSCheckoutController::displayOrderStep();
	 *  @see SSCheckoutController::handleOrderStep();
	 *  @see SSCheckoutController::isOrderStepOK();
	 */
	public function handleOrderStep(){
		if($this->isFormActionName(self::ACTION_ORDER)){
			$payment = $this->session->get('checkoutPayment');
			if($payment == 'shipping'){
				d('sadasdasd');
				
				$cartCtrl = new SSCartController();
				
				// Artikel IDs aus Warenkorb
				$ids = $cartCtrl->getCartItemIds();
				
				$article = new SSArticle();
				// Artikeln aus DB gefiltert nach PrimaryKeys
				$articles = $article->getByIds($ids);
				
				d($articles);
			
			}
		}
		return false;
	}
	
	
	/** @brief Prüfen ob Zahlungsart ausgewählt
	 *
	 *  
	 *  @see SSCheckoutController::displayPaymentStep();
	 *  @see SSCheckoutController::handlePaymentStep();
	 *  @see SSCheckoutController::isPaymentStepOK();
	 */
	public function isPaymentStepOK(){
		$payment = $this->session->get('checkoutPayment');
		if(strlen(trim($payment))){
			$payments = SSHelper::getSetting('payment');
			if(in_array($payment, $payments)){
				return true;
			}
		}
		return false;
	}
	
	/** @brief Zahlungsarten-Liste
	 *
	 *  
	 *  @see SSCheckoutController::displayPaymentStep();
	 *  @see SSCheckoutController::handlePaymentStep();
	 *  @see SSCheckoutController::isPaymentStepOK();
	 */
	public function displayPaymentStep(){
		$payments = SSHelper::getSetting('payment');
		
		$params = array();
		$params['formPropertiesAndValues'] = $this->formPropertiesAndValues;
		$params['formPropertyValueErrors'] = $this->formPropertyValueErrors;
		$params['payments'] = $payments;
		
		$params['label_submit'] = SSHelper::i18l('label_checkout_next');
		$params['action'] = self::ACTION_PAYMENT;
		
		$this->checkoutView->displayCheckoutByTmpl(self::ACTION_PAYMENT, $params);
	}
	
	/** @brief Zahlungsart verwalten
	 *
	 *  
	 *  @see SSCheckoutController::displayPaymentStep();
	 *  @see SSCheckoutController::handlePaymentStep();
	 *  @see SSCheckoutController::isPaymentStepOK();
	 */
	public function handlePaymentStep(){
		$this->view->displayMessage(
			SSHelper::i18l(self::ACTION_STEP.'_'.self::ACTION_PAYMENT)
		);
		if($this->isFormActionName(self::ACTION_PAYMENT)){
			$payment = $this->formPropertiesAndValues[self::ACTION_PAYMENT];
			if(strlen(trim($payment))){
				$payments = SSHelper::getSetting('payment');
				if(in_array($payment, $payments)){
					$this->session->set('checkoutPayment', $payment);
					$this->nextStep();
				}
			}
		}
	}
	
	/** @brief Prüfen ob Delivery Step ok
	 *
	 *  Überprüfen ob der Deliver-Step richtig
	 *  ausgeführt wurde.
	 *  Dabei wird die Lieferadresse, welche in der
	 *  Session ausgelagert wurde, nach Richtigkeit
	 *  überprüft.
	 *  
	 *  @see SSCheckoutController::displayDeliveryStep();
	 *  @see SSCheckoutController::handleDeliveryStep();
	 *  @see SSCheckoutController::isDeliveryStepOK();
	 */
	public function isDeliveryStepOK(){
		$address = $this->session->get('checkoutDeliveryAddress');
		if(is_array($address)){
			$errors = SSHelper::checkFromInputs(
				SSCustomer::TABLE
				, SSDBSchema::SHOW_IN_DELIVER_ADDRESS
				, $address
			);
			if(sizeof($errors) < 1){
				return true;
			}
		}
		
		return false;
	}
	
	/** @brief Lieferadresse-Maske anzeigen
	 *
	 *  Die Felder der Lieferadresse-Maske aus dem
	 *  SSSchema Klasse holen und das Formular darstellen.
	 *  
	 *  @see SSCheckoutController::displayDeliveryStep();
	 *  @see SSCheckoutController::handleDeliveryStep();
	 *  @see SSCheckoutController::isDeliveryStepOK();
	 */
	public function displayDeliveryStep(){
		$this->view->displayMessage(
			SSHelper::i18l(self::ACTION_STEP.'_'.self::ACTION_DELIVERY)
		);
		
		$params = array();
		$params['formPropertiesAndValues'] = $this->formPropertiesAndValues;
		$params['formPropertyValueErrors'] = $this->formPropertyValueErrors;
		
		if(!$this->isFormActionName(self::ACTION_DELIVERY)){
			$params['formPropertiesAndValues'] = $this->session->get('checkoutDeliveryAddress');
		}
		$params['label_errors'] = array();
		foreach($params['formPropertyValueErrors'] as $f){
			foreach($f as $name => $val){
				$params['label_errors'][$name] = SSHelper::i18l('label_error_'.$name);
			}
		}
		
		// Delivery Adress - Formular
		$params['fields'] = SSHelper::getFormProperties(
			SSCheckoutView::FORM_ID
			, SSCheckout::TABLE
			, SSDBSchema::SHOW_IN_DELIVER_ADDRESS
		);
		
		$params['label_submit'] = SSHelper::i18l('label_checkout_next');
		$params['action'] = self::ACTION_DELIVERY;
		
		$this->checkoutView->displayCheckoutByTmpl(self::ACTION_DELIVERY, $params);
	}
	
	/** @brief Lieferadresse
	 *
	 *  Hier wird die Lieferadresse auf richtigkeit
	 *  überprüft und zum nächsten Step weitergeleitet,
	 *  falls die Adresse korrekt eingegeben wurde.
	 *  Zudem wird die Lieferadresse in Session ausgelagert.
	 *  
	 *  @see SSCheckoutController::displayDeliveryStep();
	 *  @see SSCheckoutController::handleDeliveryStep();
	 *  @see SSCheckoutController::isDeliveryStepOK();
	 */
	public function handleDeliveryStep(){
		if($this->isFormActionName(self::ACTION_DELIVERY)){
			$this->formPropertyValueErrors = SSHelper::checkFromInputs(
				SSCheckout::TABLE
				, SSDBSchema::SHOW_IN_DELIVER_ADDRESS
				, $this->formPropertiesAndValues
			);
			if(sizeof($this->formPropertyValueErrors) < 1){
				$this->session->set('checkoutDeliveryAddress', $this->formPropertiesAndValues);
				$this->nextStep();
			}
		}
	}
	
	/** @brief Prüfen ob Register Step ok
	 *
	 *  Überprüfen ob der Register-Step richtig
	 *  ausgeführt wurde.
	 *
	 *  Dabei wird die Register-Daten (Rechnungs-Daten), 
	 *  welche in der Session ausgelagert wurde, 
	 *  nach Richtigkeit überprüft.
	 *
	 *  @see SSCheckoutController::displayRegisterStep();
	 *  @see SSCheckoutController::handleRegisterStep();
	 *  @see SSCheckoutController::isRegisterStepOK();
	 */
	public function isRegisterStepOK(){
		$billingAddress = $this->session->get('checkoutBillingAddress');
		if(is_array($billingAddress)){
			$errors = SSHelper::checkFromInputs(
				SSCustomer::TABLE
				, SSDBSchema::SHOW_IN_REGISTER
				, $billingAddress
			);
			if(sizeof($errors) < 1){
				return true;
			}
		}
		return false;
	}
	
	/** @brief Register View
	 *
	 *  Zeigt die Register-Maske an und 
	 *  ermöglicht zudem, eine andere
	 *  Lieferadresse anzugeben.
	 *  
	 *  @see SSCheckoutController::displayRegisterStep();
	 *  @see SSCheckoutController::handleRegisterStep();
	 *  @see SSCheckoutController::isRegisterStepOK();
	 */
	public function displayRegisterStep(){
		$this->view->displayMessage(
			SSHelper::i18l(self::ACTION_STEP.'_'.self::ACTION_REGISTER)
		);
		
		$params = array();
		$params['formPropertiesAndValues'] = $this->formPropertiesAndValues;
		$params['formPropertyValueErrors'] = $this->formPropertyValueErrors;
		
		if(!$this->isFormActionName(self::ACTION_REGISTER)){
			$params['formPropertiesAndValues'] = $this->session->get('checkoutBillingAddress');
		}
		$params['label_errors'] = array();
		foreach($params['formPropertyValueErrors'] as $f){
			foreach($f as $name => $val){
				$params['label_errors'][$name] = SSHelper::i18l('label_error_'.$name);
			}
		}
		// Register Formular
		$params['fields'] = SSHelper::getFormProperties(
			SSCustomerRegisterView::FORM_ID
			, SSCustomer::TABLE
			, SSDBSchema::SHOW_IN_REGISTER
		);
		
		$params['label_submit'] = SSHelper::i18l('label_checkout_next');
		$params['action'] = self::ACTION_REGISTER;
		
		$this->checkoutView->displayCheckoutByTmpl(self::ACTION_REGISTER, $params);
	}
	
	/** @brief Register Logik
	 *
	 *  Hier wird das Register oder weiter zur
	 *  Eingabe von Lieferadresse oder weiter zur
	 *  Zahlungsmethode Handling durchgeführt.
	 *  Zudem wird die Register-Daten
	 *  bzw. Rechnungsadresse in Session ausgelagert.
	 *  
	 *  @see SSCheckoutController::displayRegisterStep();
	 *  @see SSCheckoutController::handleRegisterStep();
	 *  @see SSCheckoutController::isRegisterStepOK();
	 */
	public function handleRegisterStep(){
		if($this->isFormActionName(self::ACTION_REGISTER)){
			$this->formPropertyValueErrors = SSHelper::checkFromInputs(
				SSCustomer::TABLE
				, SSDBSchema::SHOW_IN_REGISTER
				, $this->formPropertiesAndValues
			);
			//d($this->formPropertyValueErrors);
			if(sizeof($this->formPropertyValueErrors) < 1){
				$this->session->set('checkoutBillingAddress', $this->formPropertiesAndValues);
				if($this->formPropertiesAndValues['diff_delivery'] == 'yes'){
					$this->nextStep();
				}else{
					$this->session->set('checkoutDeliveryAddress', $this->formPropertiesAndValues);
					$this->nextStep();
					$this->nextStep();
				}
			}
		}
	}
	
	
	/** @brief Prüfen ob Login Step ok
	 *
	 *  Prüfen ob Login Step richtig vom
	 *  Käufer ausgeführt wurde: Entweder hat
	 *  der Käufer sich mit dem Login angemeldet,
	 *  oder ist weitergefahren zur Register-Maske.
	 *  
	 *  @see SSCheckoutController::displayLoginStep();
	 *  @see SSCheckoutController::handleLoginStep();
	 */
	public function isLoginStepOK(){
		if($this->session->get('checkoutLoginStepBy') == self::ACTION_LOGIN){
			if($this->customerLoginController->isUserLoggedIn()){
				return true;
			}
		}elseif($this->session->get('checkoutLoginStepBy') == self::ACTION_GO_FOR_REGISTER){
			return true;
		}
		return false;
	}
	
	/** @brief Login View
	 *
	 *  Zeigt die Login-Maske an und 
	 *  ermöglicht zudem, falls sich
	 *  der Käufer registrieren möchte,
	 *  zur Registrierungsmaske zu gelanden.
	 *  
	 *  
	 *  @see SSCheckoutController::isLoginStepOK();
	 *  @see SSCheckoutController::handleLoginStep();
	 */
	public function displayLoginStep(){
		$this->view->displayMessage(
			SSHelper::i18l(self::ACTION_STEP.'_'.self::ACTION_LOGIN)
		);
		$this->customerLoginController->displayView();
		
		
		$this->view->displayMessage(
			SSHelper::i18l(self::ACTION_STEP.'_go_for_register')
		);

		$params = array();
		$params['label_submit'] = SSHelper::i18l('label_checkout_next');
		$params['action'] = self::ACTION_GO_FOR_REGISTER;
		
		$this->checkoutView->displayCheckoutByTmpl(self::ACTION_LOGIN, $params);
		
	}
	
	/** @brief Login Logik
	 *
	 *  Hier wird das Login oder weiter zur
	 *  Registrierungsmaske Handling durchgeführt.
	 *  
	 *  @see SSCheckoutController::isLoginStepOK();
	 *  @see SSCheckoutController::displayLoginStep();
	 */
	public function handleLoginStep(){
		$this->customerLoginController->loginLogoutHandler();
		
		if($this->customerLoginController->isUserLoggedIn()){
			$this->nextStep();
			$this->session->set('checkoutLoginStepBy', self::ACTION_LOGIN);
		}
		if($this->isFormActionName(self::ACTION_GO_FOR_REGISTER)){
			$this->nextStep();
			$this->session->set('checkoutLoginStepBy', self::ACTION_GO_FOR_REGISTER);
		}
	}
	
	/** @brief Step setzen
	 *
	 *  Zum Step springen
	 *
	 *  @return $step
	 *
	 *  @see SSCheckoutController::getStep
	 *  @see SSCheckoutController::prevStep
	 *  @see SSCheckoutController::nextStep
	 */
	public function setStep($step){
		$step = (int)$step < 1 ? 1 : (int)$step;
		$this->session->set('checkoutStep', $step);
		return $step;
	}
	
	/** @brief Aktueller Step
	 *
	 *  Aktuelle Schritt holen
	 *
	 *  @return $step
	 *
	 *  @see SSCheckoutController::prevStep
	 *  @see SSCheckoutController::nextStep
	 */
	public function getStep(){
		$step = (int)$this->session->get('checkoutStep');
		$step = $step < 1 ? 1 : $step;
		return $step;
	}
	
	/** @brief Next Step
	 *
	 *  Zum nächsten Schritt springen.
	 *
	 *  @return $step
	 *
	 *  @see SSCheckoutController::getStep
	 *  @see SSCheckoutController::prevStep
	 */
	public function nextStep(){
		$step = (int)$this->session->get('checkoutStep');
		$step = $step < 1 ? 1 : $step;
		$step++;
		$this->session->set('checkoutStep', $step);
		return $step;
	}
	
	/** @brief Previous Step
	 *
	 *  Zum vorherigen Schritt springen.
	 *
	 *  @return $step
	 *
	 *  @see SSCheckoutController::getStep
	 *  @see SSCheckoutController::nextStep
	 */
	public function prevStep(){
		$step = (int)$this->session->get('checkoutStep');
		$step--;
		$step = $step < 1 ? 1 : $step;
		$this->session->set('checkoutStep', $step);
		return $step;
	}
}