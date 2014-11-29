<?php
class SSCustomerRegisterView {
	// Form Array Key Name
	const FORM_ID = 'register';
	
	/**
	* Register Maske anzeigen
	*/
	public function displayRegisterHtml($params = array()){
		$params['FORM_ID'] = self::FORM_ID;
		try{			
			echo SSGUI::parse(SSCustomer::TABLE.'.register.form.tmpl.php', $params);
		}catch(SSException $e) {
			echo $e;
		}
	}
	
	public function displayErrors($formErrors){		
		$errorLabels = array();
		foreach($formErrors as $name => $errors){
			$errorLabels[$name] = array(
				'label' => SSHelper::i18l(self::FORM_ID.'_label_'.$name)
			);
			foreach($errors as $errName => $errVal){
				$errorLabels[$name]['label_error'][] = SSHelper::i18l('error_'.$errName);
			}
		}
		$params = array();
		$params['formErrors'] = $formErrors;
		$params['errorLabels'] = $errorLabels;
		try{			
			echo SSGUI::parse('errors.tmpl.php', $params);
		}catch(SSException $e) {
			echo $e;
		}
	}
	
	public function displaySuccess(){
		$params = array();
		$params['label_success'] = SSHelper::i18l(self::FORM_ID.'_success');
		try{			
			echo SSGUI::parse(SSCustomer::TABLE.'.register.success.tmpl.php', $params);
		}catch(SSException $e) {
			echo $e;
		}
	}
}

