<?
            $fname = $f['name'];
            $label = $f['label'];
            $required = $f['required']?'required':'';
            $value_type = $f['value_type']?$f['value_type']:'';
            $max = $f['max']?$f['max']:255;
            $min = $f['min']?$f['min']:'';
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
<?
			if($formPropertyValueErrors[$fname]):
				$label_error = $label_errors[key($formPropertyValueErrors[$fname])];
?>
                <label for="ss-<?=$fname?>" class="error"><?=$label_error?></label>
<?
			endif;
?>
            </p>