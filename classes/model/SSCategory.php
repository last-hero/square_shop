<?php
class SSCategory extends SSObjectTable{
	const TABLE = 'category';
	const ERROR_TABLE_ATTR_DIFF = '8001';
	
	protected $TABLE = self::TABLE;
	protected $ERROR_TABLE_ATTR_DIFF = self::ERROR_TABLE_ATTR_DIFF;
	
	
	/*
	* Customer nach ID laden
	* param $id: Customer ID
	* return boolean
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

