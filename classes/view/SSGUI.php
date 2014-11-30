<?php
class SSGUI {
	const ERROR_TEMPLATE_R_NOT_FOUND = 3000;
	
	/**
	* Zeigt Detailansicht der gewünschten Eintrags an.
	* param $table: Tabellenname
	* param $id: DB-EintragsID
	*/
	public static function displayDetail($table, $id){
		global $REX;
		
		try{			
			echo self::parse($table.'.detail.tmpl.php', 
				array(
					'id' => $id
					, 'id' => $id
				)
			);
		}catch(SSException $e) {
			echo $e;
		}
	}
	
	/**
	* Gibt die gesamte Tabelle in Form von rex_list aus
	* param $table: Tabellenname
	* param $filter_key: Felder in welche gesucht werden soll
	* param $filter_value: Suchstring zum Filtern der Lister
	*/
	public static function displayList($table, $filter_key=null, $filter_value=null){
		// Filtern nach Suchstring --> Where Bedingung erstellen
        if(empty($filter_key)){
			$filter_key	= rex_post('search_fields', 'array', '');
        }
        if(empty($filter_value)){
			$filter_value	= rex_post('search', 'string', '');
        }
		
		
		$_table = SSDBSchema::_getTable($table, true);
		$_table_fullname = SSDBSchema::_getTableAttr($table, 'name', true);	
		$sql_where = '';
        if(is_string($filter_value) and strlen($filter_value)>0 and is_array($filter_key) and count($filter_key)){
			$sql_where .= '(1=10 ';
			for($i=0; $i<sizeof($filter_key); $i++){
				$sql_where .= ' OR '.$_table_fullname.'.'.$filter_key[$i].' LIKE "%'.$filter_value.'%" ';
			}
			$sql_where .= ')';
        }
		
		$query = SSDBSQL::_getSqlDmlQuery($sql_where, $table, 'list');
		
		$list = rex_list::factory($query);
		
		$imgHeaderLinkAdd = '<a href="'. $list->getUrl(array('func' => 'add')) .'"><img src="media/metainfo_plus.gif" alt="add" title="add" /></a>';
		$imgHeader = '<img src="media/metainfo_plus.gif" alt="add" title="add" />';
		$imgMetaInfo = '<img src="media/metainfo.gif" alt="field" title="field" />';
		
		if(in_array('edit', $_table['show_in']) or in_array('add', $_table['show_in'])){
			$list->addColumn(
				$imgHeaderLinkAdd, $imgMetaInfo, 0, array( '<th class="rex-icon">###VALUE###</th>', '<td class="rex-icon">###VALUE###</td>' )
			);
			$list->setColumnParams($imgHeaderLinkAdd,array('func' => 'edit', 'id' => '###id###'));
			$list->setColumnLabel('id', ss_utils::i18l('label_id'));
		}
		if(in_array('detail', $_table['show_in'])){
			$list->addColumn(
				$imgHeader, $imgMetaInfo, 0, array( '<th class="rex-icon">###VALUE###</th>', '<td class="rex-icon">###VALUE###</td>' )
			);
			$list->setColumnParams($imgHeader,array('func' => 'detail', 'table' => $table, 'id' => '###id###'));
			$list->setColumnLabel('id', ss_utils::i18l('label_id'));
		}
		
		try{
			$fields = SSDBSchema::_getFields($table, null, array('show_in'=>'list'));
		}catch(SSException $e) {
			echo $e;
		}
		foreach($fields as $fk){
			$label = $fk['name'];
			$list->setColumnLabel($label, ss_utils::i18l('label_'.$label));
			$list->setColumnSortable($label);
			if(isset($fk['sql_join']['field_labels'])){
				foreach($fk['sql_join']['field_labels'] as $f){
					$label = $fk['sql_join']['table'].'_'.$f;
					$list->setColumnLabel($label, ss_utils::i18l('label_'.$label));
					$list->setColumnSortable($label);
				}
			}
		}
		
		$list->show();
	}
	
