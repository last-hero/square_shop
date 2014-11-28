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
			$fields = SSDBSchema::_getFields(SSClient::TABLE, null, array('show_in'=>'register'));
		}catch(SSException $e) {
			echo $e;
		}
		
		// automatisch Formular generieren
		// Anhand von vordefinierten Variablen in der SSDBSchema Klasse
		$params['fields'] = array();
		foreach($fields as $f){
			$name = $f['name'];
			if(isset($f['input'])){
				$array_constraint_vals_labels = array();
				foreach($f['input_constraint_vals'] as $v){
					$array_constraint_vals_labels[] = SSHelper::i18l(self::FORM_ID.'_label_'.$name.'_'.$v);
				}
				$params['fields'][] = array(
					'name' => $name
					, 'label' => SSHelper::i18l(self::FORM_ID.'_label_'.$name)
					, 'constraint_vals' => $f['input_constraint_vals']
					, 'label_constraint_vals' => $array_constraint_vals_labels
					, 'type' => $f['input']
				);
			}
		}
		
		try{			
			echo SSGUI::parse(SSClient::TABLE.'.register.form.tmpl.php', $params);
		}catch(SSException $e) {
			echo $e;
		}
	}
}

