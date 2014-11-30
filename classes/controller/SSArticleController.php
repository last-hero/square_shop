<?php
class SSArticleController {	
	// Singleton --> Session Objekt
	private $session;
	
	// SSArticle
	private $article;
	
	// Array of SSArticle Objects
	private $articlelist;
	
	// SSArticleView
	private $articleView;
	
	// categoryId
	private $categoryId;
	
	// articleId
	private $articleId;
	
	
	/*
	* Konstruktor: lädt Session Instanz (Singleton)
	* Falls POST Request abgeschickt wurde, dann daten laden
	*/
    public function __construct(){
		// Session Objekt (Singleton) holen
		$this->session = SSSession::getInstance();
		$this->article = new SSArticle();
		$this->articleView = new SSArticleView();
		
		$this->articlelist = array();
    }
	
	/*
	* Login/Logout Funktion starten
	*/
	public function invoke(){
		if((int)$this->categoryId > 0){
			$articles = $this->article->getByCategoryId($this->categoryId);
			foreach($articles as $article){
				$artObj = new SSArticle();
				$artObj->set($artObj->getClearedUnknownProperties($article));
				$this->articlelist[] = $artObj;
			}
		}else{
			$this->article->loadById($this->articleId);
		}
		
		$this->displayView();
	}
	
	/*
	*/
	public function setCategoryId($categoryId){
		$this->categoryId = $categoryId;
	}
	
	/*
	* Falls User nicht angemeldet: Login-Maske anzeigen
	* Falls User angemeldet: Logout-Maske anzeigen
	*/
	public function displayView(){
		$currency = SSHelper::getSetting('currency');
		$mwst = SSHelper::getSetting('mwst');
		
		$params = array();
		if(sizeof($this->articlelist) > 0){
			$params['articles'] = array();
			foreach($this->articlelist as $article){
				$params['articles'][] = array(
					'title' => $article->get('title')
					, 'price' => $article->get('price')
					, 'currency' => $currency
					, 'mwst' => $mwst
					, 'imgfiles' => $article->get('images')
				);
			}
			$this->articleView->displayListHtml($params);
		}else{
			$params['title'] = $this->article->get('title');
			$params['description'] = $this->article->get('description');
			$params['price'] = $this->article->get('price');
			$params['currency'] = $currency;
			$params['mwst'] = $mwst;
			$params['imgfiles'] = $this->article->get('images');
			$this->articleView->displayDetailHtml($params);
		}
	}
}