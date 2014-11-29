<?php
class SSDBSchema {
	const FOREIGN_KEY		= 'FOREIGN KEY';
	const PRIMARY_KEY		= 'PRIMARY KEY';
	const SHOW_IN_DETAIL	 = 'detail';
	const SHOW_IN_LIST	   = 'list';
	const SHOW_IN_ADD		= 'add';
	const SHOW_IN_EDIT	   = 'edit';
	const SHOW_IN_REGISTER = 'register';
	
	const ERROR_FIELDS_NOT_FOUND = 1000;
	const ERROR_TABLE_NOT_FOUND = 1001;
	const ERROR_TABLE_ATTR_NOT_FOUND = 1003;
	
	
	
	public static function _getFieldsAsSingleArray($table, array $attributes=null, $filter=null){
		$properties = self::_getFields($table, $attributes, $filter);
		if(is_array($attributes) and count($attributes) == 1){
			$_propertiesNew = array();
			for($x=0; $x<sizeof($properties); $x++){
				$_propertiesNew[$x] = $properties[$x]['name'];
			}
			return $_propertiesNew;
		}
		return $properties;
	}
	/**
	* Enthält alle Felder
	* param $attributes --> es werden nur diese attribute zurückgegeben
	* param $filter: filter nach value z.B. $filter=array('show_in'=>'list') liefer
	*				alle die list element in show_in array enthalten
	* @return array
	* 
	* TODO: Indexes für Datenbankfelder erstellen (Performance-Steigerung bei der Suche)
	*/
	//public static function _getFields($table, $attributes=null, $filter=null){
	public static function _getFields($table, array $attributes=null, $filter=null){
		if($table == 'article' or $table == 'square_shop_article'){
			$_fields = array(
				array(
					'name' => 'id' // Feldname
					, 'sql' => 'INT UNSIGNED NOT NULL AUTO_INCREMENT' // SQL-Script
					, 'type' => self::PRIMARY_KEY // Feld-Typ [Foreign-Key|Primary-Key]
					, 'show_in' => array('detail', 'list', 'search') // Woüberall soll dieses Feld angezeigt werden
				)
				, array(
					'name' => 'cat_id'
					, 'input' => 'select_sql'
					, 'sql' => 'INT UNSIGNED NOT NULL'
					, 'sql_index' => true
					, 'type' => self::FOREIGN_KEY
					, 'sql_join' => array(
							'table' => 'category'
							, 'field' => 'id'
							, 'field_label' => 'title'
							, 'field_labels' => array('id', 'title')
							, 'on_delete' => 'RESTRICT'
							, 'on_update' => 'CASCADE'
						)
					, 'show_in' => array('detail', 'list', 'search', 'edit', 'add')
				)
				, array(
					'name' => 'no'
					, 'input' => 'text'
					, 'sql' => 'VARCHAR(45) NULL'
					, 'show_in' => array('detail', 'list', 'search', 'edit', 'add')
				)
				, array(
					'name' => 'title'
					, 'input' => 'text'
					, 'sql' => 'VARCHAR(60) NULL'
					, 'show_in' => array('detail', 'list', 'search', 'edit', 'add')
					, 'multilang' => true
				)
				, array(
					'name' => 'description'
					, 'input' => 'textarea'
					, 'sql' => 'TEXT NULL'
					, 'show_in' => array('detail', 'search', 'edit', 'add')
					, 'multilang' => true
				)
				, array(
					'name' => 'price'
					, 'input' => 'text'
					, 'show_in' => array('detail', 'list', 'search', 'edit', 'add')
					, 'sql' => 'DECIMAL(10,2) NULL DEFAULT 0'
				)
				, array(
					'name' => 'images'
					, 'input' => 'media'
					, 'sql' => 'VARCHAR(255) NULL'
					, 'show_in' => array('detail', 'edit', 'add')
				)
				, array(
					'name' => 'status'
					, 'sql' => 'TINYINT(1) NULL DEFAULT 1'
					, 'show_in' => array('detail', 'edit', 'add')
				)
				, array(
					'name' => 'createdate'
					, 'sql' => 'INT(11)'
					, 'show_in' => array('detail')
				)
				, array(
					'name' => 'updatedate'
					, 'sql' => 'INT(11)'
					, 'show_in' => array('detail')
				)
			);
		}elseif($table == 'category' or $table == 'square_shop_category'){
			$_fields = array(
				array(
					'name' => 'id'
					, 'sql' => 'INT UNSIGNED NOT NULL AUTO_INCREMENT'
					, 'type' => self::PRIMARY_KEY
					, 'show_in' => array('detail', 'list', 'search')
				)
				, array(
					'name' => 'title'
					, 'input' => 'text'
					, 'sql' => 'VARCHAR(60) NULL'
					, 'show_in' => array('detail', 'list', 'search', 'edit', 'add')
					, 'multilang' => true
				)
				, array(
					'name' => 'status'
					, 'input' => 'checkbox'
					, 'sql' => 'TINYINT(1) NULL DEFAULT 1'
					, 'show_in' => array('detail', 'edit', 'add')
				)
				, array(
					'name' => 'createdate'
					, 'sql' => 'INT(11)'
					, 'show_in' => array('detail')
				)
				, array(
					'name' => 'updatedate'
					, 'sql' => 'INT(11)'
					, 'show_in' => array('detail')
				)
			);
		}elseif($table == 'customer' or $table == 'square_shop_customer'){
			$_fields = array(
				array(
					'name' => 'id'
					, 'sql' => 'INT UNSIGNED NOT NULL AUTO_INCREMENT'
					, 'type' => self::PRIMARY_KEY
					, 'show_in' => array('detail', 'list', 'search')
				)
				, array(
					'name' => 'title'
					, 'input' => 'select'
					, 'input_settings' => array(
							'values' => array('m','w')
							, 'required' => true
						)
					, 'sql' => 'VARCHAR(20) NULL'
					, 'sql_constraint_vals' => array('m','w')
					, 'show_in' => array('detail', 'list', 'search', 'edit', 'add', 'register')
					, 'multilang' => true
				)
				, array(
					'name' => 'company'
					, 'input' => 'text'
					, 'input_settings' => array(
							'required' => false
							, 'min' => 3
							, 'max' => 60
						)
					, 'sql' => 'VARCHAR(60) NULL'
					, 'show_in' => array('detail', 'edit', 'search', 'add', 'register')
				)
				, array(
					'name' => 'firstname'
					, 'input' => 'text'
					, 'input_settings' => array(
							'required' => true
							, 'min' => 2
							, 'max' => 60
						)
					, 'sql' => 'VARCHAR(60) NULL'
					, 'show_in' => array('detail', 'list', 'search', 'edit', 'add', 'register')
				)
				, array(
					'name' => 'lastname'
					, 'input' => 'text'
					, 'input_settings' => array(
							'required' => true
							, 'min' => 2
							, 'max' => 60
						)
					, 'sql' => 'VARCHAR(60) NULL DEFAULT 0'
					, 'show_in' => array('detail', 'list', 'search', 'edit', 'add', 'register')
				)
				, array(
					'name' => 'street'
					, 'input' => 'text'
					, 'input_settings' => array(
							'required' => true
							, 'min' => 3
							, 'max' => 60
						)
					, 'sql' => 'VARCHAR(60) NULL'
					, 'show_in' => array('detail', 'edit', 'search', 'add', 'register')
				)
				, array(
					'name' => 'zip'
					, 'input' => 'text'
					, 'input_settings' => array(
							'required' => true
							, 'min' => 3
							, 'max' => 20
							//, 'type' => 'int'
						)
					, 'sql' => 'VARCHAR(20) NULL'
					, 'show_in' => array('detail', 'edit', 'search', 'add', 'register')
				)
				, array(
					'name' => 'city'
					, 'input' => 'text'
					, 'input_settings' => array(
							'required' => true
							, 'min' => 3
							, 'max' => 60
						)
					, 'sql' => 'VARCHAR(60) NULL'
					, 'show_in' => array('detail', 'edit', 'search', 'add', 'register')
				)
				, array(
					'name' => 'telephone'
					, 'input' => 'text'
					, 'input_settings' => array(
							'required' => true
							, 'min' => 3
							, 'max' => 60
						)
					, 'sql' => 'VARCHAR(60) NULL'
					, 'show_in' => array('detail', 'list', 'search', 'edit', 'add', 'register')
				)
				, array(
					'name' => 'email'
					, 'input' => 'text'
					, 'input_settings' => array(
							'required' => true
							, 'type' => 'email'
							, 'max' => 90
						)
					, 'sql' => 'VARCHAR(90) NULL'
					, 'show_in' => array('detail', 'list', 'search', 'edit', 'add', 'register')
				)
				, array(
					'name' => 'password'
					, 'input' => 'password'
					, 'input_settings' => array(
							'required' => true
							, 'type' => 'password'
							, 'min' => 6
							, 'max' => 16
							, 'encrypt' => 'md5'
						)
					, 'sql' => 'VARCHAR(32) NULL'
					, 'sql_settings' => array(
							'encrypt' => 'md5'
						)
					, 'show_in' => array('empty', 'register', 'list')
				)
				, array(
					'name' => 'status'
					, 'input' => 'checkbox'
					, 'sql' => 'TINYINT(1) NULL DEFAULT 1'
					, 'show_in' => array('detail', 'edit', 'add')
				)
				, array(
					'name' => 'createdate'
					, 'sql' => 'INT(11)'
					, 'show_in' => array('detail')
				)
				, array(
					'name' => 'updatedate'
					, 'sql' => 'INT(11)'
					, 'show_in' => array('detail')
				)
			);
		}elseif($table == 'order' or $table == 'square_shop_order'){
			$_fields = array(
				array(
					'name' => 'id'
					, 'sql' => 'INT UNSIGNED NOT NULL AUTO_INCREMENT'
					, 'type' => self::PRIMARY_KEY
					, 'show_in' => array('detail', 'list', 'search')
				)
				, array(
					'name' => 'customer_id'
					, 'input' => 'select_sql'
					, 'sql' => 'INT UNSIGNED NOT NULL'
					, 'type' => self::FOREIGN_KEY
					, 'sql_join' => array(
							'table' => 'customer'
							, 'field' => 'id'
							, 'field_labels' => array('id', 'title', 'firstname', 'lastname')
							, 'on_delete' => 'RESTRICT'
							, 'on_update' => 'CASCADE'
						)
					, 'show_in' => array('detail', 'list', 'search')
				)
				, array(
					'name' => 'no'
					, 'sql' => 'VARCHAR(45) NULL'
					, 'show_in' => array('detail', 'list')
				)
				, array(
					'name' => 'date'
					, 'sql' => 'DATETIME NULL'
					, 'show_in' => array('detail')
				)
				, array(
					'name' => 'billing_title'
					, 'sql' => 'VARCHAR(20) NULL'
					, 'show_in' => array('detail')
				)
				, array(
					'name' => 'billing_company'
					, 'input' => 'text'
					, 'sql' => 'VARCHAR(20) NULL'
					, 'show_in' => array('detail')
				)
				, array(
					'name' => 'billing_firstname'
					, 'sql' => 'VARCHAR(60) NULL'
					, 'show_in' => array('detail')
				)
				, array(
					'name' => 'billing_lastname'
					, 'sql' => 'VARCHAR(60) NULL DEFAULT 0'
					, 'show_in' => array('detail')
				)
				, array(
					'name' => 'billing_street'
					, 'sql' => 'VARCHAR(60) NULL'
					, 'show_in' => array('detail')
				)
				, array(
					'name' => 'billing_zip'
					, 'sql' => 'VARCHAR(20) NULL'
					, 'show_in' => array('detail')
				)
				, array(
					'name' => 'billing_city'
					, 'sql' => 'VARCHAR(60) NULL'
					, 'show_in' => array('detail')
				)
				, array(
					'name' => 'billing_telephone'
					, 'sql' => 'VARCHAR(60) NULL'
					, 'show_in' => array('detail')
				)
				, array(
					'name' => 'billing_email'
					, 'sql' => 'VARCHAR(90) NULL'
					, 'show_in' => array('detail')
				)
				, array(
					'name' => 'delivery_title'
					, 'sql' => 'VARCHAR(20) NULL'
					, 'show_in' => array('detail')
				)
				, array(
					'name' => 'delivery_company'
					, 'input' => 'text'
					, 'sql' => 'VARCHAR(20) NULL'
					, 'show_in' => array('detail')
				)
				, array(
					'name' => 'delivery_firstname'
					, 'sql' => 'VARCHAR(60) NULL'
					, 'show_in' => array('detail')
				)
				, array(
					'name' => 'delivery_lastname'
					, 'sql' => 'VARCHAR(60) NULL DEFAULT 0'
					, 'show_in' => array('detail')
				)
				, array(
					'name' => 'delivery_street'
					, 'sql' => 'VARCHAR(60) NULL'
					, 'show_in' => array('detail')
				)
				, array(
					'name' => 'delivery_zip'
					, 'sql' => 'VARCHAR(20) NULL'
					, 'show_in' => array('detail')
				)
				, array(
					'name' => 'delivery_city'
					, 'sql' => 'VARCHAR(60) NULL'
					, 'show_in' => array('detail')
				)
				, array(
					'name' => 'delivery_telephone'
					, 'sql' => 'VARCHAR(60) NULL'
					, 'show_in' => array('detail')
				)
				, array(
					'name' => 'delivery_email'
					, 'sql' => 'VARCHAR(90) NULL'
					, 'show_in' => array('detail')
				)
				, array(
					'name' => 'status'
					, 'sql' => 'TINYINT(1) NULL DEFAULT 1'
					, 'show_in' => array('detail')
				)
				, array(
					'name' => 'createdate'
					, 'sql' => 'INT(11)'
					, 'show_in' => array('detail')
				)
				, array(
					'name' => 'updatedate'
					, 'sql' => 'INT(11)'
					, 'show_in' => array('detail')
				)
			);
		}elseif($table == 'order_item' or $table == 'square_shop_order_item'){
			$_fields = array(
				array(
					'name' => 'id'
					, 'sql' => 'INT UNSIGNED NOT NULL AUTO_INCREMENT'
					, 'type' => self::PRIMARY_KEY
					, 'show_in' => array('detail', 'list', 'search')
				)
				, array(
					'name' => 'order_id'
					, 'sql' => 'INT UNSIGNED NOT NULL'
					, 'type' => self::FOREIGN_KEY
					, 'sql_join' => array(
							'table' => 'order'
							, 'field' => 'id'
							, 'on_delete' => 'RESTRICT'
							, 'on_update' => 'CASCADE'
						)
					, 'show_in' => array('detail')
				)
				, array(
					'name' => 'no'
					, 'sql' => 'VARCHAR(45) NULL'
					, 'show_in' => array('detail', 'list', 'search', 'edit', 'add')
				)
				, array(
					'name' => 'title'
					, 'sql' => 'VARCHAR(60) NULL'
					, 'show_in' => array('detail', 'list', 'search', 'edit', 'add')
					, 'multilang' => true
				)
				, array(
					'name' => 'description'
					, 'sql' => 'TEXT NULL'
					, 'show_in' => array('search', 'edit', 'add')
					, 'multilang' => true
				)
				, array(
					'name' => 'price'
					, 'show_in' => array('detail', 'list', 'search', 'edit', 'add')
					, 'sql' => 'DECIMAL(10,2) NULL DEFAULT 0'
				)
				, array(
					'name' => 'images'
					, 'sql' => 'VARCHAR(255) NULL'
					, 'show_in' => array('edit', 'add')
				)
				, array(
					'name' => 'qty'
					, 'sql' => 'INT NULL DEFAULT 0'
					, 'show_in' => array('detail')
				)
				, array(
					'name' => 'status'
					, 'sql' => 'TINYINT(1) NULL DEFAULT 1'
					, 'show_in' => array('empty')
				)
				, array(
					'name' => 'createdate'
					, 'sql' => 'INT(11)'
					, 'show_in' => array('empty')
				)
				, array(
					'name' => 'updatedate'
					, 'sql' => 'INT(11)'
					, 'show_in' => array('empty')
				)
			);
		}
		$fields = $_fields;
		if (!is_array($fields)) {
			throw new SSException('No Fields for this Table defined', self::ERROR_FIELDS_NOT_FOUND);
		}
		
		if(is_array($fields)){
			if(is_array($filter)){
				$fields_new = $fields;
				for($i=0; $i<sizeof($fields); $i++){
					foreach($filter as $fk => $fv){
						if(is_array($fv)){
							// to do
						}elseif(is_array($fields[$i][$fk])){
							if(!in_array($fv, $fields[$i][$fk]) and !array_key_exists($fv, $fields[$i][$fk])){
								unset($fields_new[$i]);
							}
						}elseif($fields[$i][$fk] != $fv){
							unset($fields_new[$i]);
						}
					}
				}
				$fields = array_merge($fields_new);
				unset($fields_new);
			}
			if(is_array($attributes)){
				$fields_new = $fields;
				for($i=0; $i<sizeof($fields); $i++){
					foreach($fields[$i] as $fk => $vk){
						if(!in_array($fk, $attributes)){
							unset($fields_new[$i][$fk]);
						}
					}
				}
				$fields = array_merge($fields_new);
				unset($fields_new);
			}
		}
		return $fields;	
	}
	
