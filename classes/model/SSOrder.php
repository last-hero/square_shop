<?php
/** @file SSOrder.php
 *  @brief SSOrder modellieren
 *
 *  Diese Klasse dient für das Modellieren von
 *  Checkout Daten (order, order items, delivery, billing)
 *
 *  @author Gobi Selva
 *  @author http://www.square.ch
 *  @author https://github.com/last-hero/square_shop
 */

class SSOrder extends SSModel{	
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
	
	
	public static function convertCustomerAddrToDeliveryAddr($customerAddress){
		return self::convertCustomerAddrToBillOrDeliverAddr(
			$customerAddress
			, SSDBSchema::SHOW_IN_DELIVER_ADDRESS
		);
	}
	public static function convertCustomerAddrToBillingAddr($customerAddress){
		return self::convertCustomerAddrToBillOrDeliverAddr(
			$customerAddress
			, SSDBSchema::SHOW_IN_BILL_ADDRESS
		);
	}
	
	public static function convertCustomerAddrToBillOrDeliverAddr($customerAddress, $addressType){
		$address = array();
		$properties = SSDBSchema::_getFieldsAsSingleArray(
			SSOrder::TABLE
			, null
			, array('show_in'=>$addressType)
		);
		foreach($properties as $prob){
			$label = isset($prob['input_settings']['label']) ? 
						$prob['input_settings']['label'] : $prob['name'];
			if(isset($customerAddress[$label])){
				$address[$prob['name']] = $customerAddress[$label];
			}
		}
		return $address;
	}
	
	public static function convertBillAddrToDeliverAddr($billAddress){
		$deliverAddress = array();
		$properties = SSDBSchema::_getFieldsAsSingleArray(
			SSOrder::TABLE
			, array('name')
			, array('show_in'=>SSDBSchema::SHOW_IN_DELIVER_ADDRESS)
		);
		foreach($properties as $prob){
			
			if(isset($customerAddress[$label])){
				$address[$prob['name']] = $customerAddress[$label];
			}
			$deliverAddress[$prob['name']] = $billAddress[$prob['name']];
		}
		return $deliverAddress;
	}
}