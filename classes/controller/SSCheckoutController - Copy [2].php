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
	
	// SSCartController Object
	private $cartCtrl;
	
	// SSCheckoutView Object
	private $checkoutView;
	
	// SSCustomer Object
	private $customer;
	
	// SSCustomerLoginController Object
	private $customerLoginCtrl;
	
	// SSCustomerRegisterController Object
	private $customerRegCtrl;
	
	private $step = 1;
	
	/*
	* Konstruktor: lädt Session Instanz (Singleton)
	* Falls POST Request abgeschickt wurde, dann daten laden
	*/
    public function init(){
		
		$this->cartView = new SSCartView();
		
		$this->cartCtrl = new SSCartController();
		
		$this->customerLoginCtrl = new SSCustomerLoginController();
		
		$this->customer = new SSCustomer();
		
		$this->customerRegCtrl = new SSCustomerRegisterController();
		
		$this->checkoutView = new SSCheckoutView();
    }
	
	/*
	* Warenkorb starten
	*/
	public function invoke(){
		if($this->cartCtrl->isCartEmpty()){
			$this->view->displaySuccessMessage(SSHelper::i18n('cart_is_empty'));
		}else{
			$this->checkoutHandler();
			$this->displayStepView();
			$this->checkoutViewHandler();
			
			/* --------------------------------------------------------------
			// Warenkorb + Checkout vom Session löschen
			// Bestellung wurde vollendet.
			- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
			if($this->isOrderStepOK()){
				$this->cartCtrl->clearCart();
				$this->clearAll();
			}
		}
	}
	
	/*
	* Warenkorb Handler: Add to Cart, Remove from Cart, Menge ändern
	*/
	public function checkoutHandler(){
		if(!$this->isLoginStepOK()){
			$this->setStep(1);
		}elseif(!$this->customerLoginCtrl->isUserLoggedIn()
		and !$this->isAddressStepOK(self::ACTION_REGISTER)){
			$this->setStep(2);
		}elseif(!$this->isAddressStepOK(self::ACTION_BILLING)){
			$this->setStep(2);
		}elseif(!$this->isAddressStepOK(self::ACTION_DELIVERY)){
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
				if($this->customerLoginCtrl->isUserLoggedIn()){
					$this->handleAddressStep(self::ACTION_BILLING);
				}else{
					$this->handleAddressStep(self::ACTION_REGISTER);
				}
				break;
			case 3:
				$this->handleAddressStep(self::ACTION_DELIVERY);
				break;
			case 4:
				$this->handlePaymentStep();
				break;
			case 5:
				$this->handleOrderStep();
				break;
			case 6:
				
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
				if($this->customerLoginCtrl->isUserLoggedIn()){
					$this->displayAddressStep(self::ACTION_BILLING);
				}else{
					$this->displayAddressStep(self::ACTION_REGISTER);
				}
				break;
			case 3:
				//$this->displayDeliveryStep();
				$this->displayAddressStep(self::ACTION_DELIVERY);
				break;
			case 4:
				$this->displayPaymentStep();
				break;
			case 5:
				$this->displayOrderStep();
				break;
			case 6:
				$this->view->displaySuccessMessage(
					SSHelper::i18n('checkout_order_success')
				);
				break;
			default:
				break;
		}
	}
	
	/** @brief Daten vom Session löschen
	 *
	 *  Vom Benuzter erfasste Daten für das
	 *  Checkout aus dem Session löschen
	 *
	 *  @see SSCartController::clearCart();
	 */
	public function clearAll(){
		$this->session->remove('checkoutOrderId');
		$this->session->remove('checkoutOrderDone');
		$this->session->remove('checkoutPayment');
		$this->session->remove('checkoutRegisterAddress');
		$this->session->remove('checkoutBillingAddress');
		$this->session->remove('checkoutDeliveryAddress');
		$this->session->remove('checkoutLoginStepBy');
		$this->session->remove('checkoutStep');
	}
	
	public function displayStepView(){
		$params = array();
		for($x=1; $x<=5; $x++){
			$params['label_steps'][$x] = SSHelper::i18n('label_step'.$x);
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
		if($this->session->get('checkoutOrderDone')){
			return true;
		}
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
			SSHelper::i18n(self::ACTION_STEP.'_'.self::ACTION_ORDER)
		);
		
		// Warenkorb anzeigen
		$cartCtrl = new SSCartController();
		$cartCtrl->simpleView = 1;
		$cartCtrl->displayView();
		
		$payment = $this->session->get('checkoutPayment');
		
		$params = array();
		
		$params['label_submit'] = SSHelper::i18n('label_checkout_confirm');
		$params['action'] = self::ACTION_ORDER;
		
		$params['payment'] = $payment;
		
		$this->checkoutView->displayCheckoutByTmpl(self::ACTION_ORDER.'.'.$payment, $params);
	}
	
	/** @brief Title
	 *
	 *  
	 *  @see SSCheckoutController::displayOrderStep();
	 *  @see SSCheckoutController::handleOrderStep();
	 *  @see SSCheckoutController::isOrderStepOK();
	 */
	public function handleOrderStep(){
		$checkoutOrderDone = $this->session->get('checkoutOrderDone');
		if(!$checkoutOrderDone){
			if($this->isFormActionName(self::ACTION_ORDER)){
				$this->session->get('checkoutOrderDone');
				$payment = $this->session->get('checkoutPayment');
				if($payment == 'onbill'){
					
					/* --------------------------------------------------------------
					// Käufer in DB speichern
					// Falls Käufer bereits einen Account besitzt und eingeloggt ist
					// dann Id aus dem Session holen
					- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
					if($this->customerLoginCtrl->isUserLoggedIn()){
						// Käufer ID holen
						$customerId = $this->customerLoginCtrl->getLoggedInUserId();
						
						// Käufer Daten nach ID aus dem DB laden
						// Check: Ob User immer noch in der DB vorhanden ist
						$customer = new SSCustomer();
						$customer->loadById($customerId);
					}else{
						// Käufer in DB speichern
						$registerData = $this->session->get('checkoutRegisterAddress');
						$customer = new SSCustomer();
						$customer->set($customer->getClearedUnknownProperties($registerData));
						$customer->save();
						//$registerData['id'] = $customer->get('id');
						//$this->session->set('checkoutBillingAddress', $registerData);
						$customerId = $customer->get('id');
					}
					/* ------------------------------------------------------------ */
					
					/* ---------------------------------------------------------------
					// Bestellung vorbereiten
					// Rechnungsadresse + Lieferadresse aus dem Session holen
					// Order Nummer generieren und Datum hinzufügen
					- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
					$order = new SSOrder();
					
					/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
					// Rechnungs- & Lieferadresse vom Session zu Bestellung zuweisen
					- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
					// Rechnungsadresse
					$billingAddress = $this->session->get('checkoutBillingAddress');
					unset($billingAddress['id']);
					$order->set($order->getClearedUnknownProperties($billingAddress));
					
					// Lieferadresse
					$deliveryAddress = $this->session->get('checkoutDeliveryAddress');
					unset($deliveryAddress['id']);
					$order->set($order->getClearedUnknownProperties($deliveryAddress));
					
					// Customer ID
					$order->set('customer_id', $customer->get('id'));
					
					// Order No
					$order->set('no', date("Y-m-d").' '.(time() - strtotime("today")));
					
					// Order Date
					$order->set('date',time());
					
					// Payment
					$order->set('payment', $payment);
					
					// Order in DB speichern
					$order->save();
					/* ------------------------------------------------------------ */
					
					/* ---------------------------------------------------------------
					// Artikel aus der DB holen 
					// und Order Items generieren, damit sie in DB
					// gespeichert werden können.
					- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
					
					// Warenkorb Controller erzeugen, um Artikel IDs aus dem
					// Session zu holen, die im Warenkorb hinzugefügt wurden.
					$cartCtrl = new SSCartController();
					// Artikel IDs aus Warenkorb
					$ids = $cartCtrl->getCartItemIds();
					// Artikel Objekt erzeugen um Artikel nach IDs
					// aus dem DB zuholen.
					$article = new SSArticle();
					// Artikeln aus DB gefiltert nach PrimaryKeys
					$articles = $article->getByIds($ids);
					
					foreach($articles as $art){
						$art['article_id'] = $art['id'];
						unset($art['id']);
						$orderItem = new SSOrderItem();
						$orderItem->set($orderItem->getClearedUnknownProperties($art));
						$orderItem->set('qty', $cartCtrl->getItemQtyById($art['article_id']));
						$orderItem->set('order_id', $order->get('id'));
						$orderItem->save();
					}
					/* ------------------------------------------------------------ */
					
					$checkoutOrderDone = true;
					
					$this->session->set('checkoutOrderId', $order->get('id'));
				}elseif($payment == 'paypal'){
					d('gtest');
				}
			}
		}
		if($checkoutOrderDone){
			$this->session->set('checkoutOrderDone', $checkoutOrderDone);
			$this->nextStep();
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
		
		$params['label_submit'] = SSHelper::i18n('label_checkout_next');
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
	
	/** @brief Prüfen ob Schritt ok
	 *  
	 *  Prüft ob die Adresse, die in der Session
	 *  ausgelagert wurden, immer noch richtig
	 *  sind.
	 *  
	 *  @param $action : self::ACTION_REGISTER
	 * 					 self::ACTION_BILLING
	 * 					 self::ACTION_DELIVERY
	 *  
	 *  @return boolean
	 *  
	 *  @see SSCheckoutController::displayAddressStep();
	 *  @see SSCheckoutController::handleAddressStep();
	 *  @see SSCheckoutController::isAddressStepOK();
	 *  @see SSCheckoutController::helperAddressConf();
	 */
	public function isAddressStepOK($type){
		$conf = $this->helperAddressConf($type);
		$_table = $conf['table'];
		$_showIn = $conf['showIn'];
		$_addressFromSession = $conf['addressFromSession'];
		$_action = $conf['action'];
		$_formId = $conf['formId'];
		
		if(is_array($_addressFromSession)){
			$errors = SSHelper::checkFromInputs(
				$_table
				, $_showIn
				, $_addressFromSession
			);
			if(sizeof($errors) < 1){
				return true;
			}
		}
		return false;
	}
	
	/** @brief Adresse View
	 *  
	 *  Das View zum Registrieren für Käufer ohne Benutzerkonto
	 *  und das View für Rechnungsadresse, falls Benutzer
	 *  eingeloggt. Zudem noch das View für Lieferadresse.
	 *  
	 *  Variante 1 (Neukunde): Registrieren + Lieferadresse
	 *  
	 *  Variante 2: Rechnungs- + Lieferadresse
	 *  
	 *  @param $action : self::ACTION_REGISTER
	 * 					 self::ACTION_BILLING
	 * 					 self::ACTION_DELIVERY
	 *  
	 *  @see SSCheckoutController::displayAddressStep();
	 *  @see SSCheckoutController::handleAddressStep();
	 *  @see SSCheckoutController::isAddressStepOK();
	 *  @see SSCheckoutController::helperAddressConf();
	 */
	public function displayAddressStep($action){
		$conf = $this->helperAddressConf($action);
		$_table = $conf['table'];
		$_showIn = $conf['showIn'];
		$_formPropertiesAndValues = $conf['formPropertiesAndValues'];
		$_addressFromSession = $conf['addressFromSession'];
		$_action = $conf['action'];
		$_formId = $conf['formId'];
		
		
		$this->view->displayMessage(
			SSHelper::i18n(self::ACTION_STEP.'_'.$_action)
		);
		
		$params = array();
		$params['formPropertiesAndValues'] = $this->formPropertiesAndValues;
		$params['formPropertyValueErrors'] = $this->formPropertyValueErrors;
		
		if(!$this->isFormActionName($_action)){
			$params['formPropertiesAndValues'] = $_addressFromSession;
			if($_action == self::ACTION_BILLING){
				$billAddr = $this->session->get('checkoutBillingAddress');
				if(empty($billAddr) and $this->customerLoginCtrl->isUserLoggedIn()){
					// Käufer Daten nach ID aus dem DB laden
					$customer = new SSCustomer();
					$customer->loadById($this->customerLoginCtrl->getLoggedInUserId());
					$params['formPropertiesAndValues'] 
						= SSOrder::convertCustomerAddrToBillingAddr($customer->getAddress());
				}
			}
		}
		
		$params['label_errors'] = array();
		foreach($params['formPropertyValueErrors'] as $f){
			foreach($f as $name => $val){
				$params['label_errors'][$name] = SSHelper::i18n('label_error_'.$name);
			}
		}
		// Formular
		$params['fields'] = SSHelper::getFormProperties(
			$_formId , $_table , $_showIn
		);
		
		$params['label_submit'] = SSHelper::i18n('label_checkout_next');
		$params['action'] = $_action;
		
		$params['show_diff_delivery_option'] = false;
		if($action == self::ACTION_REGISTER or $_action == self::ACTION_BILLING)
			$params['show_diff_delivery_option'] = true;
			
		
		//$this->checkoutView->displayCheckoutByTmpl($_action, $params);
		$this->checkoutView->displayCheckoutByTmpl('address', $params);
	}
	
	/** @brief Adresse Login-Handler
	 *  
	 *  Die Schritte mit Registrieren und Lieferadresse
	 *  , sowie für bereits eingeloggt Benutzer,
	 *  die Rechnungs- und Lieferadresse Logik Handler
	 *  
	 *  Die durch Formular eingegebene Adresse (+Daten)
	 *  werden auf Richtigkeit überprüft und in der
	 *  Session abgelegt.
	 *  
	 *  Variante 1 (Neukunde): Registrieren + Lieferadresse
	 *  
	 *  Variante 2: Rechnungs- + Lieferadresse
	 *  
	 *  @param $action : self::ACTION_REGISTER
	 * 					 self::ACTION_BILLING
	 * 					 self::ACTION_DELIVERY
	 *  
	 *  @see SSCheckoutController::displayAddressStep();
	 *  @see SSCheckoutController::handleAddressStep();
	 *  @see SSCheckoutController::isAddressStepOK();
	 *  @see SSCheckoutController::helperAddressConf();
	 */
	public function handleAddressStep($action){
		$conf = $this->helperAddressConf($action);
		$_table = $conf['table'];
		$_showIn = $conf['showIn'];
		$_addressFromSession = $conf['addressFromSession'];
		$_action = $conf['action'];
		$_formId = $conf['formId'];
		
		if($this->isFormActionName($_action)){
			$this->formPropertyValueErrors = SSHelper::checkFromInputs(
				$_table
				, $_showIn
				, $this->formPropertiesAndValues
			);
			//d($this->formPropertyValueErrors);
			if(sizeof($this->formPropertyValueErrors) < 1){
				if($_action == self::ACTION_REGISTER){
					$this->session->set('checkoutRegisterAddress', $this->formPropertiesAndValues);
					
					$checkoutBillingAddress = SSOrder::convertCustomerAddrToBillingAddr($this->formPropertiesAndValues);
					$this->session->set('checkoutBillingAddress', $checkoutBillingAddress);
					
					if($this->formPropertiesAndValues['diff_delivery'] == 'yes'){
						$this->nextStep();
					}else{
						$checkoutDeliveryAddress = SSOrder::convertCustomerAddrToDeliveryAddr($this->formPropertiesAndValues);
						$this->session->set('checkoutDeliveryAddress', $checkoutDeliveryAddress);
						$this->nextStep();
						$this->nextStep();
					}
				}elseif($_action == self::ACTION_BILLING){
					$this->session->set('checkoutBillingAddress', $this->formPropertiesAndValues);
									
					if($this->formPropertiesAndValues['diff_delivery'] == 'yes'){
						$this->nextStep();
					}else{
						$billAddress = $this->session->get('checkoutBillingAddress');
						$deliveryAddress = SSOrder::convertBillAddrToDeliverAddr($billAddress);
						$this->session->set('checkoutDeliveryAddress', $deliveryAddress);
						$this->nextStep();
						$this->nextStep();
					}
				}elseif($_action == self::ACTION_DELIVERY){
					$this->session->set('checkoutDeliveryAddress', $this->formPropertiesAndValues);
					$this->nextStep();
				}
			}
		}
	}
	
	/** @brief Hilfsfunktion Adresse
	 *
	 *  Enthält die Zuweisung der Variablen
	 *  welche je nach Aktion varieren.
	 *  
	 *  @see SSCheckoutController::displayLoginStep();
	 *  @see SSCheckoutController::handleLoginStep();
	 */
	function helperAddressConf($action){
		switch ($action){
			case self::ACTION_REGISTER:
				return array(
					'table' => SSCustomer::TABLE
					, 'formId' => SSCustomerRegisterView::FORM_ID
					, 'showIn' => SSDBSchema::SHOW_IN_REGISTER
					, 'formPropertiesAndValues' => $this->formPropertiesAndValues
					, 'addressFromSession' => $this->session->get('checkoutRegisterAddress')
					//, 'addressFromSession' => $this->session->get('checkoutBillingAddress')
					, 'action' => self::ACTION_REGISTER
				);
			case self::ACTION_BILLING:
				return array(
					'table' => SSOrder::TABLE
					, 'formId' => SSCheckoutView::FORM_ID
					, 'showIn' => SSDBSchema::SHOW_IN_BILL_ADDRESS
					, 'formPropertiesAndValues' => $this->formPropertiesAndValues
					, 'addressFromSession' => $this->session->get('checkoutBillingAddress')
					, 'action' => self::ACTION_BILLING
				);
			case self::ACTION_DELIVERY:
				return array(
					'table' => SSOrder::TABLE
					, 'formId' => SSCheckoutView::FORM_ID
					, 'showIn' => SSDBSchema::SHOW_IN_DELIVER_ADDRESS
					, 'formPropertiesAndValues' => $this->formPropertiesAndValues
					, 'addressFromSession' => $this->session->get('checkoutDeliveryAddress')
					, 'action' => self::ACTION_DELIVERY
				);
				break;
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
			if($this->customerLoginCtrl->isUserLoggedIn()){
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
			SSHelper::i18n(self::ACTION_STEP.'_'.self::ACTION_LOGIN)
		);
		$this->customerLoginCtrl->displayView();
		
		
		$this->view->displayMessage(
			SSHelper::i18n(self::ACTION_STEP.'_go_for_register')
		);

		$params = array();
		$params['label_submit'] = SSHelper::i18n('label_checkout_next');
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
		$this->customerLoginCtrl->loginLogoutHandler();
		
		if($this->customerLoginCtrl->isUserLoggedIn()){
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