<?php
/** @file SSCartController.php
 *  @brief Warenkorb Verwalten
 *
 *  Diese Klasse verwaltet den ganzen Warenkorb
 *
 *  @author Gobi Selva
 *  @author http://www.square.ch
 *  @author https://github.com/last-hero/square_shop
 */

class SSCartController extends SSController{
	// Form Action Code
	const ACTION_DEL_FROM_CART	 = 'del_from_cart';
	const ACTION_UPDATE_ART_QTY 	= 'update_art_qty';
	const ACTION_EMPTY_CART 		= 'empty_cart';
	
	/**
	 * @see SSArticleView::FORM_ID
	 */
	protected $FORM_ID = SSCartView::FORM_ID;
	
	/**
	 * @see SSArticle::TABLE
	 */
	protected $TABLE = SSArticle::TABLE;
	
	/**
	 * @see SSDBSchema::SHOW_IN_DETAIL
	 */
	protected $SHOW_IN = SSDBSchema::SHOW_IN_CART_ITEM;
	
	/**
	 * Ein Array für Artikel-Objekte
	 * die zum Warenkorb hinzugefügt werden
	 * 
	 * @see SSArticle
	 */
	private $articlelist;
		
	/**
	 * Ein Objekt für das Warenkorb-View
	 * @see SSCartView
	 */
	private $cartView;
	
	/**
	 * Redaxo Seiten ID zum Weiterleitein
	 * um Checkout durchzuführen.
	 */
	private $checkoutPageId;
	
	/**
	 * Flag für einfache 
	 * Warenkorbansicht
	 */
	public $simpleView;
	
	
	/** @brief Initialisierung
	 *
	 *  Erstellen der benötigten
	 *  Objekte (Artikle und Warenkorb-View).
	 *  Zuweisen der Redaxo Seiten ID von
	 *  der Redaxo Variable $REX.
	 */
    protected function init(){
		$this->article = new SSArticle();
		$this->cartView = new SSCartView();
		$this->checkoutPageId = $REX['ARTICLE_ID'];
    }
	
	/** @brief Starter
	 *
	 *  Hier wird die ganze Warenkorb-Geschichte
	 *  in Gang gesetzt. Dazu zählen das logische
	 *  Teil (Artikel add, remove, change-qty) und
	 *  das Message-Handling um Meldungen anzuzeigen,
	 *  die den jeweiligen Aktion bestätigen. Und
	 *  der Warenkorb wird angezeigt.
	 */
	public function invoke(){
		$this->cartHandler();
		$this->messageHandler();
		if($this->isCartEmpty()){
			if(!$this->showMessage){
				$this->cartView->displaySuccessMessage(SSHelper::i18l('cart_is_empty'));
			}
		}else{
			$this->displayView();
		}
	}
	
	/** @brief Warenkorb Handler
	 *
	 *  Hier geschieht die ganze Warenkorb Aktionen:
	 *  Add to Cart, Remove from Cart, Menge ändern
	 *  und den Warenkorb leeren.
	 *  Mit einem Switch-Case wird die Aktion abgefangen,
	 *  welche das Formular per POST-Variable mit verschickt.
	 */
	public function cartHandler(){
		$artId = (int)$this->formPropertiesAndValues['id'];
		$qty = (int)$this->formPropertiesAndValues['qty'];
		switch($this->formPropertiesAndValues['action']){
			case SSArticleController::ACTION_ADD_TO_CART:
				if($this->isInputValid()){
					$this->addToCart($artId, $qty);
				}
				$this->showMessage = true;
				break;
			case self::ACTION_UPDATE_ART_QTY:
				if($this->isInputValid()){
					if($qty > 0){
						$this->updateQty($artId, $qty);
					//}elseif($qty == 0){
						//$this->removeFromCart($artId);
					}
				}
				$this->showMessage = true;
				break;
			case self::ACTION_DEL_FROM_CART:
				if($this->isInputValidDelCartItem()){
					$this->removeFromCart($artId);
					$this->showMessage = true;
				}
				break;
			case self::ACTION_EMPTY_CART:
				$this->clearCart();
				$this->showMessage = true;
				break;
			default:
				break;
		}
	}
	
	/** @brief Überprüfen der Formular Daten
	 *
	 *  Die abgeschickte Formulare (add to cart
	 *  , empty carte, change qty)
	 *  werden auf Richtigkeit überprüft (z.B. ob die 
	 *  Menge korrekt angegeben ist).
	 *
	 *  @return bool  true = Eingabe korrekt     false = Eingabe falsch
	 *  @see SSHelper::checkFromInputs
	 */
	public function isInputValid(){
		$errorsOrderItem1 = SSHelper::checkFromInputs(SSCheckout::TABLE_ORDER_ITEM, SSDBSchema::SHOW_IN_CART_ITEM
												, $this->formPropertiesAndValues);
		$errorsOrderItem2 = SSHelper::checkFromInputs(SSArticle::TABLE, SSDBSchema::SHOW_IN_CART_ITEM
												, $this->formPropertiesAndValues);
												
		$this->formPropertyValueErrors = array_merge($errorsOrderItem1, $errorsOrderItem2);
		
		if(sizeof($this->formPropertyValueErrors) > 0){
			return false;
		}
		return true;
	}
	
