<?php
/** @file SSCategory.php
 *  @brief Kategorien modellieren
 *
 *  Diese Klasse dient für das Modellieren von
 *  Kategorie Daten
 *
 *  @author Gobi Selva
 *  @author http://www.square.ch
 *  @author https://github.com/last-hero/square_shop
 */

class SSCategory extends SSModel{
	/**
	 * @see SSModel::$TABLE
	 */
	const TABLE = 'category';
	protected $TABLE = self::TABLE;
	
	/**
	 * @see SSModel::$ERROR_TABLE_ATTR_DIFF
	 */
	const ERROR_TABLE_ATTR_DIFF = '8001';
	protected $ERROR_TABLE_ATTR_DIFF = self::ERROR_TABLE_ATTR_DIFF;
	
	/**
	 * @see SSModel::$ERROR_TO_MANY_FOREIGN_KEYS
	 */
	const ERROR_TO_MANY_FOREIGN_KEYS = '8002';
	protected $ERROR_TO_MANY_FOREIGN_KEYS;
	
	/**
	 * @see SSModel::$ERROR_NO_FOREIGN_KEYS
	 */
	const ERROR_NO_FOREIGN_KEYS = '8003';
	protected $ERROR_NO_FOREIGN_KEYS;
	
	/** @brief Kategorien holen
	 *
	 *  Alle erfassten Kategorien holen
	 *
	 *  @return array $res
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

