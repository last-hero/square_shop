			<form action="" method="post" name="ss-form-login">
            	<input type="hidden" name="SSForm[<?=$FORM_ID?>][action]" value="<?=$action?>" />
                <p class="ss-email">
                    <label for="ss-email"><?=$label_email?></label>
                    <input id="ss-email" name="SSForm[<?=$FORM_ID?>][<?=$fn_email?>]" type="text" class="ss-email" value="" />
                </p>
                <p class="ss-password">
                    <label for="ss-password"><?=$label_password?></label>
                    <input id="ss-password" name="SSForm[<?=$FORM_ID?>][<?=$fn_password?>]" type="text" class="ss-password" value="" />
                </p>
                <p class="ss-submit">
                    <input id="ss-submit" name="SSForm[<?=$FORM_ID?>][submit]" type="submit" class="ss-submit" value="<?=$label_submit?>" />
                </p>
			</form>