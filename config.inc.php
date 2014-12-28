<?php
/** @brief Autoloader - Classes
 *
 *  Klassen werden automatisch eingebunden.
 *  @param $class_name: Klassenname 
 */
spl_autoload_register('ssAutoloader');
function ssAutoloader($class_name) {
	global $REX;
	$classes_folder = $REX['INCLUDE_PATH'] . '/addons/square_shop/classes/';
	$sub_folders = array('', 'model/', 'view/', 'controller/', 'helper/');
	foreach($sub_folders as $folder){
		$file = $classes_folder.''.$folder.''.$class_name.'.php';
		if(file_exists($file)) {
			require_once $file;
		}
	}
}

// init addon
$mypage = 'square_shop';
$REX['ADDON']['name'][$mypage] = 'Online Shop';
$REX['ADDON']['page'][$mypage] = $mypage;
$REX['ADDON']['version'][$mypage] = '0.9.0';
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
		array('customer', ss_utils::i18l('customer')),
		array('settings', ss_utils::i18l('settings')),
		array('help', ss_utils::i18l('help'))
	);
}
?>
