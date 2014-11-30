<?php
#
#
# SSArticle
# https://github.com/last-hero/square_shop
#
# (c) Gobi Selva
# http://www.square.ch
#
# Diese Klasse dient für das Modellieren von
# Artikel Daten
#
#

class SSArticle extends SSObjectTable{
	// Tabellenname
	const TABLE = 'article';
	protected $TABLE = self::TABLE;
	
	// Fehlermeldungs ID für falsche Feldername
	// die nicht in der DB Tabelle vorhanden
	// oder nicht erlaubt sind zu manipulieren
	const ERROR_TABLE_ATTR_DIFF = '7001';
	protected $ERROR_TABLE_ATTR_DIFF = self::ERROR_TABLE_ATTR_DIFF;
	
	
	/*
	* Preis formatieren
	* param $price
	* return string
	*/
	public function formatPrice($price){
		return number_format($price, 2, ',', ' ');
	}
}