<?php

$mypage = 'square_shop';
$classes = array('SSDBSchema', 'SSDBSQL', 'SSException');
foreach($classes as $class){
	if (!class_exists($class)) {
		require_once($REX['INCLUDE_PATH'] . '/addons/'.$mypage.'/classes/class.'.$class.'.inc.php');
	}
}

$error = '';
try{
	// Tabellen erstellen
	SSDBSQL::executeSql(SSDBSQL::_getSqlCreateTables(), false);
	
	// Falls StringTable vorhanden
	// Sprachelemente zu StringTable importieren
	SSHelper::importTranslationsToStringTable();
}catch(SSException $e) {
	$error = $e;
}
		
if ($error == '') {
	$REX['ADDON']['install']['square_shop'] = true;
} else {
	$REX['ADDON']['installmsg']['square_shop'] = $error;
}