	/**
	* Editier bzw. Neuerfassen Formular in Form von rex_form
	* param $table: Tabellenname
	* param $id: ID, der zu editierenden Eintrag
	*/
	public static function displayFormAddEdit($table, $id=null){
		$mode_type = 'add';
		$whereCondition='1=10';
		if(!empty($id)){
			$whereCondition='id='.$id;
			$mode_type = 'edit';
		}
		$form = rex_form::factory($tableName=SSDBSchema::_getTableAttr($table, 'name', true), $fieldset='Allgemein', $whereCondition);
		
		try{
			$fields = SSDBSchema::_getFields($table, null, array('show_in'=>$mode_type));
		}catch(SSException $e) {
			echo $e;
		}
		
		foreach($fields as $f){
			$name = $f['name'];
			if(isset($f['input'])){
				switch ($f['input']){
					case 'text':
						$field = &$form->addTextField($name);
						$field->setLabel(ss_utils::i18l('label_'.$name));
						break;
					case 'textarea':
						$field = &$form->addTextAreaField($name);
						$field->setLabel(ss_utils::i18l('label_'.$name));
						break;
					case 'media':
						$field = &$form->addMedialistField($name);
						$field->setLabel(ss_utils::i18l('label_'.$name));
						break;
					case 'select_sql':
						$field = &$form->addSelectField($name);
						$field->setLabel(ss_utils::i18l('label_'.$name));
						$select = &$field->getSelect();
						$select->setSize(1);
						$query = 'SELECT '.$f['sql_join']['field_label'].' as title, id FROM '.SSDBSchema::_getTableAttr($f['sql_join']['table'], 'name', true).'';
						$select->addSqlOptions($query);
						break;
					case 'select':
						$field = &$form->addSelectField($name);
						$field->setLabel(ss_utils::i18l('label_'.$name));
						$select = &$field->getSelect();
						foreach($f['input_settings']['values'] as $val){
							$select->addOption($val,$val);
						}
						$select->setSize(1);
						break;
				}
			}
		}
		
		if($mode_type == 'edit'){$form->addParam('id', $id);}
		$form->show();	
	}
	
	/**
	* Editier bzw. Neuerfassen Formular in Form von rex_form
	* param $table: Tabellenname
	* param $id: ID, der zu editierenden Eintrag
	*/
	public static function displayFormSearch($table){
		try{
			$fields = SSDBSchema::_getFields($table, null, array('show_in'=>'search'));
			echo self::parse('search.form.tmpl.php', 
				array(
					'fields' => $fields
					, 'search' => rex_post('search', 'string', '')
					, 'search_fields' => rex_post('search_fields', 'string', '')
				)
			);
		}catch(SSException $e) {
			echo $e;
		}
	}
	
	/**
	* Das Template wird geparst (Platzhalter/Variable ersetzen mit Values)
	* param $template: Template Datei Name
	* param $params: Alle Variable in Array verpackt, die
	*                im Template verwendet werden
	* return string
	*/
	public static function parse($template, array $params = array()){
        global $REX, $I18N;

        extract($params);
		
		$tmppath = self::getTemplatePath($template);
		
        ob_start();
		
        include $tmppath;
		
        return ob_get_clean();
    }
	
	/**
	* Gibt den Pfad der Template-Datei zurück
	* param $template: Template Datei Name
	* return string
	*/
    public static function getTemplatePath($template){
        global $REX;
        $templates = (array) $template;
        foreach ($templates as $template) {
			foreach (array_reverse($REX['ADDON']['square_shop']['templatepaths']) as $path) {
				if (file_exists($path.'/'.$template)) {
					return $path.'/' . $template;
				}
			}
        }
		throw new SSException('Template file not found', self::ERROR_TEMPLATE_R_NOT_FOUND);
    }
}

