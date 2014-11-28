<?php
class SSClientRegisterView {
	// Form Array Key Name
	const FORM_ID = 'register';
	
	/**
	* Register Maske anzeigen
	*/
	public function displayRegisterHtml($params = array()){
		$params['FORM_ID'] = self::FORM_ID;
		
		try{
			$fields = SSDBSchema::_getFields(SSClient::TABLE, null, array('show_in'=>'add'));
		}catch(SSException $e) {
			echo $e;
		}
		$params['fields'] = array();
		foreach($fields as $f){
			$name = $f['name'];
			if(isset($f['input'])){
				$params['fields'][] = array(
					'name' => $name
					, 'label' => SSHelper::i18l(FORM_ID.'_label_'.$name)
					, 'type' => $f['input']
				);
			}
		}
		
		$form_data = '';
		foreach($fields as $f){
			$name = $f['name'];
			$label = SSHelper::i18l(self::FORM_ID.'_label_'.$name);
			if(isset($f['input'])){
				switch ($f['input']){
					case 'text':
						$form_data .= 'text|'.$name.'|'.$label.'||[no_db]|cssclassname
						';
						break;
					case 'textarea':
						$form_data .= 'textarea|'.$name.'|'.$label.'||[no_db]
						';
						break;
					case 'media':
						break;
					case 'select':
						$form_data .= 'select|'.$name.'|'.$label.'|Frau=w,Herr=m|[no_db]|defaultwert|multiple=1|selectsize
						';
						break;
					case 'select_sql':
						break;
				}
			}
		}
		
		$xform = new rex_xform;
		$xform->setDebug(TRUE);
		$form_data = trim(str_replace("<br />","",rex_xform::unhtmlentities($form_data)));
		$xform->setFormData($form_data);
		$xform->setRedaxoVars(REX_ARTICLE_ID,REX_CLANG_ID); 

		
		echo $xform->getForm();
		/*
		try{			
			echo SSGUI::parse(SSClient::TABLE.'.register.form.tmpl.php', $params);
		}catch(SSException $e) {
			echo $e;
		}
		*/
	}
}

