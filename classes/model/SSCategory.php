<?php
#
#
# SSCategory
# https://github.com/last-hero/square_shop
#
# (c) Gobi Selva
# http://www.square.ch
#
# Diese Klasse dient für das Modellieren von
# Kategorie Daten
#
#

class SSCategory extends SSObjectTable{
	// Tabellenname
	const TABLE = 'category';
	protected $TABLE = self::TABLE;
	
	// Fehlermeldungs ID für falsche Feldername
	// die nicht in der DB Tabelle vorhanden
	// oder nicht erlaubt sind zu manipulieren
	const ERROR_TABLE_ATTR_DIFF = '8001';
	protected $ERROR_TABLE_ATTR_DIFF = self::ERROR_TABLE_ATTR_DIFF;
	
	
	/*
	* alle Kategorien holen
	* return array
	*/
	public function getCategories(){
		$res = $this->_getWhere("1=1");
		if(count($res) > 0){
			try{
				return $res;
			}catch(SSException $e) {
				echo $e;
			}
		}
		return array();
	}
}

