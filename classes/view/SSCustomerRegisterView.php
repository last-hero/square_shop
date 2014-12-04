<?php
class SSCustomerRegisterView extends SSObjectView{
	// Form Array Key Name
	const FORM_ID = 'register';
	protected $FORM_ID = self::FORM_ID;
	
	public function displaySuccessMessage(){
		$this->displaySuccessMessageHtml(array(
			'label_text' => SSHelper::i18l(self::FORM_ID.'_success_text')
		));
	}
}

