        <form action="" method="post" name="ss-form-<?=$FORM_ID?>" class="ss-form ss-form-<?=$FORM_ID?>">
            <input type="hidden" name="SSForm[<?=$FORM_ID?>][action]" value="<?=$action?>" />
            <input type="hidden" name="SSForm[<?=$FORM_ID?>][uniqueId]" value="<?=microtime(true)?>" />
<?
			if($login_error):
?> 
                <p class="ss-error ss-error-login"><?=$login_error?></p>
<?
			endif;
?>
<?
foreach($fields as $f):
    switch ($f['type']){
        case 'text':
            $fname = $f['name'];
            $label = $f['label'];
            $required = $f['required']?'required':'';
            $value_type = $f['value_type']?$f['value_type']:'';
            $max = $f['max']?$f['max']:255;
            $post_data = $formPropertiesAndValues[$fname];
			$errors = $formPropertyValueErrors[$fname];
			$css_class = 'ss-'.$fname.' '.$required.' '.$value_type;
			if(count($errors) > 0){
				$css_class .= ' error';
			}
?>
            <p class="<?=$css_class?>">
                <label for="ss-<?=$fname?>"><?=$label?></label>
                <input id="ss-<?=$fname?>" name="SSForm[<?=$FORM_ID?>][<?=$fname?>]" 
                	maxlength="<?=$max?>" type="text" class="<?=trim($css_class)?>" value="<?=$post_data?>" />
            </p>
<?
            break;
        case 'password':
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
            </p>
<?
            break;
    }
endforeach;
?>
            <? $fname = 'submit'; ?>
            <p class="ss-<?=$fname?>">
                <input id="ss-<?=$fname?>" name="SSForm[<?=$FORM_ID?>][submit]" type="submit" class="ss-<?=$fname?>" value="<?=$label_submit?>" />
            </p>
        </form>