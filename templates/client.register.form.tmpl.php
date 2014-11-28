			<form action="" method="post" name="ss-form-register" class="ss-form-register">
            	<input type="hidden" name="SSForm[<?=$FORM_ID?>][action]" value="<?=$action?>" />
<?
	foreach($fields as $f):
		switch ($f['type']){
			case 'text':
				$fname = $f['name'];
				$label = $f['label'];
				$post_data = $form_data[$fname];
?>
                <p class="ss-<?=$fname?>">
                    <label for="ss-<?=$fname?>"><?=$label?></label>
                    <input id="ss-<?=$fname?>" name="SSForm[<?=$FORM_ID?>][<?=$fname?>]" type="text" class="ss-<?=$fname?>" value="<?=$post_data?>" />
                </p>
<?
				break;
			case 'password':
				$fname = $f['name'];
				$label = $f['label'];
?>
                <p class="ss-<?=$fname?>">
                    <label for="ss-<?=$fname?>"><?=$label?></label>
                    <input id="ss-<?=$fname?>" name="SSForm[<?=$FORM_ID?>][<?=$fname?>]" type="password" class="ss-<?=$fname?>" value="" />
                </p>
<?
				$fname = $f['name'].'_re';
				$label = '&nbsp;';
?>
                <p class="ss-<?=$fname?>">
                    <label for="ss-<?=$fname?>"><?=$label?></label>
                    <input id="ss-<?=$fname?>" name="SSForm[<?=$FORM_ID?>][<?=$fname?>]" type="password" class="ss-<?=$fname?>" value="" />
                </p>
<?
				break;
			case 'select':
				$fname = $f['name'];
				$label = $f['label'];
				$options = $f['constraint_vals'];
				$label_options = $f['label_constraint_vals'];
				$post_data = $form_data[$fname];
?>
                <p class="ss-<?=$fname?>">
                    <label for="ss-<?=$fname?>"><?=$label?></label>
                    <select class="ss-<?=$fname?>" id="ss-<?=$fname?>" name="SSForm[<?=$FORM_ID?>][<?=$fname?>]" size="1">
                        <option value="" selected="selected">-</option>
<?
					for($x=0; $x<sizeof($options); $x++):
?>
                        <option <?=$post_data==$options[$x]?'selected="selected"':''?> value="<?=$options[$x]?>"><?=$label_options[$x]?></option>
<?
					endfor;
?>
                    </select>
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