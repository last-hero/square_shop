<?php
/** @file SSArticle.php
 *  @brief Artikeln modellieren
 *
 *  Diese Klasse dient für das Modellieren von
 *  Artikeln
 *
 *  @author Gobi Selva
 *  @author http://www.square.ch
 *  @author https://github.com/last-hero/square_shop
 *
 *  @bug Keine Bugs bekannt.
 */

class SSArticle extends SSModel{
	/**
	 * @see SSModel::$TABLE
	 */
	const TABLE = 'article';
	protected $TABLE = self::TABLE;
	
	/**
	 * @see SSModel::$ERROR_TABLE_ATTR_DIFF
	 */
	const ERROR_TABLE_ATTR_DIFF = '7001';
	protected $ERROR_TABLE_ATTR_DIFF = self::ERROR_TABLE_ATTR_DIFF;
	
	/**
	 * @see SSModel::$ERROR_TO_MANY_FOREIGN_KEYS
	 */
	const ERROR_TO_MANY_FOREIGN_KEYS = '7002';
	protected $ERROR_TO_MANY_FOREIGN_KEYS;
	
	/**
	 * @see SSModel::$ERROR_NO_FOREIGN_KEYS
	 */
	const ERROR_NO_FOREIGN_KEYS = '7003';
	protected $ERROR_NO_FOREIGN_KEYS;
	
	
	/*
	* Preis formatieren
	* param $price
	* return string
	*/
	
	/** @brief Preis formatieren
	 *
	 *  Preis nach Schweizernorm formatieren
	 *  z.b. 29,90
	 *
	 *  @param string $price
	 *  @return string $price
	 */
	public function formatPrice($price){
		return sprintf('%0.2f', $price);
		//sprintf ("%03d\n", 26);		
		//return number_format($price, 2, ',', ' ');
	}
}