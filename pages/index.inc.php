<?php
// post vars
$page = rex_request('page', 'string');
$subpage = rex_request('subpage', 'string');

// if no subpage specified, use this one
if ($subpage == '') {
	$subpage = 'start';
}

// layout top
require($REX['INCLUDE_PATH'] . '/layout/top.php');

// title
rex_title($REX['ADDON']['name']['square_shop'] . ' <span style="font-size:14px; color:silver;">' . $REX['ADDON']['version']['square_shop'] . '</span>', $REX['ADDON']['square_shop']['SUBPAGES']);


// include subpage
if(in_array($subpage, array('article', 'category', 'order', 'client'))){
	include($REX['INCLUDE_PATH'] . '/addons/square_shop/pages/table_data.inc.php');
}else{
	include($REX['INCLUDE_PATH'] . '/addons/square_shop/pages/'.$subpage.'.inc.php');
}
?>

<?php 
// layout bottom
require($REX['INCLUDE_PATH'] . '/layout/bottom.php');
?>
