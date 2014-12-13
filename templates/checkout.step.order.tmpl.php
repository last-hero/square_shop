	<div class="ss-form-outer ss-form-outer-<?=$FORM_ID?>">
        <form action="" method="post" name="ss-form-<?=$FORM_ID?>" class="ss-form ss-form-<?=$FORM_ID?>">
            <input type="hidden" name="SSForm[<?=$FORM_ID?>][action]" value="<?=$action?>" />
            <input type="hidden" name="SSForm[<?=$FORM_ID?>][uniqueId]" value="<?=hash('md5', microtime(true))?>" />
            <input type="hidden" name="SSForm[<?=$FORM_ID?>][confirm]" value="1" />
            
            
            <br /><br />
            <? $fname = 'submit'; ?>
            <p class="ss-<?=$fname?>">
                <input id="ss-<?=$fname?>" name="SSForm[<?=$FORM_ID?>][submit]" type="submit" class="ss-<?=$fname?>" value="<?=$label_submit?>" />
            </p>
        </form>
        
        <form action="" method="post" name="ss-form-<?=$FORM_ID?>" class="ss-form ss-form-goback ss-form-<?=$FORM_ID?>">
            <input type="hidden" name="SSForm[<?=$FORM_ID?>][action]" value="<?=$action?>" />
            <input type="hidden" name="SSForm[<?=$FORM_ID?>][uniqueId]" value="<?=hash('md5', microtime(true))?>" />
            <input type="hidden" name="SSForm[<?=$FORM_ID?>][jumpTostep]" value="<?=(int)$step -1?>" />
            <? $fname = 'submit'; ?>
			<input id="ss-<?=$fname?>" name="SSForm[<?=$FORM_ID?>][submit]" type="submit" class="ss-<?=$fname?>" value="<?=SSHelper::i18n('label_goback')?>" />
        </form>
    </div>