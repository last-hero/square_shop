<?
            $fname = $f['name'];
            $label = $f['label'];
            $options = $f['values'];
            $label_options = $f['label_values'];
            $required = $f['required']?'required':'';
            $post_data = $formPropertiesAndValues[$fname];
			$errors = $formPropertyValueErrors[$fname];
			$css_class = 'ss-'.$fname.' '.$required;
			if(count($errors) > 0){
				$css_class .= ' error';
			}
?>
            <p class="<?=$css_class?>">
                <label for="ss-<?=$fname?>"><?=$label?></label>
                <select id="ss-<?=$fname?>" name="SSForm[<?=$FORM_ID?>][<?=$fname?>]" class="<?=trim($css_class)?>" size="1">
                    <option value="" selected="selected">-</option>
<?
                for($x=0; $x<sizeof($options); $x++):
?>
                    <option <?=$post_data==$options[$x]?'selected="selected"':''?> value="<?=$options[$x]?>"><?=$label_options[$x]?></option>
<?
                endfor;
?>
                </select>
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