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

// payment select box
$payment_select = new rex_select();
$payment_select->setSize(5);
$payment_select->setName('settings[payment][]');
$payment_select->setMultiple(true);
$payment_options = array('onbill', 'paypal');
foreach($payment_options as $option){
	$payment_select->addOption(ss_utils::i18l($option),$option);
	if(in_array($option, $REX['ADDON']['square_shop']['settings']['payment'])){
		$payment_select->setSelected($option);
	}
}
/*
if(count($REX['ADDON']['square_shop']['settings']['payment']) == 0){
	$payment_select->setSelected($payment_options[0]);
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
					<? $fname = 'payment' ?>
                    <div class="rex-form-row">
                        <p class="rex-form-col-a rex-form-select">
							<label for="<?=$fname?>"><?=ss_utils::i18l('settings_'.$fname)?></label>
                            <?php echo $payment_select->get(); ?>
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



<div class="rex-addon-output">
	<div class="rex-form">
		<h2 class="rex-hl2"><?=ss_utils::i18l('settings_paypal_configuration')?></h2>
		<form action="index.php" method="post">
			<fieldset class="rex-form-col-1">
				<div class="rex-form-wrapper">
					<input type="hidden" name="page" value="<?=$page; ?>" />
					<input type="hidden" name="subpage" value="<?=$subpage; ?>" />
					<input type="hidden" name="func" value="update" />
                    
					<? $fname = 'paypal_business' ?>
					<div class="rex-form-row rex-form-element-v1">
						<p class="rex-form-text">
							<label for="<?=$fname?>"><?=ss_utils::i18l('settings_'.$fname)?></label>
							<input type="text" value="<?=$REX['ADDON']['square_shop']['settings'][$fname]?>" name="settings[<?=$fname?>]" id="<?=$fname?>" class="rex-form-text">
						</p>
					</div>
                    
					<? $fname = 'paypal_mail_errors_to' ?>
					<div class="rex-form-row rex-form-element-v1">
						<p class="rex-form-text">
							<label for="<?=$fname?>"><?=ss_utils::i18l('settings_'.$fname)?></label>
							<input type="text" value="<?=$REX['ADDON']['square_shop']['settings'][$fname]?>" name="settings[<?=$fname?>]" id="<?=$fname?>" class="rex-form-text">
						</p>
					</div>
					<? $fname = 'paypal_debug' ?>
                    <?
						$paypal_select = new rex_select();
						$paypal_select->setSize(1);
						$paypal_select->setName('settings['.$fname.']');
						$paypal_select->setMultiple(false);
						$paypal_options = array('0', '1');
						foreach($paypal_options as $option){
							$paypal_select->addOption(ss_utils::i18l('settings_'.$fname.'_'.$option),$option);
							if($option == $REX['ADDON']['square_shop']['settings'][$fname]){
								$paypal_select->setSelected($option);
							}
						}
					?>
                    <div class="rex-form-row">
                        <p class="rex-form-col-a rex-form-select">
							<label for="<?=$fname?>"><?=ss_utils::i18l('settings_'.$fname)?></label>
                            <?php echo $paypal_select->get(); ?>
                            <br />
                            data/addons/<?=$page?>/paypal.ipn.log<br>
                        </p>
                    </div>
					<? $fname = 'paypal_use_sandbox' ?>
                    <?
						$paypal_select = new rex_select();
						$paypal_select->setSize(1);
						$paypal_select->setName('settings['.$fname.']');
						$paypal_select->setMultiple(false);
						$paypal_options = array('0', '1');
						foreach($paypal_options as $option){
							$paypal_select->addOption(ss_utils::i18l('settings_'.$fname.'_'.$option),$option);
							if($option == $REX['ADDON']['square_shop']['settings'][$fname]){
								$paypal_select->setSelected($option);
							}
						}
					?>
                    <div class="rex-form-row">
                        <p class="rex-form-col-a rex-form-select">
							<label for="<?=$fname?>"><?=ss_utils::i18l('settings_'.$fname)?></label>
                            <?php echo $paypal_select->get(); ?>
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
            <p><strong>Datenbank</strong></p>
            <ul>
                <li>
                	<a href="<?=$baseurl?>&func=admin&action=db-delete">Tabellen löschen</a>
                	<span class="lower">Mehrmals ausführen um wirklich alle Daten zu entfernen</span>
                </li>
                <li><a href="<?=$baseurl?>&func=admin&action=db-create">Tabellen erstellen</a></li>
                <li><a href="<?=$baseurl?>&func=admin&action=db-import">Beispiel Daten importieren</a></li>
            </ul>
        </div>
		
     	<div class="rex-addon-content">
            <p><strong>StringTable - Sprachelemente</strong></p>
            <ul>
                <li>
                	<a href="<?=$baseurl?>&func=admin&action=i18n-delete">löschen</a> 
                	<span class="lower">Alle Übersetzung aus der Tabelle löschen.</span>
                </li>
                <li>
                	<a href="<?=$baseurl?>&func=admin&action=i18n-import">importieren</a>
                	<span class="lower">Bereits erfasste Übersetzung werden nicht aktualisiert.</span>
                </li>
            </ul>
        </div>
		
     	<div class="rex-addon-content">
            <p><strong>Multilanguage Felder</strong></p>
            <ul>
                <li>
                	<a href="<?=$baseurl?>&func=admin&action=i18n-db-field-update">aktualisieren</a>
                	<span class="lower">Felder werden entweder hinzugefügt (falls neue Sprache) oder gelöscht (falls Sprache nicht mehr vorhanden)</span>
                </li>
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
				
				
				if($action == 'i18n-delete'){
					$res = SSHelper::deleteTranslationsFromStringTable();
					echo rex_info('Sprachelemente ('.$res['rows'].') wurden aus StringTable gelöscht!');
				}elseif($action == 'i18n-import'){
					SSHelper::importTranslationsToStringTable();
					echo rex_info('Sprachelemente wurden zu StringTable importiert!');
				}
				
				
				if($action == 'i18n-db-field-update'){
					//echo rex_warning('<span style="font-size:150px">☺</span><span style="font-size:50px">TODO</span>');
					?><span style="font-size:150px">☺</span><span style="font-size:50px">TODO</span>
                    <?
				}
			?>
        </div>
	</div>
</div>
<? endif;?>