	/** @brief Überprüfen der Eingabe zum Artikel entfernen
	 *
	 *  Prüft ob der Artikel aus dem Artikel entfernt werden kann.
	 *
	 *  @return bool  true = Eingabe korrekt     false = Eingabe falsch
	 *  @see SSCartController::checkFromInputs
	 */
	public function isInputValidDelCartItem(){
		$errorsOrderItem1 = SSHelper::checkFromInputs(SSCheckout::TABLE_ORDER_ITEM, SSDBSchema::SHOW_IN_CART_ITEM_DEL
												, $this->formPropertiesAndValues);
		$errorsOrderItem2 = SSHelper::checkFromInputs(SSArticle::TABLE, SSDBSchema::SHOW_IN_CART_ITEM_DEL
												, $this->formPropertiesAndValues);
												
		$this->formPropertyValueErrors = array_merge($errorsOrderItem1, $errorsOrderItem2);
		
		if(sizeof($this->formPropertyValueErrors) > 0){
			return false;
		}
		return true;
	}
	
	/** @brief Alle Artikeln holen
	 *
	 *  Alle Artikel ID + Menge vom Warenkorb holen
	 *
	 *  @return array
	 */
	public function getCartItems(){
		return $this->session->get('cartItems');
	}
	
	/** @brief Artikel IDs holen
	 *
	 *  Alle IDs der Artikeln vom Warenkorb holen
	 *
	 *  @return array
	 */
	public function getCartItemIds(){
		$items = $this->session->get('cartItems');
		$ids = array();
		for($i=0; $i<sizeof($items); $i++){
			$ids[] = $items[$i]['id'];
		}
		return $ids;
	}
	
	/** @brief Menge nach Artikel ID
	 *
	 *  Menge eines Artikels, nach ID
	 *  gefiltert, holen
	 *
	 *  @return int
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
	
	/** @brief Seite der Artikel
	 *
	 *  (REX_ARTICLE_ID) ID der Seite,
	 *  auf dem sich der Artikel befindet,
	 *  um eine direkte Url zum Artikel zu
	 *  bilden.
	 *
	 *  @return int
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
	
	/** @brief Artikel zum Warenkorb hinzufügen
	 *
	 *  Dabei werden ID, Qty in Session gespeichert
	 *  auf dem sich der Artikel befindet,
	 *  um eine direkte Url zum Artikel zu
	 *
	 *  @param int $artId: Artikel ID
	 *  @param int $qty: Menge
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
	
	/** @brief Artikel Menge setzen
	 *
	 *  Artikelmenge im Warenkorb ändern.
	 *
	 *  @param int $artId: Artikel ID
	 *  @param int $qty: Menge
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
	
	/** @brief Artikel vom Warenkorb löschen
	 *
	 *  Der gewünschte Artikel wird vom
	 *  Warenkorb (aus der Session) gelöscht.
	 *
	 *  @param int $artId: Artikel ID
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
	
	/** @brief Warenkorb leeren
	 *
	 *  Session Variable wird gelöscht,
	 *  dabei werden alle Daten betreffend
	 *  Warenkorb gelöscht.
	 */
	public function clearCart(){
		$this->session->remove('cartItems');
	}
	
	/** @brief Is Warenkorb leer?
	 *
	 *  Überprüfen ob Warenkorb leer ist
	 *
	 *  @return bool
	 */
	public function isCartEmpty(){
		$items = $this->session->get('cartItems');
		if(sizeof($items)){
			return false;
		}
		return true;
	}
	
	/** @brief Seiten ID für Checkout setzen
	 *
	 *  Die Redaxo Seiten ID, auf dem
	 *  die ganze Checkout-Geschichte gehandlet
	 *  wird, gesetzt.
	 *
	 *  @return bool
	 */
	public function setCheckoutPageId($id){
		$this->checkoutPageId = $id;
	}
	
	/** @brief Warenkorb Ansicht
	 *
	 *  Warenkorb View wird zusammengestellt
	 *  und angezeigt.
	 *
	 *  @return bool
	 */
	public function displayView(){
		$currency = SSHelper::getSetting('currency');
		$mwst = SSHelper::getSetting('mwst');
		
		// Artikel IDs aus Warenkorb
		$ids = $this->getCartItemIds();
		
		// Artikeln aus DB gefiltert nach PrimaryKeys
		$articles = $this->article->getByIds($ids);
		
		$params = array();
		
		if($this->simpleView){
			$params['simpleView'] = 1;
		}
		
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
			
		$params['url_checkout'] = rex_getUrl($this->checkoutPageId, $REX['CLANG_ID'], array('ss-cart'=>'checkout'));
		$params['label_checkout'] = SSHelper::i18l('label_checkout');
		
		$this->cartView->displayCartHtml($params);
	}
}