		<table class="ss-cart" width="100%">
        	<tr>
            	<th class="ss-img"><?=$label_bild?></th>
            	<th class="ss-artno"><?=$label_artno?></th>
            	<th class="ss-title"><?=$label_bezeichnung?></th>
            	<th class="ss-price" align="right"><?=$label_price?></th>
            	<th class="ss-qty" align="right"><?=$label_qty?></th>
            	<th class="ss-subtotal" align="right"><?=$label_subtotal?></th>
            	<th class="ss-delfromcart"></th>
            </tr>
        	<tr>
<?
		foreach($articles as $art):
?>
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
            	<td class="ss-artno"><a href="<?=$art['url']?>"><?=$art['no']?></a></td>
            	<td class="ss-title"><a href="<?=$art['url']?>"><?=$art['title']?></a></td>
            	<td class="ss-price" align="right"><?=$currency?> <?=$art['price']?></td>
            	<td class="ss-qty" align="right">
                    <form action="" method="post" name="ss-form-update_art" class="ss-form ss-form-update_art">
                        <input type="hidden" name="SSForm[<?=$FORM_ID?>][action]" value="<?=$action_update_art?>" />
                        <input type="hidden" name="SSForm[<?=$FORM_ID?>][id]" value="<?=$art['id']?>" />
                        <label for="ss-qty"><?=$label?></label>
                        <input id="ss-qty" name="SSForm[<?=$FORM_ID?>][qty]" maxlength="2" type="text" class="<?=trim($css_class)?>" value="<?=$art['qty']?>" />
                        <input id="ss-submit" name="SSForm[<?=$FORM_ID?>][submit]" type="submit" class="ss-submit" value="<?=$label_update_art?>" />
                    </form>
                </td>
            	<td class="ss-subtotal" align="right"><?=$currency?> <?=$art['subtotal']?></td>
            	<td class="ss-delfromcart" align="right">
                    <form action="" method="post" name="ss-form-delfromcart" class="ss-form ss-form-delfromcart">
                        <input type="hidden" name="SSForm[<?=$FORM_ID?>][action]" value="<?=$action_del_from_cart?>" />
                        <input type="hidden" name="SSForm[<?=$FORM_ID?>][id]" value="<?=$art['id']?>" />
                        <input type="hidden" name="SSForm[<?=$FORM_ID?>][qty]" value="0" />
                        <input id="ss-submit" name="SSForm[<?=$FORM_ID?>][submit]" type="submit" class="ss-submit" value="<?=$label_entfernen?>" />
                    </form>
                </td>
            </tr>
<?
		endforeach;
?>
        	<tr><th colspan="7">&nbsp;</th></tr>
        	<tr>
            	<th colspan="2" class="ss-empty_cart">
                    <form action="" method="post" name="ss-form-empty_cart" class="ss-form ss-form-empty_cart">
                        <input type="hidden" name="SSForm[<?=$FORM_ID?>][action]" value="<?=$action_empty_cart?>" />
                        <input id="ss-submit" name="SSForm[<?=$FORM_ID?>][submit]" type="submit" class="ss-submit" value="<?=$label_empty_cart?>" />
                    </form>
                </th>
            	<th colspan="3" class="ss-label-total" align="right"><?=$label_total?></th>
            	<th colspan="3" class="ss-total" align="right"><?=$currency?> <?=$total?></th>
            </tr>
        </table>