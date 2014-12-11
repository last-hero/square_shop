<?php
/** @file SSOrderItem.php
 *  @brief SSOrderItem modellieren
 *
 *  Diese Klasse dient für das Modellieren von
 *  Checkout Daten (order, order items, delivery, billing)
 *
 *  @author Gobi Selva
 *  @author http://www.square.ch
 *  @author https://github.com/last-hero/square_shop
 */

class SSOrderItem extends SSModel{	
	/**
	 * @see SSModel::$TABLE
	 */
	const TABLE = 'order_item';
	protected $TABLE = self::TABLE;
	
	/**
	 * @see SSModel::$ERROR_TABLE_ATTR_DIFF
	 */
	const ERROR_TABLE_ATTR_DIFF = '10001';
	protected $ERROR_TABLE_ATTR_DIFF = self::ERROR_TABLE_ATTR_DIFF;
	
	/**
	 * @see SSModel::$ERROR_TO_MANY_FOREIGN_KEYS
	 */
	const ERROR_TO_MANY_FOREIGN_KEYS = '10002';
	protected $ERROR_TO_MANY_FOREIGN_KEYS;
	
	/**
	 * @see SSModel::$ERROR_NO_FOREIGN_KEYS
	 */
	const ERROR_NO_FOREIGN_KEYS = '10003';
	protected $ERROR_NO_FOREIGN_KEYS;
}