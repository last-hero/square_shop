<?php
// ADDON PARAMETER AUS URL HOLEN
////////////////////////////////////////////////////////////////////////////////
$page      		  = rex_request('page'   , 'string');
$subpage   		  = rex_request('subpage', 'string');
$minorpage 		  = rex_request('minorpage', 'string');
$func      		  = rex_request('func'   , 'string');
$table      	  = rex_request('table'   , 'string');
$id      		  = rex_request('id', 'string');
$baseurl   		  = 'index.php?page='.$page.'&amp;subpage='.$subpage;
$myREX 			  = $REX['ADDON'][$page];


if($func == '' or $func == 'search'){
	// Einfache Suche
	SSGUI::displayFormSearch($subpage);
	
	// List ausgeben
	SSGUI::displayList($subpage);
}elseif(($func == 'edit' or $func == 'add') and $REX['USER']->isAdmin()){
	// Form - Editieren + Hinzufügen
	SSGUI::displayFormAddEdit($subpage, $id);
}elseif($func == 'detail'){
	// Detailansicht
	SSGUI::displayDetail($subpage, $id);
}
?>