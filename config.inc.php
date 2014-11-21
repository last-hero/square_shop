<?php
// init addon
$mypage = 'square_shop';
$REX['ADDON']['name'][$mypage] = 'Online Shop';
$REX['ADDON']['page'][$mypage] = $mypage;
$REX['ADDON']['version'][$mypage] = '0.5.0';
$REX['ADDON']['author'][$mypage] = 'Gobi Selva';
$REX['ADDON']['supportpage'][$mypage] = 'forum.redaxo.org';
$REX['ADDON']['perm'][$mypage] = 'square_shop[]';
$REX['ADDON'][$mypage]['templatepaths'][] = $REX['INCLUDE_PATH'] . '/addons/'.$mypage.'/templates/';

// permissions
$REX['PERM'][] = 'square_shop[]';

// add lang file
if ($REX['REDAXO']) {
	$I18N->appendFile($REX['INCLUDE_PATH'] . '/addons/'.$mypage.'/lang/');
}

// includes
require($REX['INCLUDE_PATH'] . '/addons/'.$mypage.'/classes/class.rex_'.$mypage.'_utils.inc.php');
require($REX['INCLUDE_PATH'] . '/addons/'.$mypage.'/classes/class.ss_utils.inc.php');

$classes = array('SSException', 'SSDBSchema', 'SSDBSQL', 'SSGUI', 'SSImport', 'SSHelper', 'SSClient', 'SSCart', 'SSItem');
foreach($classes as $class){
	if (!class_exists($class)) {
		require_once($REX['INCLUDE_PATH'] . '/addons/'.$mypage.'/classes/class.'.$class.'.inc.php');
	}
}


// overwrite default settings with user settings
rex_square_shop_utils::includeSettingsFile();

function rex_square_shop_script($params){
	global $REX;
	$params['subject'] .= "\n  " . '<link rel="stylesheet" type="text/css" href="../files/addons/square_shop/square_shop.css" />';
	$params['subject'] .= "\n  " . '<script src="../files/addons/square_shop/square_shop.js" type="text/javascript"></script>';
	return $params['subject'];
}
rex_register_extension('PAGE_HEADER', 'rex_square_shop_script');

if ($REX['REDAXO']) {
	// add subpages
	$REX['ADDON'][$mypage]['SUBPAGES'] = array(
		array('', ss_utils::i18l('start')),
		array('article', ss_utils::i18l('article')),
		array('category', ss_utils::i18l('category')),
		array('order', ss_utils::i18l('order')),
		array('client', ss_utils::i18l('client')),
		array('settings', ss_utils::i18l('settings')),
		array('help', ss_utils::i18l('help'))
	);
}
?>
