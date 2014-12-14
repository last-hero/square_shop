<?php

$mypage = rex_request('page','string');
$subpage = rex_request('subpage', 'string');
$chapter = rex_request('chapter', 'string');
$func = rex_request('func', 'string');

// include markdwon parser
if (!class_exists('Parsedown')) {
	require($REX['INCLUDE_PATH'] . '/addons/square_shop/classes/helper/parsedown.php');
}

// chapters
$chapterpages = array (
	'' => array($I18N->msg('square_shop_help_chapter_readme'), 'pages/help/readme.inc.php'),
	'quickstart' => array($I18N->msg('square_shop_help_chapter_quickstart'), 'pages/help/quickstart.inc.php'),
	'changelog' => array($I18N->msg('square_shop_help_chapter_changelog'), 'pages/help/changelog.inc.php'),
);

// build chapter navigation
$chapternav = '';

foreach ($chapterpages as $chapterparam => $chapterprops) {
	if ($chapterprops[0] != '') {
		if ($chapter != $chapterparam) {
			$chapternav .= ' | <a href="?page=' . $mypage . '&amp;subpage=' . $subpage . '&amp;chapter=' . $chapterparam . '">' . $chapterprops[0] . '</a>';
		} else {
			$chapternav .= ' | <a class="rex-active" href="?page=' . $mypage . '&amp;subpage=' . $subpage . '&amp;chapter=' . $chapterparam . '">' . $chapterprops[0] . '</a>';
		}
	}
}
$chapternav = ltrim($chapternav, " | ");

// build chapter output
$addonroot = $REX['INCLUDE_PATH']. '/addons/'.$mypage.'/';
$source    = $chapterpages[$chapter][1];

// output
echo '
<div class="rex-addon-output" id="subpage-' . $subpage . '">
  <h2 class="rex-hl2" style="font-size:1em">' . $chapternav . '</h2>
  <div class="rex-addon-content">
    <div class= "addon-template">
    ';

include($addonroot . $source);

echo '
    </div>
  </div>
</div>';

?>

<script type="text/javascript">
jQuery(document).ready(function($) {
	// make external links clickable
	$("#subpage-help").delegate("a", "click", function(event) {
		var host = new RegExp("/" + window.location.host + "/");

		if (!host.test(this.href)) {
			event.preventDefault();
			event.stopPropagation();

			window.open(this.href, "_blank");
		}
	});
});
</script>
