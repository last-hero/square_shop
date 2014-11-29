			<form action="" method="post" name="ss-form-login">
            	<input type="hidden" name="SSForm[<?=$FORM_ID?>][action]" value="<?=$action?>" />
<?
			if($login_error):
?> 
                <p class="ss-login_error"><?=$login_error?></p>
<?
			endif;
?>
                <p class="ss-email">
                    <label for="ss-email"><?=$label_email?></label>
                    <input id="ss-email" name="SSForm[<?=$FORM_ID?>][<?=$fn_email?>]" type="text" 
                    	class="ss-email required <?=$login_error?'error':''?>" value="" />
                </p>
                <p class="ss-password">
                    <label for="ss-password"><?=$label_password?></label>
                    <input id="ss-password" name="SSForm[<?=$FORM_ID?>][<?=$fn_password?>]" type="text" 
                    	class="ss-password required <?=$login_error?'error':''?>" value="" />
                </p>
                <p class="ss-submit">
                    <input id="ss-submit" name="SSForm[<?=$FORM_ID?>][submit]" type="submit" class="ss-submit" value="<?=$label_submit?>" />
                </p>
			</form>