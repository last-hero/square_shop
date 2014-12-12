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
	SSDBSQL::executeSql(SSDBSQL::_getSqlCreateTables(), false);
	
	SSHelper::importTranslationsInStringTable();
}catch(SSException $e) {
	$error = $e;
}
		
if ($error == '') {
	$REX['ADDON']['install']['square_shop'] = true;
} else {
	$REX['ADDON']['installmsg']['square_shop'] = $error;
}