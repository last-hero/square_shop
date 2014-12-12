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

class SSPayPalController extends SSController{
	public function init(){
		if(strlen($this->session->get('paypal_sid')) < 5){
			$this->session->set('paypal_sid', SSHelper::generateHash());
		}
	}
	
	public function invoke(){
		
	}
	public function getConf(){
		return array(
			'business' 		=> 'gobiswiss@outlook.com'
			, 'currency' 	  => SSHelper::getSetting('currency')
			, 'sid' 		   => $this->session->get('paypal_sid')
			, 'return_url'    => $_SERVER['HTTP_HOST'].rex_getUrl(
										REX_ARTICLE_ID, REX_CLANG_ID, 
										array(
											'ss-cart' => 'checkout'
											, 'payment' => 'paypal'
											, 'sid' => $sid
										)
									)
			, 'notify_url'    => $_SERVER['HTTP_HOST'].rex_getUrl(
										REX_ARTICLE_ID, REX_CLANG_ID, 
										array(
											'ss-cart' => 'checkout'
											, 'ajax' => 2
											, 'handlePayPalPayment' => 1
											, 'sid' => $sid
										)
									)
			, 'invoice' 	   => time().'_'.$sid
		);
	}
	public function getCartItems(){
		$cartCtrl = new SSCartController();
		$ids = $cartCtrl->getCartItemIds();
		$article = new SSArticle();
		$articles = $article->getByIds($ids);
		
		$items = array();
		foreach($articles as $art){
			$items[] = array(
				'title' => $art['title']
				, 'price' => $art['price']
				, 'qty' => $cartCtrl->getItemQtyById($art['id'])
			);	
		}
		return $items;
	}
	public function handlePayment(){
		global $REX;
		$mail_errors_to 	= 'gobi.selva@square.ch';
		$receiver_email 	= 'gobiS_1349359007_biz@square.ch';
		$mail_errors_to 	= 'gobi.selva@square.ch';
		$receiver_email 	= 'nati@natalia-gianinazzi.ch';
		$mc_currency 		= 'CHF';
		
		// Shop ---------------------------------------------------------------------------------------------------------------
		// Shop ---------------------------------------------------------------------------------------------------------------
		$thispage			= "tvsshop";
		$sid				= rex_get('sid', 'string', '');
		$sql 				= new rex_sql();
		
		$sql->setQuery("SELECT * FROM rex_927_orders
								WHERE sid = '".$sid."'
								ORDER BY rex_927_orders.id DESC
								LIMIT 0 , 30");
		$_cart_total = 0;						
		if($sql->getRows() > 0):
		
			$_cart_total = number_format($sql->getValue('cart_total'), 2, '.', ' ');
			
			$sql->next();
		endif;//if($sql->getRows() > 0 )
		// Shop ---------------------------------------------------------------------------------------------------------------
		// Shop ---------------------------------------------------------------------------------------------------------------
		$listener = new IpnListener();
		$listener->use_sandbox = true;
		$listener->use_ssl = false;
		
		try{
			$listener->requirePostMethod();
			$verified = $listener->processIpn();
		}catch(Exception $e){
			if(!empty($mail_errors_to)){
				mail($mail_errors_to, 'error', $e->getMessage());
			}
			exit(0);
		}
		
		if($verified){
			$errmsg = '';   // stores errors from fraud checks
			
			// 1. Make sure the payment status is "Completed" 
			if($_POST['payment_status'] != 'Completed'){ 
				// simply ignore any IPN that is not completed
				exit(0); 
			}
		
			// 2. Make sure seller email matches your primary account email.
			if($_POST['receiver_email'] != $receiver_email){
				$errmsg .= "'receiver_email' does not match: ";
				$errmsg .= $_POST['receiver_email']."\n";
			}
			
			// 3. Make sure the amount(s) paid match
			if(intval($_POST['mc_gross']) <= (intval($_cart_total) - 2) and intval($_POST['mc_gross']) >= (intval($_cart_total) + 2)){
				$errmsg .= "'mc_gross' does not match: ";
				$errmsg .= $_POST['mc_gross']."\n";
				$errmsg .= " cart_total: "."\n";
				$errmsg .= $_cart_total;
			}
			
			// 4. Make sure the currency code matches
			if($_POST['mc_currency'] != $mc_currency){
				$errmsg .= "'mc_currency' does not match: ";
				$errmsg .= $_POST['mc_currency']."\n";
			}
			
			// TODO: Check for duplicate txn_id ------------------------------
			// 5. Ensure the transaction is not a duplicate.
			
		
			$txn_id = mysql_real_escape_string($_POST['txn_id']);
			$q = "SELECT COUNT(*) as anzahl FROM paypal_orders WHERE txn_id = '$txn_id'";
			$sql->setQuery($q);
			if($sql->getValue('anzahl') > 0){
				$errmsg .= "'txn_id' has already been processed: ".$_POST['txn_id']."\n";
			}
			// TODO: Check for duplicate txn_id ------------------------------
			
			if(!empty($errmsg)){
				// manually investigate errors from the fraud checking
				$body = "IPN failed fraud checks: \n$errmsg\n\n";
				$body .= $listener->getTextReport();
				if(!empty($mail_errors_to)){
					mail($mail_errors_to, 'IPN Fraud Warning', $body);
				}
			}else{
				// TODO: process order here -------------------------------
				// add this order to a table of completed orders
				$payer_email = mysql_real_escape_string($_POST['payer_email']);
				$mc_gross = mysql_real_escape_string($_POST['mc_gross']);
				$q = "INSERT INTO paypal_orders VALUES 
						(NULL, '$txn_id', '$payer_email', $mc_gross)";
				$sql->setQuery($q);
							
				// Update Order Infos
				$q = "UPDATE rex_927_orders SET paypal_txn_id = '$txn_id',  payer_email = '$payer_email' WHERE sid = '".$sid."';";
				$sql->setQuery($q);
				
				// send user an email with a link to their digital download
				/*
				$to = filter_var($_POST['payer_email'], FILTER_SANITIZE_EMAIL);
				$subject = "Your digital download is ready";
				mail($to, "2 Thank you for your order", "Download URL: ...");
				*/
				// TODO: process order here -------------------------------
			}
			if(!empty($mail_errors_to)){
				mail($mail_errors_to, 'Verified IPN', $listener->getTextReport());
			}
		}else{
			/*
			An Invalid IPN *may* be caused by a fraudulent transaction attempt. It's
			a good idea to have a developer or sys admin manually investigate any 
			invalid IPN.
			*/
			if(!empty($mail_errors_to)){
				mail($mail_errors_to, 'Invalid IPN', $listener->getTextReport());
			}
		}
		die();
	}
}