		<table class="ss-cart" width="100%">
        	<tr>
                <? if(!isset($simpleView)): ?>
            	<th class="ss-img"></th>
                <? endif; ?>
            	<th class="ss-artno"><?=$label_artno?></th>
            	<th class="ss-title"><?=$label_bezeichnung?></th>
            	<th class="ss-price" align="right"><?=$label_price?></th>
            	<th class="ss-qty" align="right"><?=$label_qty?></th>
            	<th class="ss-subtotal" align="right"><?=$label_subtotal?></th>
                <? if(!isset($simpleView)): ?>
            	<th class="ss-delfromcart"></th>
                <? endif; ?>
            </tr>
        	<tr>
<?
		foreach($articles as $art):
?>
                <? if(!isset($simpleView)): ?>
            	<td class="ss-img">
<?
			if(count($art['imgs']) and $art['imgs'][0]):
				$img = $art['imgs'][0];
?>
				<a href="<?=$art['url']?>">
            		<img src="index.php?rex_img_type=ss-cart&rex_img_file=<?=$img?>" />
                </a>
<?
			endif;
?>
				</td>
                <? endif; ?>
            	<td class="ss-artno">
                <? if(!isset($simpleView)): ?>
                	<a href="<?=$art['url']?>"><?=$art['no']?></a>
                <? else: ?>
                	<?=$art['no']?>
                <? endif; ?>
                </td>
            	<td class="ss-title">
				    <? if(!isset($simpleView)): ?>
                        <a href="<?=$art['url']?>"><?=$art['title']?></a>
                    <? else: ?>
                        <?=$art['title']?>
                    <? endif; ?>
                </td>
            	<td class="ss-price" align="right"><?=$currency?> <?=$art['price']?></td>
            	<td class="ss-qty" align="right">
               <? if(!isset($simpleView)): ?>
                    <form action="" method="post" name="ss-form-update_art" class="ss-form ss-form-update_art">
                        <input type="hidden" name="SSForm[<?=$FORM_ID?>][action]" value="<?=$action_update_art?>" />
                        <input type="hidden" name="SSForm[<?=$FORM_ID?>][id]" value="<?=$art['id']?>" />
                        <label for="ss-qty"><?=$label?></label>
                        <input id="ss-qty" name="SSForm[<?=$FORM_ID?>][qty]" maxlength="2" type="text" class="<?=trim($css_class)?>" value="<?=$art['qty']?>" />
                        <input id="ss-submit" name="SSForm[<?=$FORM_ID?>][submit]" type="submit" class="ss-submit" value="<?=$label_update_art?>" />
                    </form>
                <? else: ?>
                	<?=$art['qty']?>
                <? endif; ?>
                </td>
            	<td class="ss-subtotal" align="right"><?=$currency?> <?=$art['subtotal']?></td>
            <? if(!isset($simpleView)): ?>
            	<td class="ss-delfromcart" align="right">
                    <form action="" method="post" name="ss-form-delfromcart" class="ss-form ss-form-delfromcart">
                        <input type="hidden" name="SSForm[<?=$FORM_ID?>][action]" value="<?=$action_del_from_cart?>" />
                        <input type="hidden" name="SSForm[<?=$FORM_ID?>][id]" value="<?=$art['id']?>" />
                        <input type="hidden" name="SSForm[<?=$FORM_ID?>][qty]" value="0" />
                        <input id="ss-submit" name="SSForm[<?=$FORM_ID?>][submit]" type="submit" class="ss-submit" value="<?=$label_entfernen?>" />
                    </form>
                </td>
            <? endif; ?>
            </tr>
<?
		endforeach;
?>
        	<tr>
            	<th colspan="2" class="ss-empty_cart">
             <? if(!isset($simpleView)): ?>
                    <form action="" method="post" name="ss-form-empty_cart" class="ss-form ss-form-empty_cart">
                        <input type="hidden" name="SSForm[<?=$FORM_ID?>][action]" value="<?=$action_empty_cart?>" />
                        <input id="ss-submit" name="SSForm[<?=$FORM_ID?>][submit]" type="submit" class="ss-submit" value="<?=$label_empty_cart?>" />
                    </form>
            <? endif; ?>
                </th>
            	<th colspan="4" class="ss-total" align="right"><?=$label_total?> <?=$currency?> <?=$total?></th>
            <? if(!isset($simpleView)): ?>
            	<th align="right">
                    <form action="<?=$url_checkout?>" method="post" class="ss-form">
                        <input id="ss-submit" name="SSForm[<?=$FORM_ID?>][submit]" type="submit" class="ss-submit" value="<?=$label_checkout?>" />
                    </form>
                	<!--<a href="<?=$url_checkout?>"><?=$label_checkout?></a>-->
                </th>
            <? endif; ?>
            </tr>
        </table>