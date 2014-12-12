        <form action="" method="post" name="ss-form-<?=$FORM_ID?>" class="ss-form ss-form-<?=$FORM_ID?>">
            <input type="hidden" name="SSForm[<?=$FORM_ID?>][action]" value="<?=$action?>" />
            <input type="hidden" name="SSForm[<?=$FORM_ID?>][uniqueId]" value="<?=hash('md5', microtime(true))?>" />
            
            
            <?
			/*
                // Mit paypal bezahlen ?
        $pppayment = sprintf("%01.2f",$_SESSION[$thispage]['cart']['art_sumtotal']);
        echo "<div><h2>Vorkasse</h2><p>Bezahlen Sie den Rechnungsbetrag im Voraus. Die Bankverbindung wird Ihnen per E-Mail mitgeteilt. <br/><br/><h2><span>PayPal</span> Kaufabwicklung</h2><p>Oder möchten Sie mit Paypal sofort bezahlen.</p> ";
        $pppayment = str_replace(",", ".", $pppayment);
        echo"
        <form action=\"https://www.paypal.com/de/cgi-bin/webscr\" target=\"_blank\" method=\"post\">
        <input type=\"hidden\" name=\"cmd\" value=\"_xclick\">
        <input type=\"hidden\" name=\"business\" value=\"" . $paypal_address . "\">
        <input type=\"hidden\" name=\"item_name\" value=\"Warenkorb\">
        <input type=\"hidden\" name=\"currency_code\" value=\"EUR\">
        <input type=\"hidden\" name=\"amount\" value=\"$pppayment\">
        <input type=\"submit\" class=\"weiter2\" value=\"Jetzt mit Paypal bezahlen\" name=\"submit\" alt=\"Make payments with PayPal - it's fast, free and secure!\">
        </form><br /></div><br /><br />
        ";
		*/
		
		
        $pppayment = sprintf("%01.2f",$total);
        $pppayment = str_replace(",", ".", $pppayment);
		
		
d($pppayment);

			?>
      <!-- <form action="https://www.paypal.com/de/cgi-bin/webscr" target="_blank" method="post">
        <input type="hidden" name="cmd" value="_xclick">
        <input type="hidden" name="business" value="" . $paypal_address . "">
        <input type="hidden" name="item_name" value="Warenkorb">
        <input type="hidden" name="currency_code" value="EUR">
        <input type="hidden" name="amount" value="$pppayment">
        <input type="submit" class="weiter2" value="Jetzt mit Paypal bezahlen" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
        </form><br /></div><br /><br />-->



<?
$PayPalMode         = 'sandbox'; // sandbox or live
$PayPalApiUsername  = 'selleremail@somesite.com'; //PayPal API Username
$PayPalApiPassword  = '123456'; //Paypal API password
$PayPalApiSignature     = 'ZWxwchnCsdQg5PxAUjcH6OPuZK3sPcPH'; //Paypal API Signature
$PayPalCurrencyCode     = 'USD'; //Paypal Currency Code
$PayPalReturnURL    = 'http://yoursite.com/paypal/process.php'; //Point to process.php page
$PayPalCancelURL    = 'http://yoursite.com/paypal/cancel_url.php'; //Cancel URL if user clicks cancel
?>

            
            
<!--<h2 align="center">Test Products</h2>
<table class="procut_item" border="0" cellpadding="4">
  <tr>
    <td width="70%"><h4>Product 1</h4>(product description)</td>
    <td width="30%">
    <form method="post" action="process.php">
    <input type="hidden" name="itemname" value="Product 1" /> 
    <input type="hidden" name="itemnumber" value="10000" /> 
    <input type="hidden" name="itemdesc" value="product description." /> 
    <input type="hidden" name="itemprice" value="225.00" />
    Quantity : <select name="itemQty"><option value="1">1</option><option value="2">2</option><option value="3">3</option></select> 
    <input class="dw_button" type="submit" name="submitbutt" value="Buy (225.00 <?php echo $PayPalCurrencyCode; ?>)" />
    </form>
    </td>
  </tr>
</table> -->       
            <? $fname = 'submit'; ?>
            <p class="ss-<?=$fname?>">
                <input id="ss-<?=$fname?>" name="SSForm[<?=$FORM_ID?>][submit]" type="submit" class="ss-<?=$fname?>" value="<?=$label_submit?>" />
            </p>
        </form>