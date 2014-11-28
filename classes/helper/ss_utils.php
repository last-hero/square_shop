<?php
class ss_utils {
	public static function vars($_param){
		$REX;
		$page      		  = rex_request('page'   , 'string');
		$subpage   		  = rex_request('subpage', 'string');
		$minorpage 		  = rex_request('minorpage', 'string');
		$func      		  = rex_request('func'   , 'string');
		$table      	  = rex_request('table'   , 'string');
		$id      		  = rex_request('id', 'string');
		$baseurl   		  = 'index.php?page='.$page.'&amp;subpage='.$subpage;
		$myREX 			  = $REX['ADDON'][$page];
	
	}
	public static function i18l($_str){
		global $I18N;
		$str = 'square_shop_'.$_str;
		$str = str_replace('square_shop_square_shop_', 'square_shop_', $str);
		
		$i18l_str = $I18N->msg($str);
		if(substr_count($i18l_str, 'translate:')){
			ss_utils::seti18l($str, $_str);
		}
		return $I18N->msg($str);
	}
	public static function seti18l($_k, $_v){
		global $REX;
		$file = $REX['INCLUDE_PATH'] . '/addons/square_shop/lang/de_de_utf8.lang';
		if(substr_count(file_get_contents($file),$_k) == false) {
			//$old = umask(0);
			$fp = fopen($file, 'a+');
			//chmod($file, 0777);
			//umask($old);
			fwrite($fp, "\n$_k = $_v");
			fclose ( $fp );
		}
	}
}

