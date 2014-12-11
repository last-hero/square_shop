<?php
/** @file SSCheckout.php
 *  @brief Chekout modellieren
 *
 *  Diese Klasse dient für das Modellieren von
 *  Checkout Daten (order, order items, delivery, billing)
 *
 *  @author Gobi Selva
 *  @author http://www.square.ch
 *  @author https://github.com/last-hero/square_shop
 */

class SSCheckout extends SSModel{
	/**
	 * Tabelle Order Items
	 * 
	 * @see SSSchema
	 */
	const TABLE_ORDER_ITEM = 'order_item';
	
	/**
	 * @see SSModel::$TABLE
	 */
	const TABLE = 'order';
	protected $TABLE = self::TABLE;
	
	/**
	 * @see SSModel::$ERROR_TABLE_ATTR_DIFF
	 */
	const ERROR_TABLE_ATTR_DIFF = '9001';
	protected $ERROR_TABLE_ATTR_DIFF = self::ERROR_TABLE_ATTR_DIFF;
	
	/**
	 * @see SSModel::$ERROR_TO_MANY_FOREIGN_KEYS
	 */
	const ERROR_TO_MANY_FOREIGN_KEYS = '9002';
	protected $ERROR_TO_MANY_FOREIGN_KEYS;
	
	/**
	 * @see SSModel::$ERROR_NO_FOREIGN_KEYS
	 */
	const ERROR_NO_FOREIGN_KEYS = '9003';
	protected $ERROR_NO_FOREIGN_KEYS;
}