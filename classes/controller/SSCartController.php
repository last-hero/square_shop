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
	* Alle Artikel ID + Menge vom Warenkorb holen
	*/
	public function getCartItems(){
		return $this->session->get('cartItems');
	}
	
	/*
	* Alle Artikel IDs vom Warenkorb holen
	*/
	public function getCartItemIds(){
		$items = $this->session->get('cartItems');
		$ids = array();
		for($i=0; $i<sizeof($items); $i++){
			$ids[] = $items[$i]['id'];
		}
		return $ids;
	}
	
	/*
	* Menge vom Artikel nach ID holen
	*/
	public function getItemQtyById($id){
		$items = $this->session->get('cartItems');
		for($i=0; $i<sizeof($items); $i++){
			if((int)$items[$i]['id'] == $id){
				return (int)$items[$i]['qty'];
			}
		}
		return 0;
	}
	
	/*
	* (REX_ARTICLE_ID) ID der Seite,
	* auf dem sich der Artikel befindet
	*/
	public function getItemPageIdById($id){
		$items = $this->session->get('cartItems');
		for($i=0; $i<sizeof($items); $i++){
			if((int)$items[$i]['id'] == $id){
				return (int)$items[$i]['pageId'];
			}
		}
		return 0;
	}
	
	/*
	* Artikel zum Warenkorb hinzufügen
	* Dabei werden ID, Qty in Session gespeichert
	* param int $artId: Artikel ID
	* param int $qty: Menge
	*/
	public function addToCart($artId, $qty){
		global $REX;
		$items = $this->session->get('cartItems');
		$updated = false;
		for($i=0; $i<sizeof($items); $i++){
			if((int)$items[$i]['id'] == $artId){
				$items[$i]['qty'] = (int)$items[$i]['qty'] + $qty;
				$updated = true;
			}
		}
		if(!$updated){
			$items[] = array('id' => $artId, 'qty' => $qty, 'pageId' => $REX['ARTICLE_ID']);
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
		$currency = SSHelper::getSetting('currency');
		$mwst = SSHelper::getSetting('mwst');
		
		$ids = $this->getCartItemIds();
		$articles = $this->article->getByIds($ids);
		
		$params = array();
		$params['currency'] = $currency;
		$params['mwst'] = $mwst;
		//$params['action'] = self::ACTION_ADD_TO_CART;
		/*
		
            	<th class="ss-img"><?=$label_bild?></th>
            	<th class="ss-artno"><?=$label_artno?></th>
            	<th class="ss-title"><?=$label_bezeichnung?></th>
            	<th class="ss-price"><?=$label_price?></th>
            	<th class="ss-qty"><?=$label_qty?></th>
            	<th class="ss-subtotal"><?=$label_subtotal?></th>
		*/
		
		$params['label_bild'] = SSHelper::i18l('label_bild');
		$params['label_artno'] = SSHelper::i18l('label_artno');
		$params['label_bezeichnung'] = SSHelper::i18l('label_bezeichnung');
		$params['label_price'] = SSHelper::i18l('label_price');
		$params['label_qty'] = SSHelper::i18l('label_qty');
		$params['label_subtotal'] = SSHelper::i18l('label_subtotal');
		
		$params['articles'] = array();
		foreach($articles as $art){
			$tmpArt = $art;
			$tmpArt['qty'] = $this->getItemQtyById($tmpArt['id']);
			$tmpArt['subtotal'] = (int)$tmpArt['price'] * (int)$tmpArt['qty'];
			
			
			$tmpArt['price'] = $this->article->formatPrice($tmpArt['price']);
			$tmpArt['subtotal'] = $this->article->formatPrice($tmpArt['subtotal']);
			
			$tmpArt['imgs'] = explode(',', $tmpArt['images']);
			$tmpArt['url'] = rex_getUrl($this->getItemPageIdById($tmpArt['id']), $REX['CLANG_ID']);
			
			$pageId = $this->getItemPageIdById($tmpArt['id']);
			$urlQueryArray = array(SSArticleController::VAR_NAME_ARTILEID=>$tmpArt['id']);
			$tmpArt['url'] = rex_getUrl($pageId, $REX['CLANG_ID'], $urlQueryArray);
					
			$params['articles'][] = $tmpArt;
		}
		$this->cartView->displayCartHtml($params);
	}
	
	/*
	*/
	public function displayCart(){
		$this->cartView->displayCartHtml($params);
	}
}