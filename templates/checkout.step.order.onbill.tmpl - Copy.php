        <form action="" method="post" name="ss-form-<?=$FORM_ID?>" class="ss-form ss-form-<?=$FORM_ID?>">
            <input type="hidden" name="SSForm[<?=$FORM_ID?>][action]" value="<?=$action?>" />
            <input type="hidden" name="SSForm[<?=$FORM_ID?>][uniqueId]" value="<?=hash('md5', microtime(true))?>" />
            
            <? $fname = 'submit'; ?>
            <p class="ss-<?=$fname?>">
                <input id="ss-<?=$fname?>" name="SSForm[<?=$FORM_ID?>][submit]" type="submit" class="ss-<?=$fname?>" value="<?=$label_submit?>" />
            </p>
        </form>
		<?
            
            $mail_errors_to 		  = 'gobi.selva@square.ch';
			$paypal_business 		 = 'gobiswiss@outlook.com';
			$currency 				= 'CHF';
			$paypal_return_url 	   = $_SERVER['HTTP_HOST'].rex_getUrl(
											REX_ARTICLE_ID, REX_CLANG_ID, 
                                            array(
												'payment' => 'paypal'
                                                , 'sid' => $_SESSION['tvsshop']['cart']['sid']
											)
										);
			$paypal_notify_url 	   = $_SERVER['HTTP_HOST'].rex_getUrl(
											REX_ARTICLE_ID, REX_CLANG_ID, 
                                            array(
												'ajax' => 2
												, 'handlePayPalPayment' => 1
                                                , 'sid' => $_SESSION['tvsshop']['cart']['sid']
											)
										);
			$paypal_invoice = time().'_'.$_SESSION['tvsshop']['cart']['sid'];
		
			
			
function generateHash(){
	$result = "";
	$charPool = '0123456789abcdefghijklmnopqrstuvwxyz';
	for($p = 0; $p<15; $p++)
		$result .= $charPool[mt_rand(0,strlen($charPool)-1)];
	return sha1(md5(sha1($result)));
}

	  	$paypal_address = 'gobiswiss@outlook.com';
		
	  	$paypal_return_url = 'http://gruesli.com/shop/checkout/data/payment/paypal/sid/d5c75e67698033215d37690281e3a4f8a40f496f/';
	  	$paypal_notify_url = 'http://gruesli.com/shop/checkout/data/ajax/2/handlePayPalPayment/1/sid/d5c75e67698033215d37690281e3a4f8a40f496f/';
		
	  	$paypal_invoice = '1418396666_d5c75e67698033215d37690281e3a4f8a40f496f';

        ?>
        <!--<form class="paypalpaymentform" action="https://www.paypal.com/cgi-bin/webscr" method="post">-->
        <form class="paypalpaymentform" action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">
            <input type="hidden" name="cmd" value="_cart" />
            <input type="hidden" name="upload" value="1" />
            <input type="hidden" name="business" value="<?=$paypal_business?>">
            <input type="hidden" name="currency_code" value="<?=$currency?>" />
            <input type="hidden" name="return" value="<?=$paypal_return_url?>">
            <input type="hidden" name="notify_url" value="<?=$paypal_notify_url?>">
            <input type="hidden" name="rm" value="2" />
            <input type="hidden" name="invoice" value="<?=$paypal_invoice?>" />
            <div class="paypalpaymentform_items">
                <? for($h=0; $h<sizeof($arrayItems); $h++): ?>
                <input type="hidden" name="item_name_<?=$h+1?>" value="<?=$arrayItems[$h]['item_name']?>" />
                <input type="hidden" name="amount_<?=$h+1?>" value="<?=$arrayItems[$h]['amount']?>" />
                <input type="hidden" name="quantity_<?=$h+1?>" value="<?=$arrayItems[$h]['quantity']?>" />
                <input type="hidden" name="shipping_<?=$h+1?>" value="0" />
                <input type="hidden" name="shipping2_<?=$h+1?>" value="0" />
                <? endfor; ?>
            </div>
            <? $fname = 'submit'; ?>
            <p class="ss-<?=$fname?>">
                <input id="ss-<?=$fname?>" name="SSForm[<?=$FORM_ID?>][submit]" type="submit" class="ss-<?=$fname?>" value="<?=$label_submit?>" />
            </p>
        </form>