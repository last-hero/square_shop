<?
		$fname = $f['name'];
		$label = $f['label'];
		$max = $f['max']?$f['max']:255;
		$value_type = $f['value_type']?$f['value_type']:'';
		$required = $f['required']?'required':'';
		$errors = $formPropertyValueErrors[$fname];
		$css_class = 'ss-'.$fname.' '.$required.' '.$value_type;
		if(count($errors) > 0){
			$css_class .= ' error';
		}
?>
            <p class="<?=$css_class?>">
                <label for="ss-<?=$fname?>"><?=$label?></label>
                <input id="ss-<?=$fname?>" name="SSForm[<?=$FORM_ID?>][<?=$fname?>]" 
                	maxlength="<?=$max?>" type="password" 
                    class="<?=trim($css_class)?>" value="" />
<?
			if($formPropertyValueErrors[$fname]):
				$label_error = SSHelper::i18n('label_error_'.key($errors));
				$label_error = sprintf($label_error,$f[key($errors)]);
?>
                <label for="ss-<?=$fname?>" class="error"><?=$label_error?></label>
<?
			endif;
?>
            </p>
<?
		if(isset($f['equalto']) and $f['equalto'] ==  $f['name'].'_re'):
			$fname = $f['name'].'_re';
			$label = $f['label_equalto'];
?>	
            <p class="<?=$css_class?>">
                <label for="ss-<?=$fname?>"><?=$label?></label>
                <input id="ss-<?=$fname?>" name="SSForm[<?=$FORM_ID?>][<?=$fname?>]"
                	maxlength="<?=$max?>" type="password" 
                    class="<?=trim($css_class)?>" value="" />
            </p>
<?
		endif;
?>