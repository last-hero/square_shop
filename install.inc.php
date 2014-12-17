<?php
$mypage = 'square_shop';

$classes = array('SSDBSchema', 'SSDBSQL', 'SSException', 'SSHelper');
foreach($classes as $class_name){
	if (!class_exists($class_name)) {
		$classes_folder = $REX['INCLUDE_PATH'] . '/addons/square_shop/classes/';
		$file = $classes_folder.''.$class_name.'.php';
		if(file_exists($file)) {
			require_once $file;
		}
		$sub_folders = array('model', 'view', 'controller', 'helper');
		foreach($sub_folders as $folder){
			$file = $classes_folder.''.$folder.'/'.''.$class_name.'.php';
			if(file_exists($file)) {
				require_once $file;
			}
		}
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