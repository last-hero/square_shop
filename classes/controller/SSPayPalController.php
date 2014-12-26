<?php
/** @file SSPayPalController.php
 *  @brief PayPal Verwalten
 *
 *  Mit dieser Klasse wird die Zahlung
 *  über PayPal abgewickelt.
 *
 *  @author Gobi Selva
 *  @author http://www.square.ch
 *  @author https://github.com/last-hero/square_shop
 */

class SSPayPalController extends SSController{
	/**
	 * Bestellungs ID
	 */
	private $orderId;
	
	/**
	 * @see SSPayPalController::getConf
	 */
	private $sid;
	
	/**
	 * Debug Ja | Nein
	 * z.B. werden die Zahlungsinformationen
	 * im Logfile geschrieben.
	 */
	private $debug = 0;
	
	/**
	 * Das Verwenden von PayPal Sandbox Accounts.
	 * Dies wird für Testzwecken verwendet.
	 */
	private $useSandbox = 0;
	
	/**
	 * Pfad vom Logfile
	 */
	private $logfile;
	
	/**
	 * PayPal Url, sie unterscheidet sich wenn Sandbox
	 * für Testzwecken verwendet wird.
	 */
	public $paypalUrl;
	
	/** @brief Initialisierung
	 *
	 * Dieser Funktion wird im Konstruktor aufgerufen
	 * und dient für die Initialisierung der Values
	 * z.B. Logfile-Pfad, Debug-Modus aktiv|inaktiv 
	 * PayPal Url anhand von Sandbox aktiv|inaktiv,
	 * OrderId aus dem Session holen
	 */
	public function init(){
		global $REX;
		$this->logfile = SSHelper::getAddonDataPath() . '/paypal.ipn.log';
		//$this->logfile = SSHelper::getAddonPath() . '/logs/paypal.ipn.log';
		
		$this->debug = (int)SSHelper::getSetting('paypal_debug');
		$this->useSandbox = (int)SSHelper::getSetting('paypal_use_sandbox');
		
		if($this->useSandbox == true) {
			$this->paypalUrl = "https://www.sandbox.paypal.com/cgi-bin/webscr";
		} else {
			$this->paypalUrl = "https://www.paypal.com/cgi-bin/webscr";
		}
		
		$checkoutCtrl = new SSCheckoutController();
		$this->orderId = $checkoutCtrl->getSession('OrderId');
	}
	
	/** @brief Wichtige Konfigurationen
	 *
	 * business: PayPal-Account des Shop-betreibers
	 * 
	 * mail_errors_to: Mail Addresse für Fehlermeldungen
	 * 
	 * sid: Ein Hash-Value die für das Identifizieren
	 * von Bestellung über PayPal Zahlung
	 * verwendet wird.
	 * 
	 * currency: Die Währung, die bei der Zahlung
	 * über PayPal verwendet wird.
	 * 
	 * notify_url: Diese Url wird während der
	 * Zahlung über PayPal abgerufen, dabei wird
	 * sie im Hintergrund, während der Käufer
	 * sich noch auf der PayPal-Seite befindet,
	 * aufgerufen. D.h. die Bestellung vom Käufer
	 * wird durch den sid (Hash-Value) identifiziert
	 * und nicht durch OrderId. Hier wird auch in
	 * der DB vermerkt, ob die Zahlung erfolgreich
	 * war oder nicht.
	 * 
	 * return_url: Auf dieser Url wird weitergeleitet,
	 * sobald der Benutzer nach der Zahlungsvorgang
	 * auf der PayPal-Seite auf "zurück zum Shop"
	 * klickt.
	 * 
	 * invoice: Bestellungs Nummer - PayPal
	 * 
	 */
	public function getConf(){
		global $REX;
		
		$order = new SSOrder();
		if($order->loadById($this->orderId)){
			$this->sid = $order->get('sid');
		}
		$sid = $this->sid;
		return array(
			'business' 		 => SSHelper::getSetting('paypal_business')
			, 'mail_errors_to' => SSHelper::getSetting('paypal_mail_errors_to')
			, 'currency' 	   => SSHelper::getSetting('currency')
			, 'sid' 		    => $sid
			, 'return_url'     => $REX['SERVER'].rex_getUrl(
										REX_ARTICLE_ID, REX_CLANG_ID, 
										array(
											'ss-cart' => 'checkout'
											, 'payment' => 'paypal'
											, 'handleExecutePaymentStep' => 1
											, 'sid' => $sid
										)
									)
			, 'notify_url'     => $REX['SERVER'].rex_getUrl(
										REX_ARTICLE_ID, REX_CLANG_ID, 
										array(
											'ss-cart' => 'checkout'
											, 'ajax' => 2
											, 'handlePaymentPerAPI' => 1
											, 'payment' => 'paypal'
											, 'sid' => $sid
										)
									)
			, 'invoice' 	    => time().'_'.$sid
		);
	}
	
