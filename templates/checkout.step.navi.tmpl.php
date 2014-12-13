    <ul class="ss-checkout ss-checkout-steps">
<?
	for($x=1; $x<=5; $x++):
?>
    	<li>
<?
		if($step_active == $x):
?>        
			<h2><span><?=$x?></span><strong><?=$label_steps[$x]?></strong></h2>
<?
		elseif($step_active > $x):
?> 
            
        <form action="" method="post" name="ss-form-<?=$FORM_ID?>" class="ss-form ss-form-<?=$FORM_ID?>">
            <input type="hidden" name="SSForm[<?=$FORM_ID?>][action]" value="<?=$action?>" />
            <input type="hidden" name="SSForm[<?=$FORM_ID?>][uniqueId]" value="<?=hash('md5', microtime(true))?>" />
            <input type="hidden" name="SSForm[<?=$FORM_ID?>][jumpToStep]" value="<?=$x?>" />
            
			<!--<h2><a href="<?=$url_steps[$x]?>" target="_self" title=""><span><?=$x?></span></a></h2>-->
            <? $fname = 'submit'; ?>
            
			<input id="ss-<?=$fname?>" name="SSForm[<?=$FORM_ID?>][submit]" type="submit" class="ss-<?=$fname?>" value="<?=$x?>" />
        </form>
<?
		else:
?> 
			<h2><span><?=$x?></span></h2>
<?
		endif;
?> 
        </li>
<?
	endfor;
?>
    </ul>