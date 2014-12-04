<?php
class SSObjectView {
	// Form Array Key Name -> $_POST[SSForm][[FORM_ID]
	protected $FORM_ID = 'SSObjectFormView';
	
	/**
	* Register Maske anzeigen
	*/
	public function displayFormHtml($params = array()){
		$params['FORM_ID'] = $this->FORM_ID;
		try{			
			echo SSGUI::parse('form.tmpl.php', $params);
		}catch(SSException $e) {
			echo $e;
		}
	}
	
	private function displayMessageHtml($params = array()){
		$params['FORM_ID'] = $this->FORM_ID;
		try{			
			echo SSGUI::parse('message.tmpl.php', $params);
		}catch(SSException $e) {
			echo $e;
		}
	}
	
	public function displaySuccessMessageHtml($params = array()){
		$params['msg_type'] = 'success';
		if(!isset($params['label_text'])){
			$params['label_text'] = SSHelper::i18l($this->FORM_ID.'_success_text');
		}
		$this->displayMessageHtml($params);
	}
	
	public function displayErrorMessageHtml($params = array()){
		$params['msg_type'] = 'error';
		if(!isset($params['label_text'])){
			$params['label_text'] = SSHelper::i18l($this->FORM_ID.'_failure_text');
		}
		$this->displayMessageHtml($params);
	}
}