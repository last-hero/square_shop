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
		}elseif(!$this->isRegisterStepOK()){
			$this->setStep(2);
		}elseif(!$this->isBillingStepOK()){
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
				if($this->customerLoginCtrl->isUserLoggedIn()){
					$this->handleBillingStep();
				}else{
					$this->handleRegisterStep();
				}
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
					$this->displayBillingStep();
				}else{
					$this->displayRegisterStep();
				}
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
		$cartCtrl = new SSCartController();
		$cartCtrl->simpleView = 1;
		$cartCtrl->displayView();
		
		
		$payment = $this->session->get('checkoutPayment');
		
		$params = array();
		
		$params['label_submit'] = SSHelper::i18n('label_checkout_confirm');
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
			SSHelper::i18n(self::ACTION_STEP.'_'.self::ACTION_DELIVERY)
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
				$params['label_errors'][$name] = SSHelper::i18n('label_error_'.$name);
			}
		}
		
		// Delivery Adress - Formular
		$params['fields'] = SSHelper::getFormProperties(
			SSCheckoutView::FORM_ID
			, SSOrder::TABLE
			, SSDBSchema::SHOW_IN_DELIVER_ADDRESS
		);
		
		$params['label_submit'] = SSHelper::i18n('label_checkout_next');
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
				SSOrder::TABLE
				, SSDBSchema::SHOW_IN_DELIVER_ADDRESS
				, $this->formPropertiesAndValues
			);
			if(sizeof($this->formPropertyValueErrors) < 1){
				$this->session->set('checkoutDeliveryAddress', $this->formPropertiesAndValues);
				$this->nextStep();
			}
		}
	}
	
	
	/** @brief Prüfen ob Billing Step ok
	 *
	 *  Überprüfen ob der Billing-Step richtig
	 *  ausgeführt wurde.
	 *  Dabei wird die Rechnungsadresse, welche in der
	 *  Session ausgelagert wurde, nach Richtigkeit
	 *  überprüft.
	 *  
	 *  @see SSCheckoutController::displayBillingStep();
	 *  @see SSCheckoutController::handleBillingStep();
	 *  @see SSCheckoutController::isBillingStepOK();
	 */
	public function isBillingStepOK(){
		/*
		$this->session->remove('checkoutOrderId');
		$this->session->remove('checkoutOrderDone');
		$this->session->remove('checkoutPayment');
		$this->session->remove('checkoutRegisterAddress');
		$this->session->remove('checkoutBillingAddress');
		$this->session->remove('checkoutDeliveryAddress');
		*/
		//$this->session->remove('checkoutLoginStepBy');
		//$this->session->remove('checkoutStep');
		
		$address = $this->session->get('checkoutBillingAddress');
		if(is_array($address)){
			$errors = SSHelper::checkFromInputs(
				SSCustomer::TABLE
				, SSDBSchema::SHOW_IN_BILL_ADDRESS
				, $address
			);
			if(sizeof($errors) < 1){
				return true;
			}
		}
		
		return false;
	}
	
	/** @brief Rechnungsadresse-Maske anzeigen
	 *
	 *  Die Felder der Rechnungsadresse-Maske aus dem
	 *  SSSchema Klasse holen und das Formular darstellen.
	 *  
	 *  @see SSCheckoutController::displayBillingStep();
	 *  @see SSCheckoutController::handleBillingStep();
	 *  @see SSCheckoutController::isBillingStepOK();
	 */
	public function displayBillingStep(){
		
		$this->view->displayMessage(
			SSHelper::i18n(self::ACTION_STEP.'_'.self::ACTION_BILLING)
		);
		
		$params = array();
		$params['formPropertiesAndValues'] = $this->formPropertiesAndValues;
		$params['formPropertyValueErrors'] = $this->formPropertyValueErrors;
		
		if(!$this->isFormActionName(self::ACTION_BILLING)){
			$params['formPropertiesAndValues'] = $this->session->get('checkoutBillingAddress');
			
			$billAddr = $this->session->get('checkoutBillingAddress');
			if(empty($billAddr) and $this->customerLoginCtrl->isUserLoggedIn()){
				// Käufer ID holen
				$customerId = $this->customerLoginCtrl->getLoggedInUserId();
				
				// Käufer Daten nach ID aus dem DB laden
				$customer = new SSCustomer();
				$customer->loadById($customerId);
				$address = $customer->getAddress();
					
				$checkoutBillingAddress = SSOrder::convertCustomerAddrToBillingAddr($address);
				//$this->session->set('checkoutBillingAddress', $checkoutBillingAddress);
				$params['formPropertiesAndValues'] = $checkoutBillingAddress;
			}
		}
		$params['label_errors'] = array();
		foreach($params['formPropertyValueErrors'] as $f){
			foreach($f as $name => $val){
				$params['label_errors'][$name] = SSHelper::i18n('label_error_'.$name);
			}
		}
		
		// Billing Adress - Formular
		$params['fields'] = SSHelper::getFormProperties(
			SSCheckoutView::FORM_ID
			, SSOrder::TABLE
			, SSDBSchema::SHOW_IN_BILL_ADDRESS
		);
		
		$params['label_submit'] = SSHelper::i18n('label_checkout_next');
		$params['action'] = self::ACTION_BILLING;
		
		$this->checkoutView->displayCheckoutByTmpl(self::ACTION_BILLING, $params);
	}
	
	/** @brief Rechnungsadresse
	 *
	 *  Hier wird die Rechnungsadresse auf richtigkeit
	 *  überprüft und zum nächsten Step weitergeleitet,
	 *  falls die Adresse korrekt eingegeben wurde.
	 *  Zudem wird die Rechnungsadresse in Session ausgelagert.
	 *  
	 *  @see SSCheckoutController::displayBillingStep();
	 *  @see SSCheckoutController::handleBillingStep();
	 *  @see SSCheckoutController::isBillingStepOK();
	 */
	public function handleBillingStep(){
		if($this->isFormActionName(self::ACTION_BILLING)){
			$this->formPropertyValueErrors = SSHelper::checkFromInputs(
				SSOrder::TABLE
				, SSDBSchema::SHOW_IN_BILL_ADDRESS
				, $this->formPropertiesAndValues
			);
			if(sizeof($this->formPropertyValueErrors) < 1){
				$this->session->set('checkoutBillingAddress', $this->formPropertiesAndValues);
								
				if($this->formPropertiesAndValues['diff_delivery'] == 'yes'){
					$this->nextStep();
				}else{
					$billAddress = $this->session->get('checkoutBillingAddress');
					$deliveryAddress =SSOrder::convertBillAddrToDeliverAddr($billAddress);
					$this->session->set('checkoutDeliveryAddress', $deliveryAddress);
					$this->nextStep();
					$this->nextStep();
				}
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
				SSOrder::TABLE
				, SSDBSchema::SHOW_IN_BILL_ADDRESS
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
			SSHelper::i18n(self::ACTION_STEP.'_'.self::ACTION_REGISTER)
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
				$params['label_errors'][$name] = SSHelper::i18n('label_error_'.$name);
			}
		}
		// Register Formular
		$params['fields'] = SSHelper::getFormProperties(
			SSCustomerRegisterView::FORM_ID
			, SSCustomer::TABLE
			, SSDBSchema::SHOW_IN_REGISTER
		);
		
		$params['label_submit'] = SSHelper::i18n('label_checkout_next');
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