	/** @brief Bestellungsinformationen
	 *
	 * Alle Artikel, Subtotal, Total usw.
	 */
	public function getOrderInfoAndItems(){
		$orderItems = new SSOrderItem();
		$orderItems->getByForeignId($this->orderId, SSOrder::TABLE);
						
		$cartCtrl = new SSCartController();
		return $cartCtrl->getOverviewData();
	}
	
	/** @brief handlePayment
	 *
	 *  Dies Funktion wird aufgerufen, wenn
	 *  PayPal im Hintergrund den "notify_url"
	 *  aufruft.
	 *
	 *  Die Bestellung wird überprüft, ob die
	 *  Zahlung erfolgreich über PayPal bezahlt
	 *  wurde und dem entsprechend in der DB
	 *  vermerkt.
	 *
	 *  @see SSPayPalController::getConf
	 */
	public function handlePayment(){
		global $REX;
		$conf = $this->getConf();
		
		$receiver_email 	 = $conf['business'];
		$mail_errors_to 	 = $conf['mail_errors_to'];
		$mc_currency 	    = $conf['currency'];
		$sid				= rex_get('sid', 'string', '');
		$orderId		    = 0;
		$mc_gross 		   = 0;
		$payment_status 	 = 0;		
		
		/* --------------------------------------------------------------
		// Bestellungsinformationen aus DB holen
		- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
		$order = new SSOrder();
		$orderDbData = $order->_getWhere('sid = "'.$sid.'"', SSDBSchema::SHOW_IN_PAYMENT);
		if(count($orderDbData) == 1){
			$orderId = (int)$orderDbData[0]['id'];
		}
		if($order->loadById($orderId)){
			//$order->set('payment_response', serialize($_POST));
			//$order->save();
		}
		/* --------------------------------------------------------------
		- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
		
		/* --------------------------------------------------------------
		// Bestellung: Total Preis aus der DB holen
		- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
		if($orderId > 0){
			$orderItems = new SSOrderItem();
			$items = $orderItems->getByForeignId($orderId, SSOrder::TABLE);
			foreach($items as $item){
				$orderTotalPrice += (int)$item['price'] * (int)$item['qty'];
			}
			$orderTotalPrice = number_format($orderTotalPrice, 2, '.', '');
			$mc_gross = number_format($orderTotalPrice, 2, '.', '');
		}
		/* --------------------------------------------------------------
		- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
		try{
			$verified = $this->isIPNOK();
		}catch(Exception $e){
			if(!empty($mail_errors_to)){
				mail($mail_errors_to, 'error', $e->getMessage());
			}
			exit(0);
		}
		
		if($verified){
			$_RESP = array();
			$_RESPONSE['payment_status']	 = rex_post('payment_status', 'string', '');
			$_RESPONSE['receiver_email']	 = rex_post('receiver_email', 'string', '');
			$_RESPONSE['mc_gross']		   = rex_post('mc_gross', 'string', '');
			$_RESPONSE['mc_currency']		= rex_post('mc_currency', 'string', '');
			$_RESPONSE['txn_id']			 = rex_post('txn_id', 'string', '');
			$_RESPONSE['payer_email']		= rex_post('payer_email', 'string', '');
			
			$_RESP = SSHelper::cleanInput($_POST);
		
			$error = 0;   // Error Counter
			
			// 1. Überprüfen ob Zahlungsstatus "Completed" ist
			if($_RESPONSE['payment_status'] != 'Completed'){ 
				// Falls nicht, dann IPN ignorieren
				exit(0); 
			}
		
			// 2. Verkäufer E-Mail überprüfen
			if($_RESPONSE['receiver_email'] != $receiver_email){
				if(DEBUG == true){
					$this->errorLog("Verkäufer 'receiver_email' stimmt nicht überein: ".$_RESPONSE['mc_currency'] ."(Shop: Verkäufer: ".$receiver_email.")");
				}
				$error++;
			}
			
			// 3. Bestellungstotal überprüfen
			if(intval($_RESPONSE['mc_gross']) <= (intval($orderTotalPrice) - 2) and intval($_RESPONSE['mc_gross']) >= (intval($orderTotalPrice) + 2)){
				if(DEBUG == true){
					$this->errorLog("Total: 'mc_gross' stimmt nicht überein: ".$_RESPONSE['mc_gross'] ."(Shop: Total: ".$orderTotalPrice.")");
				}
				$error++;
			}
			
			// 4. Währung überprüfen
			if($_RESPONSE['mc_currency'] != $mc_currency){
				if(DEBUG == true){
					$this->errorLog("Curreny: 'mc_currency' stimmt nicht überein: ".$_RESPONSE['mc_currency'] ."(Shop: Curreny: ".$mc_currency.")");
				}
				$error++;
			}
			
			// 5. Überprüfen ob die Transaktions ID bereits verwendet wurde
			$txn_id = mysql_real_escape_string($_RESPONSE['txn_id']);
			$orderDbData = $order->_getWhere('paypal_txn_id = "'.$txn_id.'"', SSDBSchema::SHOW_IN_PAYMENT);
			if(count($orderDbData) > 0){
				if(DEBUG == true){
					$this->errorLog("'txn_id' has already been processed: ".$_RESPONSE['txn_id']);
				}
				$error++;
			}
			
			if($error > 0){
				/* --------------------------------------------------------------
				// Bezahlung fehlgeschlagen
				// Zahlungsstatus auf -1 setzen
				- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
				if($order->loadById($orderId)){
					$order->set('payment_status', "-1");
					$order->save();
				}
				/* --------------------------------------------------------------
				- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
			}else{
				/* --------------------------------------------------------------
				// Bezahlung erfoglreich
				- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
				$payer_email = mysql_real_escape_string($_RESPONSE['payer_email']);
				$mc_gross = mysql_real_escape_string($_RESPONSE['mc_gross']);
				
				// Bezahlungs-Info zur Bestellung hinzufügen
				if($order->loadById($orderId)){
					$order->set('paypal_txn_id', $txn_id);
					$order->set('payer_email', $payer_email);
					$order->set('payment_status', 1);
					$order->save();
				}
				/* --------------------------------------------------------------
				- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
			}
		}else{
		}
		die();
	}
	
	/** @brief Instant Payment Notification
	 *
	 *  Diese Funktion wurde auf dem GitHub-Portal
	 *  zur Verwendung angeboten.
	 *
	 *  PayPal Daten werden überprüft, ob sie Original
	 *  von PayPal erzeugt wurden. Damit wird verhindert,
	 *  dass die Daten nicht manipuliert wurden.
	 *
	 *  @author PayPal
	 *  @author http://developer.paypal.com/
	 *  @author https://github.com/paypal/ipn-code-samples/blob/master/paypal_ipn.php
	 */
	public function isIPNOK(){
		// CONFIG: Enable debug mode. This means we'll log requests into 'ipn.log' in the same directory.
		// Especially useful if you encounter network errors or other intermittent problems with IPN (validation).
		// Set this to 0 once you go live or don't require logging.
		define("DEBUG", $this->debug);
		
		// Set to 0 once you're ready to go live
		define("USE_SANDBOX", $this->useSandbox);
		
		
		//define("LOG_FILE", "./ipn.log");
		define("LOG_FILE", $this->logfile);
		
		
		// Read POST data
		// reading posted data directly from $_POST causes serialization
		// issues with array data in POST. Reading raw POST data from input stream instead.
		$raw_post_data = file_get_contents('php://input');
		$raw_post_array = explode('&', $raw_post_data);
		$myPost = array();
		foreach ($raw_post_array as $keyval) {
			$keyval = explode ('=', $keyval);
			if (count($keyval) == 2)
				$myPost[$keyval[0]] = urldecode($keyval[1]);
		}
		// read the post from PayPal system and add 'cmd'
		$req = 'cmd=_notify-validate';
		if(function_exists('get_magic_quotes_gpc')) {
			$get_magic_quotes_exists = true;
		}
		foreach ($myPost as $key => $value) {
			if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
				$value = urlencode(stripslashes($value));
			} else {
				$value = urlencode($value);
			}
			$req .= "&$key=$value";
		}
		
		// Post IPN data back to PayPal to validate the IPN data is genuine
		// Without this step anyone can fake IPN data
		
		if(USE_SANDBOX == true) {
			$paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
		} else {
			$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
		}
		
		$ch = curl_init($paypal_url);
		if ($ch == FALSE) {
			return FALSE;
		}
		
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
		
		if(DEBUG == true) {
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
		}
		
		// CONFIG: Optional proxy configuration
		//curl_setopt($ch, CURLOPT_PROXY, $proxy);
		//curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
		
		// Set TCP timeout to 30 seconds
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
		
		// CONFIG: Please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set the directory path
		// of the certificate as shown below. Ensure the file is readable by the webserver.
		// This is mandatory for some environments.
		
		//$cert = __DIR__ . "./cacert.pem";
		//curl_setopt($ch, CURLOPT_CAINFO, $cert);
		
		$res = curl_exec($ch);
		if (curl_errno($ch) != 0) // cURL error
			{
			if(DEBUG == true) {	
				error_log(date('[Y-m-d H:i e] '). "Can't connect to PayPal to validate IPN message: " . curl_error($ch) . PHP_EOL, 3, LOG_FILE);
			}
			curl_close($ch);
			exit;
		
		} else {
				// Log the entire HTTP response if debug is switched on.
				if(DEBUG == true) {
					error_log(date('[Y-m-d H:i e] '). "HTTP request of validation request:". curl_getinfo($ch, CURLINFO_HEADER_OUT) ." for IPN payload: $req" . PHP_EOL, 3, LOG_FILE);
					error_log(date('[Y-m-d H:i e] '). "HTTP response of validation request: $res" . PHP_EOL, 3, LOG_FILE);
				}
				curl_close($ch);
		}
		
		// Inspect IPN validation result and act accordingly
		
		// Split response headers and payload, a better way for strcmp
		$tokens = explode("\r\n\r\n", trim($res));
		$res = trim(end($tokens));
		
		if (strcmp ($res, "VERIFIED") == 0) {
			// check whether the payment_status is Completed
			// check that txn_id has not been previously processed
			// check that receiver_email is your PayPal email
			// check that payment_amount/payment_currency are correct
			// process payment and mark item as paid.
		
			// assign posted variables to local variables
			//$item_name = $_POST['item_name'];
			//$item_number = $_POST['item_number'];
			//$payment_status = $_POST['payment_status'];
			//$payment_amount = $_POST['mc_gross'];
			//$payment_currency = $_POST['mc_currency'];
			//$txn_id = $_POST['txn_id'];
			//$receiver_email = $_POST['receiver_email'];
			//$payer_email = $_POST['payer_email'];
			
			if(DEBUG == true) {
				error_log(date('[Y-m-d H:i e] '). "Verified IPN: $req ". PHP_EOL, 3, LOG_FILE);
			}
			return true;
		} else if (strcmp ($res, "INVALID") == 0) {
			// log for manual investigation
			// Add business logic here which deals with invalid IPN messages
			if(DEBUG == true) {
				error_log(date('[Y-m-d H:i e] '). "Invalid IPN: $req" . PHP_EOL, 3, LOG_FILE);
			}
			return false;
		}	
	}
	
	
	function errorLog($str){
		error_log(date('[Y-m-d H:i e] '). $str . PHP_EOL, 3, $this->logfile);
	}
}