        <form action="" method="post" name="ss-form-<?=$FORM_ID?>" class="ss-form ss-form-<?=$FORM_ID?>">
            <input type="hidden" name="SSForm[<?=$FORM_ID?>][action]" value="<?=$action?>" />
            <input type="hidden" name="SSForm[<?=$FORM_ID?>][uniqueId]" value="<?=hash('md5', microtime(true))?>" />
 
 			<?
			
				$ppTotal = sprintf("%01.2f",$total);
				$ppTotal = str_replace(",", ".", $ppTotal);
				
				
				
				$mail_errors_to 	= 'gobi.selva@square.ch';
				$receiver_email 	= 'gobiS_1349359007_biz@square.ch';
				$mail_errors_to 	= 'gobi.selva@square.ch';
				$receiver_email 	= 'nati@natalia-gianinazzi.ch';
				$mc_currency 		= 'CHF';
	
			?>
            
            <? $fname = 'submit'; ?>
            <p class="ss-<?=$fname?>">
                <input id="ss-<?=$fname?>" name="SSForm[<?=$FORM_ID?>][submit]" type="submit" class="ss-<?=$fname?>" value="<?=$label_submit?>" />
            </p>
        </form>
        
        <form action="https://www.paypal.com/de/cgi-bin/webscr" target="_blank" method="post">
        <input type="hidden" name="cmd" value="_xclick">
        <input type="hidden" name="business" value="<?=$paypal_address?>">
        <input type="hidden" name="item_name" value="Warenkorb">
        <input type="hidden" name="currency_code" value="CHF">
        <input type="hidden" name="amount" value="<?=$pppayment?>">
        <input type="submit" class="weiter2" value="Jetzt mit Paypal bezahlen" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
        </form>
        
      <?
	  	$paypal_address = 'gobiswiss@outlook.com';
		
	  	$paypal_return_url = 'http://gruesli.com/shop/checkout/data/payment/paypal/sid/d5c75e67698033215d37690281e3a4f8a40f496f/';
	  	$paypal_notify_url = 'http://gruesli.com/shop/checkout/data/ajax/2/handlePayPalPayment/1/sid/d5c75e67698033215d37690281e3a4f8a40f496f/';
		
	  	$paypal_invoice = '1418396666_d5c75e67698033215d37690281e3a4f8a40f496f';
	  ?>  
<form class="paypalpaymentform" action="https://www.paypal.com/cgi-bin/webscr" method="post">
<!--<form class="paypalpaymentform" action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">-->
    <input type="hidden" name="cmd" value="_cart">
    <input type="hidden" name="upload" value="1">
    <input type="hidden" name="business" value="<?=$paypal_address?>">
    <input type="hidden" name="currency_code" value="CHF">
    <input type="hidden" name="return" value="<?=$paypal_return_url?>">
    <input type="hidden" name="notify_url" value="<?=$paypal_notify_url?>">
    <input type="hidden" name="rm" value="2">
    <input type="hidden" name="invoice" value="<?=$paypal_invoice?>">
        <input type="hidden" name="item_name_1" value="1031  -  Reality and fiction">
        <input type="hidden" name="amount_1" value="149.00">
        <input type="hidden" name="quantity_1" value="1">
        <input type="hidden" name="shipping_1" value="11">
        <input type="hidden" name="shipping2_1" value="0">
        <input type="hidden" name="item_name_2" value="1029  -  Nr. 1029 - Pharrell Gruesli">
        <input type="hidden" name="amount_2" value="149.00">
        <input type="hidden" name="quantity_2" value="1">
        <input type="hidden" name="shipping_2" value="0">
        <input type="hidden" name="shipping2_2" value="0">
        <input type="hidden" name="item_name_3" value="1028  -  Nr. 1028 - Black and white Gruesli need some colors">
        <input type="hidden" name="amount_3" value="122.00">
        <input type="hidden" name="quantity_3" value="1">
        <input type="hidden" name="shipping_3" value="0">
        <input type="hidden" name="shipping2_3" value="0">
        <input type="hidden" name="item_name_4" value="981  -  Nr. 981 - Violetta">
        <input type="hidden" name="amount_4" value="95.00">
        <input type="hidden" name="quantity_4" value="1">
        <input type="hidden" name="shipping_4" value="0">
        <input type="hidden" name="shipping2_4" value="0">
    </div>
