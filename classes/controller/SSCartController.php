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
	const TABLE_ORDER_ITEM = 'order_item';
	// Form Action Code
	const ACTION_DEL_FROM_CART	 	= 'del_from_cart';
	const ACTION_UPDATE_ART_QTY 	= 'update_art_qty';
	const ACTION_EMPTY_CART 		= 'empty_cart';
	
	// Singleton --> Session Objekt
	private $session;
	
	// Array of SSArticle Objects
	private $articlelist;
	
	// POST Var Daten -> korrekt eingegebene von User
	private $formPropertiesAndValues;
	
	// Fehler
	private $formPropertyValueErrors;
	
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
		$this->formPropertiesAndValues = SSHelper::getPostByFormId(SSCartView::FORM_ID);
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
		$artId = (int)$this->formPropertiesAndValues['id'];
		$qty = (int)$this->formPropertiesAndValues['qty'];
		switch($this->formPropertiesAndValues['action']){
			case SSArticleController::ACTION_ADD_TO_CART:
				if($this->isInputValid()){
					$this->addToCart($artId, $qty);
				}
				break;
			case self::ACTION_UPDATE_ART_QTY:
				if($this->isInputValid()){
					if($qty > 0){
						$this->updateQty($artId, $qty);
					}elseif($qty == 0){
						$this->removeFromCart($artId);
					}
				}
				break;
			case self::ACTION_DEL_FROM_CART:
				if($this->isInputValid()){
					$this->removeFromCart($artId);
				}
				break;
			case self::ACTION_EMPTY_CART:
				$this->clearCart();
				break;
			case 'null':
				break;
		}
	}
	
	
	
	/*
	* Formular Input Dateon vom User auf Richtigkeit überpürfen
	* return bool
	*/
	public function isInputValid(){
		$errorsOrderItem1 = SSHelper::checkFromInputs(self::TABLE_ORDER_ITEM, SSDBSchema::SHOW_IN_CART_ITEM
												, $this->formPropertiesAndValues);
		$errorsOrderItem2 = SSHelper::checkFromInputs(SSArticle::TABLE, SSDBSchema::SHOW_IN_CART_ITEM
												, $this->formPropertiesAndValues);
												
		$this->formPropertyValueErrors = array_merge($errorsOrderItem1, $errorsOrderItem2);
		
		d($this->formPropertyValueErrors);
		
		if(!$this->isArticleExists($this->formPropertiesAndValues['id'])){
			$this->formPropertyValueErrors['id']['notfound'] = 1;
		}
		if(sizeof($this->formPropertyValueErrors) > 0){
			return false;
		}
		return true;
	}
	
	/*
	* überprüfen ob Artikel in DB vorhanden
	*/
	public function isArticleExists($artId){
		$article = new SSArticle();
		if($article->loadById($artId)){
			return true;
		}
		return false;
	}
	
	/*
	* überprüfen ob Artikel in DB vorhanden
	*/
	public function ssssssisArticleExists($artId){
		$artId = (int)$this->formPropertiesAndValues['id'];
		$qty = (int)$this->formPropertiesAndValues['qty'];
		if($qty > 0){
			$article = new SSArticle();
			if($article->loadById($artId)){
				return true;
			}
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
	* Artikel Menge setzen
	* param int $artId: Artikel ID
	* param int $qty: Menge
	*/
	public function updateQty($artId, $qty){
		global $REX;
		$items = $this->session->get('cartItems');
		$updated = false;
		for($i=0; $i<sizeof($items); $i++){
			if((int)$items[$i]['id'] == $artId){
				$items[$i]['qty'] = $qty;
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
		// Array Keys neuverteilen um lücken zufüllen
		$items = array_values($items);
		// Items in Session speichern
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
		
		// Artikel IDs aus Warenkorb
		$ids = $this->getCartItemIds();
		
		// Artikeln aus DB gefiltert nach PrimaryKeys
		$articles = $this->article->getByIds($ids);
		
		$params = array();
		
		// Währung
		$params['currency'] = $currency;
		
		// MwSt Satz
		$params['mwst'] = $mwst;
		
		// Labels
		$params['label_bild'] = SSHelper::i18l('label_bild');
		$params['label_artno'] = SSHelper::i18l('label_artno');
		$params['label_bezeichnung'] = SSHelper::i18l('label_bezeichnung');
		$params['label_price'] = SSHelper::i18l('label_price');
		$params['label_qty'] = SSHelper::i18l('label_qty');
		$params['label_subtotal'] = SSHelper::i18l('label_subtotal');
		$params['label_total'] = SSHelper::i18l('label_total');
		$params['label_entfernen'] = SSHelper::i18l('label_entfernen');
		$params['label_empty_cart'] = SSHelper::i18l('empty_cart');
		$params['label_update_art'] = SSHelper::i18l('ok');
		
		$params['action_del_from_cart'] = self::ACTION_DEL_FROM_CART;
		$params['action_update_art'] = self::ACTION_UPDATE_ART_QTY;
		$params['action_empty_cart'] = self::ACTION_EMPTY_CART;
		
		// Artikel Daten zusammenführen
		$tmpArticles = array();
		
		// Total
		$total = 0;
		foreach($articles as $art){
			$tmpArt = $art;
			$tmpArt['qty'] = $this->getItemQtyById($tmpArt['id']);
			$tmpArt['subtotal'] = (int)$tmpArt['price'] * (int)$tmpArt['qty'];
			$total += $tmpArt['subtotal'];
			
			
			$tmpArt['price'] = $this->article->formatPrice($tmpArt['price']);
			$tmpArt['subtotal'] = $this->article->formatPrice($tmpArt['subtotal']);
			
			$tmpArt['imgs'] = explode(',', $tmpArt['images']);
			$tmpArt['url'] = rex_getUrl($this->getItemPageIdById($tmpArt['id']), $REX['CLANG_ID']);
			
			$pageId = $this->getItemPageIdById($tmpArt['id']);
			$urlQueryArray = array(SSArticleController::VAR_NAME_ARTILEID=>$tmpArt['id']);
			$tmpArt['url'] = rex_getUrl($pageId, $REX['CLANG_ID'], $urlQueryArray);
			
			$tmpArticles[$tmpArt['id']] = $tmpArt;
		}
		$params['total'] = $this->article->formatPrice($total);
		
		// Artikel sortieren nach Reihenfolge
		// welche der Käufer zum Warenkorb hinzugefügt hat
		$params['articles'] = array();
		foreach($ids as $id){
			$params['articles'][] = $tmpArticles[$id];
		}
			
		$this->cartView->displayCartHtml($params);
	}
	
	/*
	*/
	public function displayCart(){
		$this->cartView->displayCartHtml($params);
	}
}