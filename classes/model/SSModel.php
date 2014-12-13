<?php
/** @file SSModel.php
 *  @brief Daten modellieren
 *
 *  Diese Klasse dient als Parent für alle Subklassen
 *  die Daten aus DB modellieren
 *
 *  @author Gobi Selva
 *  @author http://www.square.ch
 *  @author https://github.com/last-hero/square_shop
 */

class SSModel {
	/**
	 * Enthält Daten, die vom Formular via
	 * Submit betätigt wurden.
	 */
	protected $propertiesAndValues;
	
	/**
	 * Enthält alle Datenbank Felder (der Tabelle) und deren Eigenschaften,
	 * die in der SSDBSchema definiert sind.
	 */
	protected $properties;
	
	/**
	 * Datenbank Tabellenname
	 */
	protected $TABLE = null;
	
	/**
	 * Exception ID für falsche Attribute
	 * die nicht in der DB Tabelle vorhanden sind
	 * oder keinen Zugriff von dieser Klasse aus haben
	 */
	protected $ERROR_TABLE_ATTR_DIFF;
	
	/**
	 * Exception ID für zu viele ForeignKeys als erwaret
	 */
	protected $ERROR_TO_MANY_FOREIGN_KEYS;
	
	/**
	 * Exception ID für keine ForeingKeys gefunden
	 */
	protected $ERROR_NO_FOREIGN_KEYS;
	
	/** @brief Konstruktor
	 *
	 *  Initialisiert die DB Tabellenfelder
	 */
    function __construct(){
		$this->loadPropertiesAndNames();
		if(is_array($array)) $this->set($propertiesAndValues);
    }
	
	/** @brief Tabellenfelder laden
	 *
	 *  DB Tabellenfelder vom DB-Schema holen
	 */
	public function loadPropertiesAndNames(){
		//$this->properties = SSDBSchema::_getFields($this->TABLE, 'name');
		$this->properties = SSDBSchema::_getFields($this->TABLE);
		
		$this->propertiesAndValues = array();
		foreach($this->properties as $field){
			$this->propertiesAndValues[$field['name']] = null;
		}
	}
	
	/** @brief Tabellenfelder reinigen
	 *
	 *  Felder raus kicken, die nicht erlaubt oder
	 *  in der DB-Schema definiert sind.
	 *  Die bereinigten Felder zurückgeben.
	 *
	 *  @param $propertiesAndValues: Felder und Values
	 *  @return $propertiesAndValuesNew: bereinigte Felder und Values
	 */
	public function getClearedUnknownProperties($propertiesAndValues){
		$propertyNames = SSDBSchema::_getFieldsAsSingleArray($this->TABLE, array('name'));
		$propertiesAndValuesNew = array();
		foreach($propertiesAndValues as $key => $val){
			if(in_array($key, $propertyNames)){
				$propertiesAndValuesNew[$key] = $val;
			}
		}
		return $propertiesAndValuesNew;
	}
	
	/** @brief Existenzprüfung von Felder
	 *
	 *  Prüfen nach Existenz von DB Tabellenfelder
	 *
	 *  @param (string|array) $keys: Tabellenfeld(er)
	 *  @return bool: Falls 1 Feld nicht existiert false, ansonsten true
	 */
	public function doPropertiesExist($keys){
		if(is_string($keys))$keys = array($keys=>$keys);
		if(SSHelper::array_keys_exists($keys, $this->propertiesAndValues)){
			return true;
		}
		return false;
	}
	
	/** @brief Wert holen
	 *
	 *  Wert nach Propertyname holen
	 *
	 *  @param (string) $key: Tabellenfeld
	 *  @return (mixed) $value: Tabellenfeld Wert
	 */
	public function get($key){
		if($this->doPropertiesExist($key)){
			return $this->propertiesAndValues[$key];
		}
		return null;
	}
	
	/** @brief Wert setzen
	 *
	 *  Wert nach Propertyname setzen
	 *
	 *  @param (string|array) $keyOrData: Tabellenfeld(er)
	 *  @return (mixed) $val: Tabellenfeld Wert
	 */
	public function set($keyOrData, $val = ''){
		if(is_array($keyOrData)){
			$data = $keyOrData;
			if($this->doPropertiesExist($data)){
				foreach($data as $k => $v){
					$this->propertiesAndValues[$k] = $v;
				}
			}else{
				throw new SSException('Table '.$this->TABLE.' Attr is/are different', $this->ERROR_TABLE_ATTR_DIFF);
			}
		}else{
			$key = $keyOrData;
			if($this->doPropertiesExist($key)){
				$this->propertiesAndValues[$key] = $val;
			}else{
				throw new SSException('Table '.$this->TABLE.' Attr is/are different', $this->ERROR_TABLE_ATTR_DIFF);
			}
		}
	}
	
