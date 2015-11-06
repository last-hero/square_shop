<div class="rex-addon-output">
	<div class="rex-area-content">
		<div class="rex-form-wrapper">
			<form action="<?=$baseurl?>" method="post" name="form-search">
                <p class="rex-form-col-a rex-form-text" id="_top">
                    <input type="hidden" name="func" value="search" />
                    <label for="search"><?=ss_utils::i18l('searchkey')?></label>
                    <input id="search" name="search" type="text" class="rex-form-text" value="<?php echo $search; ?>" />
                    <input type="submit" name="submit" class="rex-form-submit submit" value="suchen" />
                    <?=($search != "")?'&nbsp;<a href="'.$baseurl.'" class="rex-offline">X</a>':'';?>
                </p>
                <br />
                <p class="rex-form-col-a rex-form-text">
<?
				foreach($fields as $field):
					$fieldname = $field['name'];
					$_attr_checked = ' checked=checked ';
					if(is_array($search_fields) and sizeof($search_fields) > 0){
						$_attr_checked = '';
						if(in_array($fieldname, $search_fields)){
							$_attr_checked = ' checked=checked ';
						}
					}
?>
                    <input type="checkbox" class="checkbox" name="search_fields[]" id="search_fields_<?=$fieldname?>" value="<?=$fieldname?>" <?=$_attr_checked?>>
                    <label class="checkbox" for="search_fields_<?=$fieldname?>"><?=ss_utils::i18l('label_'.$fieldname)?></label>
<?
				endforeach;
?>
                </p>
			</form>
		</div>
	</div>
</div>