		<article class="ss-article-detail">
        	<aside>
                <h2>
                    <?=$title?>
                </h2>
                <p class="ss-article-detail-no">
                    <?=$no?>
                </p>
                <p class="ss-article-detail-desc">
                    <?=$description?>
                </p>
                <p class="ss-article-detail-price">
                    <?=$currency?> <?=$price?>
                </p>
<?
		$fname = 'qty';
		$label = $label_qty;
		$max = 2;
?>
                <form action="" method="post" name="ss-form-addtocart" class="ss-form ss-form-addtocart">
                    <input type="hidden" name="SSForm[<?=$FORM_ID?>][action]" value="<?=$action?>" />
                    <input type="hidden" name="SSForm[<?=$FORM_ID?>][id]" value="<?=$id?>" />
                    <p class="<?=$css_class?>">
                        <label for="ss-<?=$fname?>"><?=$label?></label>
                        <input id="ss-<?=$fname?>" name="SSForm[<?=$FORM_ID?>][<?=$fname?>]" 
                        maxlength="<?=$max?>" type="text" class="<?=trim($css_class)?>" value="1" />
                        <input id="ss-submit" name="SSForm[<?=$FORM_ID?>][submit]" type="submit" class="ss-submit" value="<?=$label_submit?>" />
                    </p>
                </form>
                <p class="ss-article-detail-goback">
                    <a href="<?=$url?>"><?=$label_goback?></a>
                </p>
            </aside>
<?
		if(count($imgs)):
?>
        	<figure>
<?
			foreach($imgs as $img):
?>
            	<img src="index.php?rex_img_type=ss-article-detail&rex_img_file=<?=$img?>" />
<?
			endforeach;
?>
            </figure>
<?
		endif;
?>
        </article>