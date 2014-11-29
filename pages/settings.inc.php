<?php
// ADDON PARAMETER AUS URL HOLEN
////////////////////////////////////////////////////////////////////////////////
$page      		 = rex_request('page'   , 'string');
$subpage   		  = rex_request('subpage', 'string');
$minorpage 		= rex_request('minorpage', 'string');
$func      		 = rex_request('func'   , 'string');
$baseurl   		  = 'index.php?page='.$page.'&amp;subpage='.$subpage;
$myREX 			= $REX['ADDON'][$page];

// save settings
if ($func == 'update') {
	$settings = (array) rex_post('settings', 'array', array());

	rex_square_shop_utils::replaceSettings($settings);
	rex_square_shop_utils::updateSettingsFile();
}
?>
<?
	
	/*

	$clientLoginController = new SSClientLoginController();
	$clientLoginController->invoke();
	
	
	
	$session = SSSession::getInstance();
	
	if(isset($_POST['user']) and isset($_POST['user'])){
		$session->set('Customer', $customer);
	}
	if(!$session->get('customer')){
		$clientLoginView = new SSClientLoginView($session->get('customer'));
		$clientLoginView->displayLoginHtml();
	}else{
		$clientLoginView = new SSClientLoginView($session->get('customer'));
		$clientLoginView->displayLogoutHtml();
	}
	*/
	
	/*
	$customer = new SSClient();
	try{
		$customer->loadClientById(1);
		SSClientLoginView::displayLoginHtml();
		SSClientLoginView::displayLogoutHtml();
	}catch(SSException $e) {
		echo $e;
	}
	*/
	
	
	
?>

<div class="rex-addon-output">
	<div class="rex-form">
		<h2 class="rex-hl2"><?=ss_utils::i18l('settings')?></h2>
		<form action="index.php" method="post">
			<fieldset class="rex-form-col-1">
				<div class="rex-form-wrapper">
					<input type="hidden" name="page" value="<?=$page; ?>" />
					<input type="hidden" name="subpage" value="<?=$subpage; ?>" />
					<input type="hidden" name="func" value="update" />

					<div class="rex-form-row rex-form-element-v1">
						<p class="rex-form-text">
							<label for="currency"><?=ss_utils::i18l('settings_currency')?></label>
							<input type="text" value="<?=$REX['ADDON']['square_shop']['settings']['currency']; ?>" name="settings[currency]" id="currency" class="rex-form-text">
						</p>
					</div>

					<div class="rex-form-row rex-form-element-v1">
						<p class="rex-form-text">
							<label for="currency"><?=ss_utils::i18l('settings_mwst')?></label>
							<input type="text" value="<?=$REX['ADDON']['square_shop']['settings']['mwst']; ?>" name="settings[mwst]" id="mwst" class="rex-form-text">
						</p>
					</div>
					<div class="rex-form-row rex-form-element-v1">
						<p class="rex-form-submit">
							<input type="submit" class="rex-form-submit" name="sendit" value="<?=$I18N->msg('square_shop_settings_save'); ?>" />
						</p>
					</div>
				</div>
			</fieldset>
		</form>
	</div>
</div>
<? if ($REX['USER']->isAdmin()):?>
<div class="rex-addon-output">
	<div class="rex-form">
		<h2 class="rex-hl2">Admin Modus</h2>
		
     	<div class="rex-addon-content">
            <p>Datenbank</p>
            <ul>
                <li><a href="<?=$baseurl?>&func=admin&action=db-delete">Tabellen löschen</a></li>
                <li><a href="<?=$baseurl?>&func=admin&action=db-create">Tabellen erstellen</a></li>
                <li><a href="<?=$baseurl?>&func=admin&action=db-import">Beispiel Daten importieren</a></li>
            </ul>
        </div>
	</div>
</div>
<? endif;?>
<? if ($func == 'admin'):?>
<?
	$action= rex_request('action'   , 'string');
?>
<div class="rex-addon-output">
	<div class="rex-form">
		<h2 class="rex-hl2"><?=strtoupper($action)?></h2>
     	<div class="rex-addon-content">
			<? 
				if($action == 'db-delete'){
					SSDBSQL::executeSql(SSDBSQL::_getSqlDeleteTables(), true);
				}elseif($action == 'db-create'){
					SSDBSQL::executeSql(SSDBSQL::_getSqlCreateTables(), true);
				}elseif($action == 'db-import'){
					if(SSImport::importSamples()){
						echo rex_info('Beispiel Daten importiert');
					}else{
						echo rex_warning('Beispiel Daten Import FEHLGESCHLAGEN!');
					}
				}
			?>
        </div>
	</div>
</div>
<? endif;?>

