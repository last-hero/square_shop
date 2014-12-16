<?
	$payPalCtrl = new SSPayPalController();
	$conf = $payPalCtrl->getConf();
	
	$formUrl = $payPalCtrl->paypalUrl;
	
	if(isset($_GET['handlePayPalPayment'])):
		$payPalCtrl->handlePayment();
		die();
	endif;
?>
<?
		$cartOverview = $payPalCtrl->getOrderInfoAndItems();
		$items = $cartOverview['items'];
?>
		<table class="ss-cart" width="100%">
        	<tr>
            	<th class="ss-title"><?=$label_bezeichnung?></th>
            	<th class="ss-price" align="right"><?=$label_price?></th>
            	<th class="ss-qty" align="right"><?=$label_qty?></th>
            	<th class="ss-subtotal" align="right"><?=$label_subtotal?></th>
            </tr>
        	<tr>
<?
			for($x=0; $x<count($items); $x++):
?>
            	<td class="ss-title"><?=$items[$x]['title']?></td>
            	<td class="ss-price" align="right"><?=$currency?> <?=$items[$x]['price']?></td>
            	<td class="ss-qty" align="right"><?=$currency?> <?=$items[$x]['qty']?></td>
            	<td class="ss-subtotal" align="right"><?=$cartOverview['currency']?> <?=$items[$x]['subtotal']?></td>
            </tr>
<?
			endfor;
?>
        	<tr>
            	<th colspan="4" class="ss-total" align="right"><?=$label_total?> <?=$cartOverview['currency']?> <?=$cartOverview['total']?></th>
            </tr>
        </table>
        <br /><br />
        <!--<form class="paypalpaymentform" action="https://www.paypal.com/cgi-bin/webscr" method="post">-->
        <form action="<?=$formUrl?>" 
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
                <input id="ss-<?=$fname?>-next" name="SSForm[<?=$FORM_ID?>][submit]" type="submit" class="ss-<?=$fname?> ss-next" value="<?=$label_submit?>" />
            </p>
        </form>