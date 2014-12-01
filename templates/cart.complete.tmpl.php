		<table class="ss-cart" width="100%">
        	<tr>
            	<th class="ss-img"><?=$label_bild?></th>
            	<th class="ss-artno"><?=$label_artno?></th>
            	<th class="ss-title"><?=$label_bezeichnung?></th>
            	<th class="ss-price" align="right"><?=$label_price?></th>
            	<th class="ss-qty" align="right"><?=$label_qty?></th>
            	<th class="ss-subtotal" align="right"><?=$label_subtotal?></th>
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
				<?=$art['qty']?>
                
                <!--<form action="" method="post" name="ss-form-addtocart" class="ss-form ss-form-addtocart">
                    <input type="hidden" name="SSForm[<?=$FORM_ID?>][action]" value="<?=$action?>" />
                    <input type="hidden" name="SSForm[<?=$FORM_ID?>][id]" value="<?=$id?>" />
                    <p class="<?=$css_class?>">
                        <label for="ss-<?=$fname?>"><?=$label?></label>
                        <input id="ss-<?=$fname?>" name="SSForm[<?=$FORM_ID?>][<?=$fname?>]" 
                        maxlength="<?=$max?>" type="text" class="<?=trim($css_class)?>" value="1" />
                        <input id="ss-submit" name="SSForm[<?=$FORM_ID?>][submit]" type="submit" class="ss-submit" value="<?=$label_submit?>" />
                    </p>
                </form>-->
                </td>
            	<td class="ss-subtotal" align="right"><?=$currency?> <?=$art['subtotal']?></td>
            </tr>
<?
		endforeach;
?>
        </table>