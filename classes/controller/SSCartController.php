<?php
#
#
# SSCartController
# https://github.com/last-hero/square_shop
#
# (c) Gobi Selva
# http://www.square.ch
#
# Diese Klasse verwaltet den ganzen Warenkorb
#
#

class SSCartController {	
	// Singleton --> Session Objekt
	private $session;
	
	// Array of SSArticle Objects
	private $articlelist;
	
	// POST Var Daten -> korrekt eingegebene von User
	private $formPropertiesAndValues;
	
	// SSCartView Object
	private $cartView;
	
	/*
	* Konstruktor: lädt Session Instanz (Singleton)
	* Falls POST Request abgeschickt wurde, dann daten laden
	*/
    public function __construct(){
		// Session Objekt (Singleton) holen
		$this->session = SSSession::getInstance();
		
		$this->article = new SSArticle();
		
		$this->cartView = new SSCartView();
		
		// Form Post Vars (User input) holen
		$this->formPropertiesAndValues = SSHelper::getPostByFormId(SSArticleView::FORM_ID);
    }
	
	/*
	* Warenkorb starten
	*/
	public function invoke(){
		$this->cartHandler();
		$this->displayView();
	}
	
	/*
	* Warenkorb Handler: Add to Cart, Remove from Cart, Menge ändern
	*/
	public function cartHandler(){
		switch($this->formPropertiesAndValues['action']){
			case SSArticleController::ACTION_ADD_TO_CART:
				if($this->isInputAddToCartValid()){
					$artId = (int)$this->formPropertiesAndValues['id'];
					$qty = (int)$this->formPropertiesAndValues['qty'];
					$this->addToCart($artId, $qty);
				}
				break;
			case 'null':
				break;
		}
	}
	
	/*
	* Add To Cart Inputs überprüfen
	*/
	public function isInputAddToCartValid(){
		$artId = (int)$this->formPropertiesAndValues['id'];
		$qty = (int)$this->formPropertiesAndValues['qty'];
		$article = new SSArticle();
		if($article->loadById($artId)){
			return true;
		}
		return false;
	}
	
	/*
	* Artikel zum Warenkorb hinzufügen
	* Dabei werden ID, Qty in Session gespeichert
	* param int $artId: Artikel ID
	* param int $qty: Menge
	*/
	public function addToCart($artId, $qty){
		$items = $this->session->get('cartItems');
		$updated = false;
		for($i=0; $i<sizeof($items); $i++){
			if((int)$items[$i]['id'] == $artId){
				$items[$i]['qty'] = (int)$items[$i]['qty'] + $qty;
				$updated = true;
			}
		}
		if(!$updated){
			$items[] = array('id' => $artId, 'qty' => $qty);
		}
		$this->session->set('cartItems', $items);
	}
	
	/*
	* Artikel vom Warenkorb löschen
	*/
	public function removeFromCart($artId){
		$items = $this->session->get('cartItems');
		for($i=0; $i<sizeof($items); $i++){
			if((int)$items[$i]['id'] == $artId){
				unset($items[$i]);
			}
		}
		$this->session->set('cartItems', $items);
	}
	
	/*
	* Artikel zum Warenkorb hinzufügen
	* Dabei werden ID, Qty in Session gespeichert
	*/
	public function clearCart(){
		$this->session->remove('cartItems');
	}
	
	/*
	* berechnet Total + Subtotal neu
	*/
	public function calcTotal(){
	}
	
	/*
	*/
	public function displayView(){
		$this->cartView->displayCartHtml($params);
	}
	
	/*
	*/
	public function displayCart(){
		$this->cartView->displayCartHtml($params);
	}
}