</form>
























<?
// Get Data from DB ---------------------------------*/
			if($_GET['ajax'] == 1 or $_GET['ajax'] == '1' or $_GET['ajax'] == true or $_POST['ajax'] == 1 or $_POST['ajax'] == '1' or $_POST['ajax'] == true){
				if($tvsshop_func == 'getdata'){
					if($_SESSION[$thispage]['cart']['art_id'] != ''):
						$myIniFile	= $REX['INCLUDE_PATH'] . "/addons/" . $thispage . "/" . $thispage . ".ini";
						$settings	= parse_ini_file($myIniFile);
						$shopname	= $settings['Shopname'];
						$imagesize	= $settings['ImageSize'];
						$usefacebook= $settings['UseFacebook'];
						$currency	= $settings['Currency'];
						$shopmail	= $settings['Shopmail'];
						$shipping	= str_replace(",",".",$settings['Shipping']);
			
						$table_pre = $REX['TABLE_PREFIX'] .$REX['ADDON']['rxid']['tvsshop'];
						$art_table = $table_pre . "_articles";
						$cat_table = $table_pre . "_categories";
						$shipping_prices    = array('ch' => array('priority' => 11, 'economy' => 9),
													'eu' => array('priority' => 20, 'economy' => 15.50),
													'oc' => array('priority' => 26, 'economy' => 18)										
												);
						$shipping_to		= $_SESSION[$thispage]['cart']['shipping_to'];
						$shipping_method	= $_SESSION[$thispage]['cart']['shipping_method'];
						$shipping_price     = doubleval($shipping_prices[$shipping_to][$shipping_method]);
						
						$articles			= explode(",", $_SESSION[$thispage]['cart']['art_id']);
						$articles_count		= explode(",", $_SESSION[$thispage]['cart']['art_count']);
						$articles_sum_count	= 0;
						$articles_sum		= 0;
						$subtotal_this		= 0;
						$cart_total			= 0;
						$articles_ordertext	= "";
						$articles_mailordertext	= "";
						$sql = new rex_sql();
						foreach($articles as $i => $value):
							$sql->setQuery("SELECT * FROM " . $art_table . " WHERE id = " . $value . " AND status = 1");
							if($sql->getRows() > 0 ):
								if($tvsshop_art_id == $value){
									$subtotal_this = $sql->getValue('price') * doubleval($articles_count[$i]);
								}
								$articles_sum = $articles_sum + $sql->getValue('price') * doubleval($articles_count[$i]);
								$articles_sum_count = $articles_sum_count + doubleval($articles_count[$i]);
								
					$articles_mailordertext	.= '_-tr-_';
					$articles_mailordertext	.= 		'_-td-_'.$sql->getValue('title').'_--td-_';
					$articles_mailordertext	.= 		'_-td-_'.$sql->getValue('id').'_--td-_';
					$articles_mailordertext	.= 		'_-td-_'.$sql->getValue('artnr').'_--td-_';
					$articles_mailordertext	.= 		'_-td-_'.$articles_count[$i].'_--td-_';
					$articles_mailordertext	.= 		'_-td-_'.(sprintf("%01.2f",$sql->getValue('price') * doubleval($articles_count[$i]))).' '.$currency.'_--td-_';
					$articles_mailordertext	.= '_--tr-_';
					
								$arrayItems[] = array('item_name' => $sql->getValue('artnr').'  -  '.$sql->getValue('title'), 
														'amount' => $sql->getValue('price'), 
														'quantity' => doubleval($articles_count[$i])
													);
								$sql->next();
							endif;//if($sql->getRows() > 0 )
						endforeach;
			$articles_mailordertext	.= '_-tr-_';
			$articles_mailordertext	.= 		'_-th-__--th-_';
			$articles_mailordertext	.= 		'_-th-__--th-_';
			$articles_mailordertext	.= 		'_-th-__--th-_';
			$articles_mailordertext	.= 		'_-th-__--th-_';
			$articles_mailordertext	.= 		'_-th-__--th-_';
			$articles_mailordertext	.= '_--tr-_';
			$articles_mailordertext	.= '_-tr-_';
			$articles_mailordertext	.= 		'_-td-_Shipping Price:_--td-_';
			$articles_mailordertext	.= 		'_-td-__--td-_';
			$articles_mailordertext	.= 		'_-td-__--td-_';
			$articles_mailordertext	.= 		'_-td-__--td-_';
			$articles_mailordertext	.= 		'_-td-_'.sprintf("%01.2f",$shipping_price) . " " . $currency.'_--td-_';
			$articles_mailordertext	.= '_--tr-_';
			$articles_mailordertext	.= '_-tr-_';
			$articles_mailordertext	.= 		'_-td-_Total:_--td-_';
			$articles_mailordertext	.= 		'_-td-__--td-_';
			$articles_mailordertext	.= 		'_-td-__--td-_';
			$articles_mailordertext	.= 		'_-td-_'.$articles_sum_count.'_--td-_';
			$articles_mailordertext	.= 		'_-td-_'.sprintf("%01.2f",$articles_sum + $shipping_price).'_--td-_';
			$articles_mailordertext	.= '_--tr-_';
						$_SESSION[$thispage]['cart']['articles_mailordertext'] = $articles_mailordertext;
					endif; //if($_SESSION[$thispage]['cart']['art_id'] != ''):
					$_SESSION[$thispage]['cart']['cart_total'] = number_format(doubleval($articles_sum) + doubleval($shipping_price), 2, '.', ' ');
					$cart_total = sprintf("%01.2f",doubleval($articles_sum) + doubleval($shipping_price)) . " " . $currency;
					$subtotal_this = sprintf("%01.2f",doubleval($subtotal_this)) . " " . $currency;
					$articles_sum = sprintf("%01.2f",doubleval($articles_sum)) . " " . $currency;
					$shipping_price = sprintf("%01.2f",doubleval($shipping_price)) . " " . $currency;
				}
				
				$paypal_items = '';
				for($h=0; $h<sizeof($arrayItems); $h++):
					$paypal_items .= '<input type="hidden" name="item_name_'.($h+1).'" value="'.$arrayItems[$h]['item_name'].'" />
						<input type="hidden" name="amount_'.($h+1).'" value="'.$arrayItems[$h]['amount'].'" />
						<input type="hidden" name="quantity_'.($h+1).'" value="'.$arrayItems[$h]['quantity'].'" />';
					if($h == 0):
					   $paypal_items .= '<input type="hidden" name="shipping_'.($h+1).'" value="'.$shipping_price.'" />';
					else:
					   $paypal_items .= '<input type="hidden" name="shipping_'.($h+1).'" value="0" />';
					endif;
					   $paypal_items .= '<input type="hidden" name="shipping2_'.($h+1).'" value="0" />';
                endfor;
				echo json_encode(
					array(
						'subtotal_this'=>$subtotal_this,
						'art_count_this'=>$articles_count,
						'art_count_total'=>$_SESSION[$thispage]['cart']['art_count_total'],
						'cart_subtotal'=>$articles_sum,
						'shipping_price'=>$shipping_price,
						'shipping_subtotal'=>$shipping_price,
						'cart_total'=>$cart_total,
						'paypal_items'=>($arrayItems),
						'success'=>1
					)
				);
				die();
			}
?>