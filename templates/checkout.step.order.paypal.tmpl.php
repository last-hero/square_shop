<?
	$payPalCtrl = new SSPayPalController();
	$conf = $payPalCtrl->getConf();
	
	if(isset($_GET['handlePayPalPayment'])):
		$payPalCtrl->handlePayment();
		die();
	endif;
?>
        <!--<form class="paypalpaymentform" action="https://www.paypal.com/cgi-bin/webscr" method="post">-->
        <form action="https://www.sandbox.paypal.com/cgi-bin/webscr" 
        method="post" name="ss-form-<?=$FORM_ID?>" class="ss-form ss-form-<?=$FORM_ID?> paypalpaymentform" method="post">
            <input type="hidden" name="cmd" value="_cart" />
            <input type="hidden" name="upload" value="1" />
            <input type="hidden" name="business" value="<?=$conf['business']?>">
            <input type="hidden" name="currency_code" value="<?=$conf['currency']?>">
            <input type="hidden" name="return" value="<?=$conf['return_url']?>">
            <input type="hidden" name="notify_url" value="<?=$conf['notify_url']?>">
            <input type="hidden" name="invoice" value="<?=$conf['invoice']?>">
            <input type="hidden" name="rm" value="2" />
            <div class="paypalpaymentform_items">
<?
			$items = $payPalCtrl->getCartItems();
			for($x=0; $x<count($items); $x++):
?>
                <input type="hidden" name="item_name_<?=$x+1?>" value="<?=$items[$x]['title']?>" />
                <input type="hidden" name="amount_<?=$x+1?>" value="<?=$items[$x]['price']?>" />
                <input type="hidden" name="quantity_<?=$x+1?>" value="<?=$items[$x]['qty']?>" />
                <input type="hidden" name="shipping_<?=$x+1?>" value="0" />
                <input type="hidden" name="shipping2_<?=$x+1?>" value="0" />
<?				
			endfor;
?>
            </div>
            <? $fname = 'submit'; ?>
            <p class="ss-<?=$fname?>">
                <input id="ss-<?=$fname?>" name="SSForm[<?=$FORM_ID?>][submit]" type="submit" class="ss-<?=$fname?>" value="<?=$label_submit?>" />
            </p>
        </form>