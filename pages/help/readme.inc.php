<?php

$search = array('(CHANGELOG.md)', '(LICENSE.md)', '(QUICKSTART.md)');
$replace = array(
	'(index.php?page=square_shop&subpage=help&chapter=changelog)'
	, '(index.php?page=square_shop&subpage=help&chapter=license)'
	, '(index.php?page=square_shop&subpage=help&chapter=quickstart)'
);

echo rex_square_shop_utils::getHtmlFromMDFile('README.md', $search, $replace);

