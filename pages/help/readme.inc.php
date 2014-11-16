<?php

$search = array('(CHANGELOG.md)', '(LICENSE.md)');
$replace = array('(index.php?page=square_shop&subpage=help&chapter=changelog)', '(index.php?page=square_shop&subpage=help&chapter=license)');

echo rex_square_shop_utils::getHtmlFromMDFile('README.md', $search, $replace);