	/**
	* Enthält alle Tabellen
	* param $table tabellenname (shortform)
	* param $prefix: mit oder ohne Redaxo-Prefix für Tabellenname
	* @return array
	*/
	public static function _getTables($prefix=false){
		global $REX;
		$tables = array(
					array(
						'name' => 'square_shop_article'
						, 'labels' => array('article')
						, 'show_in' => array('list', 'edit', 'add')
					)
					, array(
						'name' => 'square_shop_category'
						, 'labels' => array('category')
						, 'show_in' => array('list', 'edit', 'add')
					)
					, array(
						'name' => 'square_shop_order_item'
						, 'labels' => array('order_item')
						, 'show_in' => array('detail')
					)
					, array(
						'name' => 'square_shop_order'
						, 'labels' => array('order')
						, 'show_in' => array('list', 'detail')
					)
					, array(
						'name' => 'square_shop_customer'
						, 'labels' => array('customer')
						, 'show_in' => array('list', 'edit', 'add')
					)
				);
		for($i=0; $i<sizeof($tables); $i++){
			$tables[$i]['labels'][] = $tables[$i]['name'];
		}
		$ret_val = $tables;
		if($prefix){
			for($i=0; $i<sizeof($ret_val); $i++){
				$ret_val[$i]['name'] = $REX['TABLE_PREFIX'].$ret_val[$i]['name'];
			}
		}
		return $ret_val;
	}
	
	/**
	* param $table tabellenname (shortform)
	* param $prefix: mit oder ohne Redaxo-Prefix
	* @return string -> liefert eine einzige tabelle
	*/
	public static function _getTable($table, $prefix=false){
		$tables = self::_getTables($prefix);
		if(is_array($tables) and sizeof($tables)>0){
			for($i=0; $i<sizeof($tables); $i++){
				if(in_array($table, $tables[$i]['labels'])){
					return $tables[$i];
				}
			}
		}
		
		throw new SSException('Table not found', self::ERROR_TABLE_NOT_FOUND);
		
		return false;
	}
	
	/**
	* param $table tabellenname (shortform)
	* param $prefix: mit oder ohne Redaxo-Prefix
	* @return string -> liefert eine einzige tabelle
	*/
	public static function _getTableAttr($table, $attr, $prefix=false){
		$_table = self::_getTable($table, $prefix);
		if(is_array($_table) and isset($_table[$attr])){
			return $_table[$attr];
		}
		
		throw new SSException('Attribute for Table not found', self::ERROR_TABLE_ATTR_NOT_FOUND);
		
		return false;
	}
}

