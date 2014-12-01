<?php
#
#
# SSArticleController
# https://github.com/last-hero/square_shop
#
# (c) Gobi Selva
# http://www.square.ch
#
# Diese Klasse dient für das Verwalten von
# Artikel Daten und deren Views
#
#

class SSArticleController {
	// GET Variable Name
	const VAR_NAME_ARTILEID = 'artid';
	
	// Form Action Code
	const ACTION_ADD_TO_CART = 'addtocart';
	
	// Singleton --> Session Objekt
	private $session;
	
	// SSArticle Object
	private $article;
	
	// Array of SSArticle Objects
	private $articlelist;
	
	// SSArticleView Object
	private $articleView;
	
	// Kategorie ID
	private $categoryId;
	
	// Artikel ID
	private $articleId;
	
	// Form Felder mit Values (User Input)
	private $formPropertiesAndValues;
	
	
	/*
	* Konstruktor: lädt Session Instanz (Singleton)
	* Falls POST Request gesendet wurde, dann daten laden
	*/
    public function __construct(){
		// Session Objekt (Singleton) holen
		$this->session = SSSession::getInstance();
		$this->article = new SSArticle();
		$this->articleView = new SSArticleView();
		
		$this->articlelist = array();
		if(isset($_GET[self::VAR_NAME_ARTILEID])){
			$this->articleId = $_GET[self::VAR_NAME_ARTILEID];
		}
		
		// Form Felder mit Values (User Input) laden aus POST Variable
		// Die Daten werden nach FORM_ID filtriert, damit Daten von
		// diese Formular geladen werden
		$this->formPropertiesAndValues = SSHelper::getPostByFormId(SSArticleView::FORM_ID);
    }
	
	/*
	* Artikel Detail/List starten
	* Falls ArtikelID gesetzt wurde
	*   dann Daten von dieser Artikel aus DB holen
	* Falls kein ArtikelID sondern Kategorie ID gesetzt wurde
	*   dann alle Artikel der Kategorie holen
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
	
	/*
	* Kategorie ID setzen, für Artikelliste Ausgabe
	* param $categoryId
	*/
	public function setCategoryId($categoryId){
		$this->categoryId = $categoryId;
	}
	
	/*
	* Detailansicht, falls keine Artikelliste
	* ansonsten Artikelliste ausgeben
	*/
	public function displayView(){
		$currency = SSHelper::getSetting('currency');
		$mwst = SSHelper::getSetting('mwst');
		
		$params = array();
		$params['currency'] = $currency;
		$params['mwst'] = $mwst;
		$params['action'] = self::ACTION_ADD_TO_CART;
		
		if(sizeof($this->articlelist) > 0){
			$params['articles'] = array();
			$params['label_detail'] = SSHelper::i18l('detail');
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
		}else{
			$params['label_goback'] = SSHelper::i18l('label_goback');
			$params['id'] = $this->article->get('id');
			$params['no'] = $this->article->get('no');
			$params['title'] = $this->article->get('title');
			$params['description'] = $this->article->get('description');
			$params['price'] = $this->article->formatPrice($this->article->get('price'));
			$params['url'] = rex_getUrl($REX['ARTICLE_ID'], $REX['CLANG_ID']);
			$params['imgs'] = explode(',', $this->article->get('images'));
			$params['label_submit'] = SSHelper::i18l('label_addtocart');
			$params['label_qty'] = SSHelper::i18l('label_qty');
			$this->articleView->displayDetailHtml($params);
		}
	}
}