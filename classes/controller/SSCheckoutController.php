<?php
/** @file SSCheckoutController.php
 *  @brief Einkauf Verwalten
 *
 *  Diese Klasse verwaltet den ganzen Einkauf
 *
 *
 *  @todo Mail an Käufer + Shop-Betreiber nach erfolgreicher Bestellung
 *
 *  @author Gobi Selva
 *  @author http://www.square.ch
 *  @author https://github.com/last-hero/square_shop
 */

class SSCheckoutController extends SSController{
	const ACTION_STEP					= 'checkout_step';
	
	const ACTION_LOGIN				   = 'login';
	const ACTION_GO_FOR_REGISTER		 = 'checkout_go_for_register';
	const ACTION_REGISTER				= 'register';
	const ACTION_BILLING				 = 'billing';
	const ACTION_DELIVERY				= 'delivery';
	const ACTION_SELECT_PAYMENT		  = 'selectpayment';
	const ACTION_EXE_PAYMENT			 = 'exe_payment';
	const ACTION_ORDER				   = 'order';
	
	const PAYMENT_ONBILL				 = 'onbill';
	
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
	
	private $paymentsPerAPI = array('paypal', 'saferpay');
	
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
		$handlePaymentPerAPI		 = rex_get('handlePaymentPerAPI', 'string', '');
		$handleExecutePaymentStep	= rex_get('handleExecutePaymentStep', 'string', '');
		if($handlePaymentPerAPI){
			$this->handlePaymentPerAPI();
		}elseif($handleExecutePaymentStep){
			$this->handleExecutePaymentStep();
			if($this->customerLoginCtrl->isUserLoggedIn()){
				$this->displayConfirmStep();
			}else{
				$this->customerLoginCtrl->displayView();
			}
		}else{
			if($this->cartCtrl->isCartEmpty()){
				$this->view->displaySuccessMessage(SSHelper::i18n('cart_is_empty'));
			}else{
				$this->checkoutHandler();
				if($this->getStep() < 6)
					$this->displayStepView();
				$this->checkoutViewHandler();
			}
		}
	}
	
	/*
	* Warenkorb Handler: Add to Cart, Remove from Cart, Menge ändern
	*/
	public function checkoutHandler(){
		if((int)$this->formPropertiesAndValues['jumpToStep'] > 0){
			$this->setStep($this->formPropertiesAndValues['jumpToStep']);
		}
		
		if($this->isConfirmStepOK()){
			$this->setStep(7);
		}elseif($this->isExecutePaymentStepOK()
		and $this->getStep() == 6){
			$this->setStep(6);
		}if(!$this->isLoginStepOK()){
			$this->setStep(1);
		}elseif(!$this->customerLoginCtrl->isUserLoggedIn()
		and !$this->isAddressStepOK(self::ACTION_REGISTER)
		and $this->getStep() > 2){
			$this->setStep(2);
		}elseif(!$this->isAddressStepOK(self::ACTION_BILLING)
		and $this->getStep() > 2){
			$this->setStep(2);
		}elseif(!$this->isAddressStepOK(self::ACTION_DELIVERY)
		and $this->getStep() > 3){
			$this->setStep(3);
		}elseif(!$this->isSelectPaymentStepOK()
		and $this->getStep() > 4){
			$this->setStep(4);
		}elseif(!$this->isOrderStepOK()
		and $this->getStep() > 5){
			$this->setStep(5);
		}elseif(!$this->isExecutePaymentStepOK()
		and $this->getStep() > 6){
			$this->setStep(6);
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
				$this->handleSelectPaymentStep();
				break;
			case 5:
				$this->handleOrderStep();
				if($this->getStep() == 7){
					$this->handleConfirmStep();	
				}
				break;
			case 6:
				$this->handleExecutePaymentStep();
				/*
				if($this->getStep() == 7){
					$this->handleConfirmStep();	
				}
				*/
				break;
			case 7:
				$this->handleConfirmStep();
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
				$this->displayAddressStep(self::ACTION_DELIVERY);
				break;
			case 4:
				$this->displaySelectPaymentStep();
				break;
			case 5:
				$this->displayOrderStep();
				break;
			case 6:
				$this->displayExecutePaymentStep();
				break;
			case 7:
				$this->displayConfirmStep();
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
		$this->session->remove('checkout');
	}
	
	public function displayStepView(){
		$params = array();
		for($x=1; $x<=5; $x++){
			$params['label_steps'][$x] = SSHelper::i18n('label_step'.$x.'_title');
		}
		$params['step_active'] = $this->getStep();
		$this->checkoutView->displayCheckoutStepHtml($params);
	}
	
	/** @brief Title
	 *
	 *  
	 *  @see SSCheckoutController::displayConfirmStep();
	 *  @see SSCheckoutController::handleConfirmStep();
	 *  @see SSCheckoutController::isConfirmStepOK();
	 */
	public function isConfirmStepOK(){
		$payment = $this->getSession('SelectPayment');
		if($payment == self::PAYMENT_ONBILL){
			if($this->getSession('OrderId') and $this->getSession('OrderCompleted')){
				return true;
			}
		}elseif(in_array($payment, $this->paymentsPerAPI)){
			if($this->getSession('OrderId') 
			and $this->getSession('OrderCompleted')
			and $this->getSession('OrderIsPaid')){
				return true;
			}
		}
		return false;
	}
	
	/** @brief Title
	 *
	 *  
	 *  @see SSCheckoutController::displayConfirmStep();
	 *  @see SSCheckoutController::handleConfirmStep();
	 *  @see SSCheckoutController::isConfirmStepOK();
	 */
	public function displayConfirmStep(){
		$orderId = $GLOBALS['checkout']['OrderId'];
		$order = new SSOrder();
		if($order->loadById($orderId)){
			if(in_array($order->get('payment'), $this->paymentsPerAPI)){
				// Meldung anzeigen, wenn Zahlung erfolgreich
				if((int)$order->get('payment_status') == 1){
					$msg = SSHelper::i18n('checkout_order_with_payment_success');
					$msg = str_replace('%order_no%', $order->get('no'), $msg);
					$this->view->displaySuccessMessage($msg);
				}else{
					// Meldung anzeigen, wenn Zahlung fehlgeschlagen
					$msg = SSHelper::i18n('checkout_order_with_payment_error');
					$msg = str_replace('%order_no%', $order->get('no'), $msg);
					$this->view->displayErrorMessage($msg);
				}
			}else{
				// Meldung anzeigen, wenn Bestellung erfolgreich
				// --> auf Rechnung
				$msg = SSHelper::i18n('checkout_order_success');
				$msg = str_replace('%order_no%', $order->get('no'), $msg);
				$this->view->displaySuccessMessage($msg);
			}
		}else{
			// Meldung anzeigen, wenn Bestellung fehlgeschlagen
			$msg = SSHelper::i18n('checkout_order_error');
			$this->view->displayErrorMessage($msg);
		}
	}
	
	/** @brief Title
	 *
	 *  
	 *  @see SSCheckoutController::displayConfirmStep();
	 *  @see SSCheckoutController::handleConfirmStep();
	 *  @see SSCheckoutController::isConfirmStepOK();
	 */
	public function handleConfirmStep(){
		$GLOBALS['checkout'] = array(
			'OrderId' => $this->getSession('OrderId')
			, 'OrderCompleted' => $this->getSession('OrderCompleted')
			, 'SelectPayment' => $this->getSession('SelectPayment')
			, 'Step' => $this->getStep()
		);
		
				
		/* --------------------------------------------------------------
		// Todo Better Mail
		// Mail an Shop-Betreier + Käufer
		- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
		$orderId = $this->getSession('OrderId');
		$this->mailOrder($orderId);
		/* ------------------------------------------------------------ */
		
		$this->cartCtrl->clearCart();
		$this->clearAll();
	}
	
	/** @brief Title
	 *
	 *  
	 *  @see SSCheckoutController::displayExecutePaymentStep();
	 *  @see SSCheckoutController::handleExecutePaymentStep();
	 *  @see SSCheckoutController::isExecutePaymentStepOK();
	 */
	public function isExecutePaymentStepOK(){
		return false;
	}
	
	/** @brief Title
	 *
	 *  
	 *  @see SSCheckoutController::displayExecutePaymentStep();
	 *  @see SSCheckoutController::handleExecutePaymentStep();
	 *  @see SSCheckoutController::isExecutePaymentStepOK();
	 */
	public function displayExecutePaymentStep(){
		$payment	= rex_get('payment', 'string', '');
		
		$order = new SSOrder();
		if($order->loadById($this->getSession('OrderId'))){
			$payment = $this->getSession('SelectPayment');
			
			$this->view->displayMessage(
				SSHelper::i18n(self::ACTION_STEP.'_'.self::ACTION_EXE_PAYMENT.'_'.$payment)
			);
			
			$params = array();
			
			$params['label_submit'] = SSHelper::i18n('label_execute_payment');
			$params['action'] = self::ACTION_EXE_PAYMENT;
			
			$params['payment'] = $payment;
			
			$params['step'] = $this->getStep();
			$this->checkoutView->displayCheckoutByTmpl(self::ACTION_ORDER.'.'.$payment, $params);
			//$this->checkoutView->displayCheckoutByTmpl(self::ACTION_ORDER, $params);
		}elseif($payment){
		}
	}
	
	/** @brief Title
	 *
	 *  
	 *  @see SSCheckoutController::displayExecutePaymentStep();
	 *  @see SSCheckoutController::handleExecutePaymentStep();
	 *  @see SSCheckoutController::isExecutePaymentStepOK();
	 */
	public function handleExecutePaymentStep(){
		global $REX;
		$payment	= rex_get('payment', 'string', '');
		//$payment = $this->getSession('SelectPayment');
		if($payment == 'paypal'){
			$sid = rex_get('sid', 'string', '');
			$orderId = 0;
			$order = new SSOrder();
			$orderDbData = $order->_getWhere('sid = "'.$sid.'"', SSDBSchema::SHOW_IN_PAYMENT);
			if(count($orderDbData) == 1){
				$orderId = (int)$orderDbData[0]['id'];
			}
			$order->loadById($orderId);
			if($orderId > 0 and $this->customerLoginCtrl->isUserLoggedIn()
			and $order->get('customer_id') == $this->customerLoginCtrl->getLoggedInUserId()){
				$customer = new SSCustomer();
				$GLOBALS['checkout'] = array(
					'OrderId' => $orderId
					, 'OrderCompleted' => true
					, 'CustomerId' => $this->customerLoginCtrl->getLoggedInUserId()
					, 'SelectPayment' => $orderDbData['payment']
					, 'PaymentStatus' => $orderDbData['payment_status']
					, 'PayerEmail' => $orderDbData['payer_email']
					, 'Step' => 7
				);
				
				
				
				/* --------------------------------------------------------------
				// Todo Better Mail
				// Mail an Shop-Betreier + Käufer
				- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
				$this->mailOrder($orderId);
				/* ------------------------------------------------------------ */
			}
			$this->cartCtrl->clearCart();
			$this->clearAll();
		}else{
			$payment = $this->getSession('SelectPayment');
			if($payment == self::PAYMENT_ONBILL){
				if($this->getSession('OrderId') and $this->getSession('OrderCompleted')){
					$this->nextStep();
				}else{
					$this->prevStep();
				}
			}
		}
	}
	
	/** @brief Title
	 *
	 */
	public function handlePaymentPerAPI(){
		$payment	= rex_get('payment', 'string', '');		
		if($payment == 'paypal'){
			$paypal = new SSPayPalController();
			$paypal->handlePayment();
		}
	}
	
	
	/** @brief Prüfen ob Zahlungsart ausgewählt
	 *
	 *  
	 *  @param $orderId
	 */
	public function mailOrder($orderId){
		/* --------------------------------------------------------------
		// Todo Better Mail
		// Mail an Shop-Betreier + Käufer
		- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
		$orders_mail_to = SSHelper::getSetting('orders_mail_to');
		
		$order = new SSOrder();
		$order->loadById($orderId);
		$orderItem = new SSOrderItem();
		$orderItemsArray = $orderItem->getByForeignId($orderId, SSOrder::TABLE);
		
		$orders_mail_to_customer = $order->get('billing_email');
		$order_no = $order->get('no');
		
		$output = 'Guten Tag ';
		$output .= $order->get('billing_firstname').' '.$order->get('billing_lastname');
		$output .= "\r\n";
		$total = 0;
		for($x=0; $x<count($orderItemsArray); $x++){
			$subtotal = (int)$orderItemsArray[$x]['qty'] * (int)$orderItemsArray[$x]['price'];
			$total += $subtotal;
			$output .= "\n".'ArtNr.: '.$orderItemsArray[$x]['no'];
			$output .= "\n".'Artikel: '.$orderItemsArray[$x]['title'];
			$output .= "\n".'Preis: '.$orderItemsArray[$x]['price'];
			$output .= "\n".'Menge: '.$orderItemsArray[$x]['qty'];
			$output .= "\n".'Subtotal: '.number_format($subtotal, 2, '.', '');
			$output .= "\n";
			$output .= "- - - - - - - - - - - - - - - - - - - - - - - -";
		}
		$output .= "\n";
		$output .= "- - - - - - - - - - - - - - - - - - - - - - - -";
		$output .= "\n".'Total: '.number_format($total, 2, '.', '');
		$output .= "\r\n";
		$output .= "\r\n";
				
		mail($orders_mail_to,"Bestellung - ".$order_no,$output);
		mail($orders_mail_to_customer,"Bestellung - ".$order_no,$output);
		/* ------------------------------------------------------------ */
	}
	
	
	/** @brief Prüfen ob Zahlungsart ausgewählt
	 *
	 *  
	 *  @see SSCheckoutController::displayOrderStep();
	 *  @see SSCheckoutController::handleOrderStep();
	 *  @see SSCheckoutController::isOrderStepOK();
	 */
	public function isOrderStepOK(){
		$payment = $this->getSession('SelectPayment');
		if($this->getSession('OrderId') and ($this->getSession('OrderCompleted') or in_array($payment, $this->paymentsPerAPI))){
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
		
		
		$payment = $this->getSession('SelectPayment');
		
		$this->view->displayMessage(
			SSHelper::i18n('label_'.$payment.'')
			, SSHelper::i18n('label_checkout_payment')
		);		
		
		
		
		$params = array();
		$params['label_submit'] = SSHelper::i18n('label_checkout_confirm');
		$params['action'] = self::ACTION_ORDER;
		$params['step'] = $this->getStep();
		$params['showGoBackBtn'] = true;
		
		$params['payment'] = $payment;
		//$this->checkoutView->displayCheckoutByTmpl(self::ACTION_ORDER.'.'.$payment, $params);
		$this->checkoutView->displayCheckoutByTmpl(self::ACTION_ORDER, $params);
	}
	
	/** @brief Title
	 *
	 *  
	 *  @see SSCheckoutController::displayOrderStep();
	 *  @see SSCheckoutController::handleOrderStep();
	 *  @see SSCheckoutController::isOrderStepOK();
	 *  @see SSCheckoutController::saveOrderToDb();
	 */
	public function handleOrderStep(){
		$orderCompleted = $this->getSession('OrderCompleted');
		if(!$orderCompleted){
			if($this->isFormActionName(self::ACTION_ORDER)){
				$payment = $this->getSession('SelectPayment');
				if($payment == self::PAYMENT_ONBILL){
					// Bestellung in DB ablegen
					if(!$this->getSession('OrderId')){
						$this->saveOrderToDb();
						$order = new SSOrder();
						if($order->loadById($this->getSession('OrderId'))){
							$orderCompleted = true;
							$this->nextStep();
							$this->nextStep();
						}
					}
				}elseif($payment == 'paypal'){
					// Bestellung in DB ablegen
					if(!$this->getSession('OrderId')){
						$this->saveOrderToDb();
						$order = new SSOrder();
						if($order->loadById($this->getSession('OrderId'))){
							$order->set('sid', SSHelper::generateHash());
							try{
								$order->save();
								$this->nextStep();
							}catch(SSException $e) {
								echo $e;
							}
						}
					}
				}
			}
		}
		if($orderCompleted){
			$this->setSession('OrderCompleted', $orderCompleted);
		}
		return false;
	}
	
	
	/** @brief Bestellung speichern
	 *
	 *  Bestellun in der Datenbank ablegen.
	 *  
	 *  @see SSCheckoutController::displayOrderStep();
	 *  @see SSCheckoutController::handleOrderStep();
	 *  @see SSCheckoutController::isOrderStepOK();
	 *  @see SSCheckoutController::saveOrderToDb();
	 */
	public function saveOrderToDb(){
		$payment = $this->getSession('SelectPayment');
				
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
			$registerData = $this->getSession('RegisterAddress');
			$customer = new SSCustomer();
			$customer->set($customer->getClearedUnknownProperties($registerData));
			$customer->save();
			//$registerData['id'] = $customer->get('id');
			//$this->setSession('BillingAddress', $registerData);
			$customerId = $customer->get('id');
			if((int)$customerId > 0){
				$userName = $customer->get('firstname').' '.$customer->get('lastname');
				$this->customerLoginCtrl->loginUser($customerId, $userName);
			}
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
		$billingAddress = $this->getSession('BillingAddress');
		unset($billingAddress['id']);
		$order->set($order->getClearedUnknownProperties($billingAddress));
		
		// Lieferadresse
		$deliveryAddress = $this->getSession('DeliveryAddress');
		unset($deliveryAddress['id']);
		$order->set($order->getClearedUnknownProperties($deliveryAddress));
		
		// Customer ID
		$order->set('customer_id', $customer->get('id'));
		
		// Order No
		$order->set('no', date("Y-m-d").' '.(time() - strtotime("today")));
		
		// Order Date
		$order->set('date',time());
		
		// SelectPayment
		$order->set('payment', $payment);
		
		// Datum setzen
		$order->set('date', date("Y-m-d H:i:s"));
		
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
		// Artikeln aus DB gefiltert nach PrimaryKeys (IDs)
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
		
		$this->setSession('OrderId', $order->get('id'));
	}
	
	
	/** @brief Prüfen ob Zahlungsart ausgewählt
	 *
	 *  
	 *  @see SSCheckoutController::displaySelectPaymentStep();
	 *  @see SSCheckoutController::handleSelectPaymentStep();
	 *  @see SSCheckoutController::isSelectPaymentStepOK();
	 */
	public function isSelectPaymentStepOK(){
		$payment = $this->getSession('SelectPayment');
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
	 *  @see SSCheckoutController::displaySelectPaymentStep();
	 *  @see SSCheckoutController::handleSelectPaymentStep();
	 *  @see SSCheckoutController::isSelectPaymentStepOK();
	 */
	public function displaySelectPaymentStep(){
		$payments = SSHelper::getSetting('payment');
		
		$this->view->displayMessage(
			SSHelper::i18n(self::ACTION_STEP.'_'.self::ACTION_SELECT_PAYMENT)
		);
			
		$params = array();
		$params['formPropertiesAndValues'] = $this->formPropertiesAndValues;
		$params['formPropertyValueErrors'] = $this->formPropertyValueErrors;
		$params['payments'] = $payments;
		
		$params['label_submit'] = SSHelper::i18n('label_checkout_next');
		$params['action'] = self::ACTION_SELECT_PAYMENT;
		$params['step'] = $this->getStep();
		$params['showGoBackBtn'] = true;
		
		if(!$this->getSession('DiffDelivery'))
			$params['jumpToStep'] = $this->getStep() - 2;
		
		$this->checkoutView->displayCheckoutByTmpl(self::ACTION_SELECT_PAYMENT, $params);
	}
	
	/** @brief Zahlungsart verwalten
	 *
	 *  
	 *  @see SSCheckoutController::displaySelectPaymentStep();
	 *  @see SSCheckoutController::handleSelectPaymentStep();
	 *  @see SSCheckoutController::isSelectPaymentStepOK();
	 */
	public function handleSelectPaymentStep(){
		if($this->isFormActionName(self::ACTION_SELECT_PAYMENT)){
			$payment = $this->formPropertiesAndValues[self::ACTION_SELECT_PAYMENT];
			if(strlen(trim($payment))){
				$payments = SSHelper::getSetting('payment');
				if(in_array($payment, $payments)){
					$this->setSession('SelectPayment', $payment);
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
	public function isAddressStepOK($action){
		if($action == self::ACTION_DELIVERY and !$this->getSession('DiffDelivery'))
			return true;
		$conf = $this->helperAddressConf($action);
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
		$params['showRequiredFieldsInfo'] = true;
		
		if(!$this->isFormActionName($_action)){
			$params['formPropertiesAndValues'] = $_addressFromSession;
			if($_action == self::ACTION_BILLING){
				$billAddr = $this->getSession('BillingAddress');
				if(empty($billAddr) and $this->customerLoginCtrl->isUserLoggedIn()){
					// Käufer Daten nach ID aus dem DB laden
					$customer = new SSCustomer();
					$customer->loadById($this->customerLoginCtrl->getLoggedInUserId());
					$params['formPropertiesAndValues'] 
						= SSOrder::convertCustomerAddrToBillingAddr($customer->getAddress());
				}
			}
		}
		
		/*
		$params['label_errors'] = array();
		foreach($params['formPropertyValueErrors'] as $f){
			foreach($f as $name => $val){
				$params['label_errors'][$name] = SSHelper::i18n('label_error_'.$name);
			}
		}
		*/
		// Formular
		$params['fields'] = SSHelper::getFormProperties(
			$_formId , $_table , $_showIn
		);
		
		$params['label_submit'] = SSHelper::i18n('label_checkout_next');
		$params['action'] = $_action;
		$params['step'] = $this->getStep();
		
		$params['showDiffDeliveryAddrOption'] = false;
		if($action == self::ACTION_REGISTER or $_action == self::ACTION_BILLING)
			$params['showDiffDeliveryAddrOption'] = true;
			
		if($action != self::ACTION_BILLING)
			$params['showGoBackBtn'] = true;
		
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
					$this->setSession('RegisterAddress', $this->formPropertiesAndValues);
					
					$checkoutBillingAddress = SSOrder::convertCustomerAddrToBillingAddr($this->formPropertiesAndValues);
					$this->setSession('BillingAddress', $checkoutBillingAddress);
					
					if($this->formPropertiesAndValues['diff_delivery'] == 'yes'){
						$this->setSession('DiffDelivery', true);
						$this->nextStep();
					}else{
						$this->setSession('DiffDelivery', false);
						$checkoutDeliveryAddress = SSOrder::convertCustomerAddrToDeliveryAddr($this->formPropertiesAndValues);
						$this->setSession('DeliveryAddress', $checkoutDeliveryAddress);
						$this->nextStep();
						$this->nextStep();
					}
				}elseif($_action == self::ACTION_BILLING){
					$this->setSession('BillingAddress', $this->formPropertiesAndValues);
									
					if($this->formPropertiesAndValues['diff_delivery'] == 'yes'){
						$this->setSession('DiffDelivery', true);
						$this->nextStep();
					}else{
						$this->setSession('DiffDelivery', false);
						$billAddress = $this->getSession('BillingAddress');
						$deliveryAddress = SSOrder::convertBillAddrToDeliverAddr($billAddress);
						$this->setSession('DeliveryAddress', $deliveryAddress);
						$this->nextStep();
						$this->nextStep();
					}
				}elseif($_action == self::ACTION_DELIVERY){
					$this->setSession('DeliveryAddress', $this->formPropertiesAndValues);
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
					, 'addressFromSession' => $this->getSession('RegisterAddress')
					//, 'addressFromSession' => $this->getSession('BillingAddress')
					, 'action' => self::ACTION_REGISTER
				);
			case self::ACTION_BILLING:
				return array(
					'table' => SSOrder::TABLE
					, 'formId' => SSCheckoutView::FORM_ID
					, 'showIn' => SSDBSchema::SHOW_IN_BILL_ADDRESS
					, 'formPropertiesAndValues' => $this->formPropertiesAndValues
					, 'addressFromSession' => $this->getSession('BillingAddress')
					, 'action' => self::ACTION_BILLING
				);
			case self::ACTION_DELIVERY:
				return array(
					'table' => SSOrder::TABLE
					, 'formId' => SSCheckoutView::FORM_ID
					, 'showIn' => SSDBSchema::SHOW_IN_DELIVER_ADDRESS
					, 'formPropertiesAndValues' => $this->formPropertiesAndValues
					, 'addressFromSession' => $this->getSession('DeliveryAddress')
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
		if($this->getSession('LoginStepBy') == self::ACTION_LOGIN){
			if($this->customerLoginCtrl->isUserLoggedIn()){
				return true;
			}
		}elseif($this->getSession('LoginStepBy') == self::ACTION_GO_FOR_REGISTER){
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
		$params['step'] = $this->getStep();
		
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
			$this->setSession('LoginStepBy', self::ACTION_LOGIN);
		}
		if($this->isFormActionName(self::ACTION_GO_FOR_REGISTER)){
			$this->nextStep();
			$this->setSession('LoginStepBy', self::ACTION_GO_FOR_REGISTER);
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
		$this->setSession('Step', $step);
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
		$step = (int)$this->getSession('Step');
		if($step < 1 and (int)$GLOBALS['checkout']['Step'] > 0){
			$step = $GLOBALS['checkout']['Step'];
		}
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
		$step = (int)$this->getSession('Step');
		$step = $step < 1 ? 1 : $step;
		$step++;
		$this->setSession('Step', $step);
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
		$step = (int)$this->getSession('Step');
		$step--;
		$step = $step < 1 ? 1 : $step;
		$this->setSession('Step', $step);
		return $step;
	}
	
	/** @brief store Value in Session
	 *
	 *  Session Funktion um Session innerhalb von
	 *  Checkout zu verwalten.
	 *
	 *  Key + Value in Session ablegen.
	 *
	 *  @return bool
	 *
	 *  @see SSCheckoutController::setSession
	 *  @see SSCheckoutController::getSession
	 *  @see SSCheckoutController::removeSession
	 */
	public function setSession($key, $value){
		$values = $this->session->get('checkout');
		$values[$key] = $value;
		$this->session->set('checkout', $values);
		return true;
	}
	
	/** @brief get Value from Session
	 *
	 *  Session Funktion um Session innerhalb von
	 *  Checkout zu verwalten.
	 *
	 *  Wert nach Key in Session holen.
	 *
	 *  @return mixed
	 *
	 *  @see SSCheckoutController::setSession
	 *  @see SSCheckoutController::getSession
	 *  @see SSCheckoutController::removeSession
	 */
	public function getSession($key){
		$values = $this->session->get('checkout');
		return $values[$key];
	}
	
	/** @brief remove Key + Value from Session
	 *
	 *  Session Funktion um Session innerhalb von
	 *  Checkout zu verwalten.
	 *
	 *  Key + Value von der Session löschen.
	 *
	 *  @return bool
	 *
	 *  @see SSCheckoutController::setSession
	 *  @see SSCheckoutController::getSession
	 *  @see SSCheckoutController::removeSession
	 */
	public function removeSession($key){
		$values = $this->session->get('checkout');
		$values[$key] = null;
		unset($values[$key]);
		$this->session->set('checkout', $values);
		return true;
	}
}