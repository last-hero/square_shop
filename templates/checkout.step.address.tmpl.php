	<div class="ss-form-outer ss-form-outer-<?=$FORM_ID?>">
        <form action="" method="post" name="ss-form-<?=$FORM_ID?>" class="ss-form ss-form-<?=$FORM_ID?>">
            <input type="hidden" name="SSForm[<?=$FORM_ID?>][action]" value="<?=$action?>" />
            <input type="hidden" name="SSForm[<?=$FORM_ID?>][uniqueId]" value="<?=hash('md5', microtime(true))?>" />
			<h2><?=SSHelper::i18n('label_'.$action.'_address')?></h2>
<?
foreach($fields as $f):
	try{			
		echo SSGUI::parse('form.field.'.$f['type'].'.tmpl.php', 
			array(
				'f'=>$f
				, 'FORM_ID'=>$FORM_ID
				, 'formPropertiesAndValues'=>$formPropertiesAndValues
				, 'formPropertyValueErrors'=>$formPropertyValueErrors
				//, 'label_errors'=>$label_errors
			)
		);
	}catch(SSException $e) {
		echo $e;
	}
endforeach;
?>

		<? if($show_diff_delivery_option): ?>
            <br /><br />
			<h2><?=SSHelper::i18n('label_delivery_address')?></h2>
            <? $fname = 'diff_delivery'; ?>
            <? $post_data = $formPropertiesAndValues[$fname]; ?>
			<p class="ss-<?=$fname?> required">
                <label for="ss-<?=$fname?>"><?=SSHelper::i18n('label_diff_delivery_address')?></label>
                <select id="ss-<?=$fname?>" name="SSForm[<?=$FORM_ID?>][<?=$fname?>]" class="ss-<?=$fname?> required" size="1">
                    <option value=""><?=SSHelper::i18n('label_no')?></option>
                    <option value="yes" <?=$post_data=='yes'?'selected="selected"':''?> >
						<?=SSHelper::i18n('label_yes')?>
                    </option>
                </select>
            </p>
		<? endif; ?>
            
            <br /><br />
            <? $fname = 'submit'; ?>
            <p class="ss-<?=$fname?>">
            	<? if($show_required_fields_info): ?>
            	<span class="info"><?=SSHelper::i18n('label_required_fields_info')?></span>
                <? endif; ?>
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