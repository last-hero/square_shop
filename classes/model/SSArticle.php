<?php
class SSArticle extends SSObjectTable{
	const TABLE = 'article';
	const ERROR_TABLE_ATTR_DIFF = '7001';
	
	protected $TABLE = self::TABLE;
	protected $ERROR_TABLE_ATTR_DIFF = self::ERROR_TABLE_ATTR_DIFF;
	
	
	
	/*
	* Customer nach ID laden
	* param $id: Customer ID
	* return boolean
	*/
	public function getByCategoryId($id){
		//$query = SSDBSQL::_getSqlDmlQuery("id = $id", $this->TABLE, SSDBSchema::SHOW_IN_DETAIL);
		//$res = SSDBSQL::executeSql($query);
		$tablePropertyNames = SSDBSchema::_getFieldsAsSingleArray($this->TABLE, array('name'), array('sql_join' => 'table'));
		if(sizeof($tablePropertyNames) == 1){
			$propertyName = $tablePropertyNames[0];
		}else{
			return false;	
		}
		$tableData = SSDBSchema::_getTable($this->TABLE, true);
		$table = $tableData['name'];
		$res = $this->_getWhere($table.".".$propertyName." = $id");
		if(count($res) > 0){
			try{
				$result = $this->getClearedUnknownProperties($res[0]);
				return $res;
			}catch(SSException $e) {
				echo $e;
			}
		}
		return false;
	}
}

