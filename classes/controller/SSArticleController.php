<?php
/** @file SSArticleController.php
 *  @brief Artikelverwaltung - Controller
 *
 *  Diese Klasse dient für das Verwalten von
 *  Artikel Daten und deren Views
 *
 *  @author Gobi Selva
 *  @author http://www.square.ch
 *  @author https://github.com/last-hero/square_shop
 */
class SSArticleController extends SSController {
	/**
	 * Variable Name für Artikel ID in der URL 
	 * -> $_GET[VAR_NAME_ARTILEID]
	 */
	const VAR_NAME_ARTILEID = 'artid';
	
	/**
	 * Jedes Formular hat ein Action
	 * zum identifizieren, um welche
	 * Aktion ausgeführt werden soll.
	 * Hier wird "Add To Cart", ein
	 * Artikel zum Warenkorb hinzufügen.
	 */
	const ACTION_ADD_TO_CART = 'addtocart';
	
	/**
	 * @see SSArticleView::FORM_ID
	 */
	protected $FORM_ID = SSArticleView::FORM_ID;
	
	/**
	 * @see SSArticle::TABLE
	 */
	protected $TABLE = SSArticle::TABLE;
	
	/**
	 * @see SSDBSchema::SHOW_IN_DETAIL
	 */
	protected $SHOW_IN = SSDBSchema::SHOW_IN_DETAIL;
	
	/**
	 * Ein Artikel-Objekt für die Detailansicht
	 * @see SSArticle
	 */
	private $article;
	
	/**
	 * Ein Array für Artikel-Objekte für Listenansicht
	 * @see SSArticle
	 */
	private $articlelist;
		
	/**
	 * Ein Artikel View für Detail-/Listenansicht
	 * @see SSArticleView
	 */
	private $articleView;
	
	/**
	 * Kategorie Id, nach dem die Artikeln
	 * gefiltert werden (Listenansicht).
	 */
	private $categoryId;
		
	/**
	 * Artikel Id für die Detailansicht
	 * eines Artikel.
	 */
	private $articleId;
	
	/** @brief Initialisierung
	 *
	 *  Erstellen der benötigten Objekte.
	 *  Dazu zählen Artikel, Artikel-View und
	 *  ein Array für die Artikelliste.
	 *  Zudem wird ein ArtikelID gesetzt, 
	 *  Falls die Detailansicht von einer
	 *  Artikel erwünscht wird.
	 */
    protected function init(){
		$this->article = new SSArticle();
		$this->articleView = new SSArticleView();
		$this->articlelist = array();
		if(isset($_GET[self::VAR_NAME_ARTILEID])){
			$this->articleId = $_GET[self::VAR_NAME_ARTILEID];
		}
    }
	
	/** @brief Starter
	 *
	 *  Artikel Detail/List-Ansicht starten.
	 *  Falls ArtikelID gesetzt wurde, 
	 *  	dann Daten von dieser Artikel aus DB holen
	 *  Falls kein ArtikelID sondern Kategorie ID gesetzt wurde, 
	 *  	dann alle Artikel der Kategorie holen
	 */
	public function invoke(){
		if((int)$this->articleId > 0){
			$this->article->loadById($this->articleId);
		}elseif((int)$this->categoryId > 0){
			// Daten nach Kategorie ID holen
			$articles = $this->article->getByForeignId($this->categoryId, SSCategory::TABLE);
			foreach($articles as $article){
				$artObj = new SSArticle();
				$artObj->set($artObj->getClearedUnknownProperties($article));
				// Artikel Objekte in Array laden
				$this->articlelist[] = $artObj;
			}
		}
		// View anzeigen --> Detail / List
		$this->displayView();
	}
	
	/** @brief Kategorie ID setzen
	 *
	 *  Kategorie ID für Artikelliste setzen
	 *  @param $categoryId
	 */
	public function setCategoryId($categoryId){
		$this->categoryId = $categoryId;
	}
	
	/** @brief Artikel ID setzen
	 *
	 *  Artikle ID für Detailansicht setzen
	 *  @param $articleId
	 */
	public function setArticleId($articleId){
		$this->articleId = $articleId;
	}
	
	/** @brief Artikel oder Artikeln anzeigen
	 *
	 *  Detailansicht vom Artikel.
	 *  Falls mehrere Artikeln in der Liste vorhanden,
	 *  dann Artikelliste anzeigen
	 *  @param $categoryId
	 */
	public function displayView(){
		if(sizeof($this->articlelist) > 0){
			$this->displayListView();
		}else{
			$this->displayDetailView();
		}
	}
	
	/** @brief Detailansicht
	 *
	 *  Detailansicht vom Artikel.
	 */
	public function displayDetailView(){
		$currency = SSHelper::getSetting('currency');
		$mwst = SSHelper::getSetting('mwst');
		
		$params = array();
		$params['currency'] = $currency;
		$params['mwst'] = $mwst;
		$params['action'] = self::ACTION_ADD_TO_CART;
		
		$params['label_goback'] = SSHelper::i18n('label_goback');
		$params['id'] = $this->article->get('id');
		$params['no'] = $this->article->get('no');
		$params['title'] = $this->article->get('title');
		$params['description'] = $this->article->get('description');
		$params['price'] = $this->article->formatPrice($this->article->get('price'));
		$params['url'] = rex_getUrl($REX['ARTICLE_ID'], $REX['CLANG_ID']);
		$params['imgs'] = explode(',', $this->article->get('images'));
		$params['label_submit'] = SSHelper::i18n('label_addtocart');
		$params['label_qty'] = SSHelper::i18n('label_qty');
		$this->articleView->displayDetailHtml($params);
	}
	
	/** @brief Listenansicht
	 *
	 *  Listenansicht von Artikeln, gefiltert nach Kategorie
	 */
	public function displayListView(){
		$currency = SSHelper::getSetting('currency');
		$mwst = SSHelper::getSetting('mwst');
		
		$params = array();
		$params['currency'] = $currency;
		$params['mwst'] = $mwst;
		$params['action'] = self::ACTION_ADD_TO_CART;
		
		$params['articles'] = array();
		$params['label_detail'] = SSHelper::i18n('detail');
		foreach($this->articlelist as $article){
			$params['articles'][] = array(
				'title' => $article->get('title')
				, 'id' => $article->get('id')
				, 'no' => $article->get('no')
				, 'price' => $article->formatPrice($article->get('price'))
				, 'url' => rex_getUrl($REX['ARTICLE_ID'], $REX['CLANG_ID'], array(self::VAR_NAME_ARTILEID=>$article->get('id')))
				, 'imgs' => explode(',', $article->get('images'))
			);
		}
		$this->articleView->displayListHtml($params);
	}
}