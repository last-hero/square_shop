	<div class="ss-form-outer ss-form-outer-<?=$FORM_ID?>">
        <form action="" method="post" name="ss-form-<?=$FORM_ID?>" class="ss-form ss-form-<?=$FORM_ID?>">
            <input type="hidden" name="SSForm[<?=$FORM_ID?>][action]" value="<?=$action?>" />
            <input type="hidden" name="SSForm[<?=$FORM_ID?>][uniqueId]" value="<?=hash('md5', microtime(true))?>" />
            <? $fname = $action; ?>
            <? $post_data = $formPropertiesAndValues[$fname]; ?>
			<p class="ss-<?=$fname?> required">
                <label for="ss-<?=$fname?>"><?=SSHelper::i18n('label_'.$FORM_ID.'_payment')?></label>
                <select id="ss-<?=$fname?>" name="SSForm[<?=$FORM_ID?>][<?=$fname?>]" class="ss-<?=$fname?> required" size="<?=count($payments)+1?>">
                <? foreach($payments as $payment): ?>
                    <option value="<?=$payment?>" <?=$post_data==$payment?' selected=selected ':''?>>
						<?=SSHelper::i18n('label_'.$payment)?>
                    </option>
                <? endforeach; ?>
                </select>
            </p>
            
            <br /><br />
            <? $fname = 'submit'; ?>
            <p class="ss-<?=$fname?>">
                <input id="ss-<?=$fname?>-next" name="SSForm[<?=$FORM_ID?>][submit]" type="submit" class="ss-<?=$fname?> ss-next" value="<?=$label_submit?>" />
            </p>
        </form>
        
		<? if($showGoBackBtn): ?>
        <? $jumpToStep = (int)$jumpToStep > 0 ? $jumpToStep : (int)$step -1; ?>
        <form action="" method="post" name="ss-form-<?=$FORM_ID?>" class="ss-form ss-form-goback ss-form-<?=$FORM_ID?>">
            <input type="hidden" name="SSForm[<?=$FORM_ID?>][action]" value="<?=$action?>" />
            <input type="hidden" name="SSForm[<?=$FORM_ID?>][uniqueId]" value="<?=hash('md5', microtime(true))?>" />
            <input type="hidden" name="SSForm[<?=$FORM_ID?>][jumpToStep]" value="<?=$jumpToStep?>" />
            <? $fname = 'submit'; ?>
			<input id="ss-<?=$fname?>-prev" name="SSForm[<?=$FORM_ID?>][submit]" type="submit" class="ss-<?=$fname?> ss-prev" value="<?=SSHelper::i18n('label_goback')?>" />
        </form>
		<? endif; ?>
	</div>