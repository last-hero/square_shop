<?php
/** @file SSDBSQL.php
 *  @brief SQL Scripts erstellen und ausführen
 *
 *  Mit dieser Klasse werden die ganze SQL Scripts generiert
 *  und ausgeführt
 *
 *  @author Gobi Selva
 *  @author http://www.square.ch
 *  @author https://github.com/last-hero/square_shop
 *
 *  @bug Keine Bugs bekannt.
 */

class SSDBSQL {
	const FOREIGN_KEY		= 'FOREIGN KEY';
	const PRIMARY_KEY		= 'PRIMARY KEY';
	const ERROR_SQL_QUERY_NOT_GIVEN = 2000;
	
	/** @brief SQL Select Query generieren
	 *
	 *  Liefert SQL Query zum Selektieren 
	 *  von Tabellen und Attribute 
	 *  (Data Manipulation Language)
	 *
	 *  @param $where: Where Klausel
	 *  @param $table: Tabellenname
	 *  @param $type_show_in: siehe SSSchema
	 *  @return (string) $query: SQL Select Query
	 */
	public static function _getSqlDmlQuery($where, $table, $type_show_in){
        global $REX;
		
        if(!$where){$where = ' 1=1 ';}
		
		try{
			$_table_fullname = SSDBSchema::_getTableAttr($table, 'name', true);
		}catch(SSException $e) {
			echo $e;
		}
		
		try{
			$fields = SSDBSchema::_getFields($table, null, array('show_in'=>$type_show_in));
		}catch(SSException $e) {
			echo $e;
		}

		if(is_array($fields) and !empty($_table_fullname)){
			$_fields_string = '';
			$_fields_join_string = '';
			
			// Select Felder generieren and hand von $Fields
			foreach($fields as $fk){
				if($fk['type'] == SSDBSchema::FOREIGN_KEY){
					// Select Felder mit Joins zu in Beziehung stehende Tabelle
					$_table_join_full = SSDBSchema::_getTableAttr($fk['sql_join']['table'], 'name', true);
					$_field_join_full = $_table_join_full.'.'.$fk['sql_join']['field'];
					
					foreach($fk['sql_join']['field_labels'] as $label){
						$_field_join_label_full = $_table_join_full.'.'.$label;
						$_field_join_label_new_full = $fk['sql_join']['table'].'_'.$label;
						
						$_fields_string .= $_field_join_label_full.' as '.$_field_join_label_new_full.', ';
					}
					
					$_fields_join_string .= 'LEFT JOIN '.$_table_join_full
												.' ON '.$_field_join_full.' = '.$_table_fullname.'.'.$fk['name'];
				}else{
					$_fields_string .= $_table_fullname.'.'.$fk['name'].', ';
				}
			}
			$_fields_string = substr($_fields_string, 0, -2);
			
			$query = '
				SELECT '.$_fields_string.'
				FROM '.$_table_fullname.'
					 '.$_fields_join_string.'
				WHERE '.$where;
			return $query;
		}
		return '';
    }
	
	/** @brief SQL Insert Query generieren
	 *
	 *  Liefert SQL Query zum Hinzufügen 
	 *  von Datensätzen
	 *  (Data Manipulation Language)
	 *
	 *  @param $attrAndValues: Attribute und Values
	 *  @param $table: Tabellenname
	 *  @return (string) $query: SQL Insert Query
	 */
	public static function _getSqlInsertQuery($attrAndValues, $table){
        global $REX;
		
		try{
			$_table_fullname = SSDBSchema::_getTableAttr($table, 'name', true);
		}catch(SSException $e) {
			echo $e;
		}
		
		try{
			$fields = SSDBSchema::_getFieldsAsSingleArray($table, array('name'));
		}catch(SSException $e) {
			echo $e;
		}
		
		$attrAndValues['createdate'] = time();
		$attrAndValues['updatedate'] = time();
		
		if(is_array($attrAndValues) and !empty($_table_fullname)
		and SSHelper::array_keys_exists($attrAndValues, array_flip($fields))){
			$_sql_sets = '';
			foreach($attrAndValues as $key => $val){
				$_sql_sets .= $key.' = "'.$val.'", ';
			}
			$_sql_sets = substr($_sql_sets, 0, -2);
			$query = '
				INSERT INTO '.$_table_fullname.'
				SET '.$_sql_sets.'
				';
			return $query;
		}
		return '';
    }
	