	/** @brief Speichern in DB
	 *
	 *  Alle Property-Values (dieses Objektes) in DB speichern.
	 *  Falls kein Eintrag extistiert, wird neu erstellt.
	 *
	 *  @return bool
	 */
	public function save(){
		$propertiesAndValues = $this->propertiesAndValues;
		
		// Verschlüsseln --> z.B. Passwort
		$dbProperties = SSDBSchema::_getFields($this->TABLE, null, array('sql_settings' => 'encrypt'));
		foreach($propertiesAndValues as $name => $value){
			foreach($dbProperties as $dbProperty){
				if($name == $dbProperty['name']){
					$encrypt = $dbProperty['sql_settings']['encrypt'];
					if($encrypt){
						$propertiesAndValues[$name] = hash($encrypt, $value);
					}
				}
			}
		}
		if((int)$this->get('id') > 0){
			$query = SSDBSQL::_getSqlUpdateQuery($propertiesAndValues, $this->TABLE);
			$res = SSDBSQL::executeSqlQuery($query, false);
			if((int)$res['rows'] == 1){
				return true;
			}else{
				throw new SSException('Record could not update from Table: '.$this->TABLE.'', 12001);
			}
		}else{
			unset($propertiesAndValues['id']);
			$query = SSDBSQL::_getSqlInsertQuery($propertiesAndValues, $this->TABLE);
			$res = SSDBSQL::executeSqlQuery($query, false);
			if((int)$res['last_insert_id'] > 0){
				$this->set('id', $res['last_insert_id']);
				return true;
			}else{
				throw new SSException('Record could not insert to Table: '.$this->TABLE.'', 12002);
			}
		}
		return false;
	}
	
	/** @brief Datensatz laden
	 *
	 *  Eintrag aus der DB nach ID holen
	 *  und zum Objekt zuweisen mit $this->set
	 *
	 *  @param (string) $id: PrimaryKey Value
	 *  @return bool
	 */
	public function loadById($id){
		//$query = SSDBSQL::_getSqlDmlQuery("id = $id", $this->TABLE, SSDBSchema::SHOW_IN_DETAIL);
		//$res = SSDBSQL::executeSqlQuery($query);
		
		$tableData = SSDBSchema::_getTable($this->TABLE, true);
		$table = $tableData['name'];
		
		$where = $table.".id = $id";
		
		$res = $this->_getWhere($where);
		if(count($res) == 1){
			try{
				$result = $this->getClearedUnknownProperties($res[0]);
				$this->set($result);
				return true;
			}catch(SSException $e) {
				echo $e;
			}
		}
		return false;
	}
	
	/** @brief Datensatz holen
	 *
	 *  Eintrag oder Einträge aus der DB nach ID(s) holen
	 *  und zurückgeben.
	 *
	 *  @param (string|array) $ids: PrimaryKey Value(s)
	 *  @return bool
	 *  
	 *  @see getByForeignId()
	 *  @see _getWhere()
	 */
	public function getByIds($ids){
		$tableData = SSDBSchema::_getTable($this->TABLE, true);
		$table = $tableData['name'];
		
		$ids = !is_array($ids)?array($ids):$ids;
		$where = $table.".id IN (".implode(',', $ids).")";
		
		$res = $this->_getWhere($where);
		if(count($res) > 0){
			try{
				return $res;
			}catch(SSException $e) {
				echo $e;
			}
		}
		return false;
	}
	
	/** @brief Datensatz nach FK holen
	 *
	 *  Einträge aus der DB nach ForeignID holen.
	 *  Funktioniert nur wenn ein ForeignId in
	 *  der Tabelle vorhanden ist.
	 *
	 *  @param (string) $foreignId: ForeignKey Value
	 *  @param (string) $foreignTable: Foreign Tabellenname
	 *
	 *  @return (array|bool) $res: Datensätze oder false
	 *  
	 *  @see getByIds()
	 *  @see _getWhere()
	 */
	public function getByForeignId($foreignId, $foreignTable){
		// Foreign Keys holen
		$foreignKeyNames = SSDBSchema::_getForeignKeyNamesByForeignTable($this->TABLE, $foreignTable);
		
		if(sizeof($foreignKeyNames) == 1){
			$propertyName = $foreignKeyNames[0];
		}elseif(sizeof($foreignKeyNames) > 1){
			throw new SSException('Too many Foreign Keys for Table '.$this->TABLE.'', $this->ERROR_TO_MANY_FOREIGN_KEYS);
		}else{
			throw new SSException('No Foreign Keys for Table '.$this->TABLE.'', $this->ERROR_NO_FOREIGN_KEYS);
		}
		
		$tableData = SSDBSchema::_getTable($this->TABLE, true);
		$table = $tableData['name'];
		$res = $this->_getWhere($table.".".$propertyName." = $foreignId");
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
	
	/** @brief Datensätze aus der DB holen
	 *
	 *  Mit Where-Klausel Datensätze aus der
	 *  Datenbank holen.
	 *
	 *  @param (string) $where: z.B. email = "example@domain.com"
	 *  @param (string) $show_in: DB-Tabellenfelder Filter (siehe in SSDBSchema)
	 *
	 *  @return (array) $res: Datensätze
	 *  
	 *  @see getByIds()
	 *  @see getByForeignId()
	 */
	protected function _getWhere($where, $show_in = null){
		if(!$show_in){
			$show_in = SSDBSchema::SHOW_IN_DETAIL;
		}
		$query = SSDBSQL::_getSqlDmlQuery($where, $this->TABLE, SSDBSchema::SHOW_IN_DETAIL);
		
		$res = SSDBSQL::executeSqlQuery($query, false);
		return $res['records'];
	}
}