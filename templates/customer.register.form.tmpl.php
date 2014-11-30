        <form action="" method="post" name="ss-form-<?=$FORM_ID?>" class="ss-form ss-form-<?=$FORM_ID?>">
            <input type="hidden" name="SSForm[<?=$FORM_ID?>][action]" value="<?=$action?>" />
            <input type="hidden" name="SSForm[<?=$FORM_ID?>][uniqueId]" value="<?=microtime(true)?>" />
<?
foreach($fields as $f):
    switch ($f['type']){
        case 'text':
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
<?
			if($formPropertyValueErrors[$fname]):
				$label_error = $label_errors[key($formPropertyValueErrors[$fname])];
?>
                <label for="ss-<?=$fname?>" class="error"><?=$label_error?></label>
<?
			endif;
?>
            </p>
<?
            $fname = $f['name'].'_re';
            $label = '&nbsp;';
?>
            <p class="<?=$css_class?>">
                <label for="ss-<?=$fname?>"><?=$label?></label>
                <input id="ss-<?=$fname?>" name="SSForm[<?=$FORM_ID?>][<?=$fname?>]"
                	maxlength="<?=$max?>" type="password" 
                    class="<?=trim($css_class)?>" value="" />
            </p>
<?
            break;
        case 'select':
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
				$label_error = $label_errors[key($formPropertyValueErrors[$fname])];
?>
                <label for="ss-<?=$fname?>" class="error"><?=$label_error?></label>
<?
			endif;
?>
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