	/** @brief SQL Create Query generieren
	 *
	 *  Liefert SQL Query zum Erstellen 
	 *  von Tabellen und Attribute
	 *  (Data Definition Language)
	 *
	 *  @return (string) $query: SQL Create Query
	 */
	public static function _getSqlCreateTables(){
		//$fields = SSDBSchema::_getFields(null, null, array('show_in'=>$type_show_in));
		$tables = SSDBSchema::_getTables();
		$structure = array();
		foreach($tables as $table){
			try{
				$structure[$table['name']] = SSDBSchema::_getFields($table['name']);
			}catch(SSException $e) {
				echo $e;
			}
		}
		
		
		$query = '';
		$query .= "SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0; ";
		$query .= "SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0; ";
		$query .= "SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES'; ";


		foreach($structure as $tk => $tv){
			try{
				$q = '';
				$q .= 'CREATE TABLE IF NOT EXISTS '.SSDBSchema::_getTableAttr($tk, 'name', true).'(';
				$q_l = '';
				foreach($tv as $field){
					$q .= $field['name'].' '.$field['sql'].', ';
					
					if(isset($field['type']) and $field['type'] == SSDBSchema::PRIMARY_KEY){
						$q_l .= 'PRIMARY KEY ('.$field['name'].'), ';
					}elseif(isset($field['type']) and $field['type'] == SSDBSchema::FOREIGN_KEY){
						$fk_table = SSDBSchema::_getTableAttr($field['sql_join']['table'], 'name', true);
						$fk_field = $field['sql_join']['field'];
						$fk_on_del = $field['sql_join']['on_delete'];
						$fk_on_upt = $field['sql_join']['on_update'];
						$q_l .= 'CONSTRAINT '.$field['name'].'
									FOREIGN KEY ('.$field['name'].')
									REFERENCES '.$fk_table.' ('.$fk_field.')
									ON DELETE '.$fk_on_del.'
									ON UPDATE '.$fk_on_upt.', ';
					}
				}
				$q .= $q_l;
				$q = substr($q, 0, -2);
				$q .= ') ENGINE = InnoDB; ';
			}catch(SSException $e) {
				echo $e;
			}
			$query .= $q;
		}
		
		$query .= "SET SQL_MODE=@OLD_SQL_MODE; ";
		$query .= "SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS; ";
		$query .= "SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS; ";
		
		return $query;
	}
	
	/** @brief SQL Delete Query generieren
	 *
	 *  Liefert SQL Query zum Löschen 
	 *  von Tabellen und Attribute
	 *  (Data Manipulation Language)
	 *
	 *  @return (string) $query: SQL Delete Query
	 */
	public static function _getSqlDeleteTables(){
		$tables = SSDBSchema::_getTables();
		$structure = array();
		foreach($tables as $table){
			try{
				$structure[$table['name']] = SSDBSchema::_getFields($table['name']);
			}catch(SSException $e) {
				echo $e;
			}
		}
		
		$query = '';
		try{
			$delete = '';
			$drop = '';
			foreach($structure as $tk => $tv){
				$delete .= 'DELETE FROM '.SSDBSchema::_getTableAttr($tk, 'name', true).'; ';
				$drop .= 'DROP TABLE IF EXISTS '.SSDBSchema::_getTableAttr($tk, 'name', true).'; ';
			}
			$query = $delete.$drop;
		}catch(SSException $e) {
			echo $e;
		}
		return $query;
	}
	
	
	/**
	* Führt einen SQL Script mittels rex_sql aus
	* param $query
	* param $debug
	* @return array <- Results als Array
	*/
	
	/** @brief SQL Query ausführen
	 *
	 *  Führt einen SQL Script mittels rex_sql aus
	 *
	 *  @param $query: SQL Query
	 *  @param $debug: Fehlermeldungen anzeigen oder nicht
	 *  @return (array) $res: DB Results
	 */
	public static function executeSql($query, $debug=false){
		if(is_string($query)){
			$q = explode(';', $query);
		}elseif(is_array($query)){
			$q = $query;
		}
		
		
		$res = array();
		
		if(is_array($q)){
			$sql = new rex_sql();
			$sql->setDebug($debug);
			
			for($i=0; $i<sizeof($q); $i++){
				if(strlen(trim($q[$i]))){
					$sql->setQuery($q[$i]);
				}
			}
			
			$res = array();
			for($i = 0; $i < $sql->getRows(); $i++){
				$res[] = $sql->getRow();
				$sql->next();
			}
			return $res;
		}
		throw new SSException('SQL Query not given', self::ERROR_SQL_QUERY_NOT_GIVEN);
		
		return $res;
	}
}

