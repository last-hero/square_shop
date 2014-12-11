<?php
/** @file SSArticleView.php
 *  @brief View Klasse
 *
 *  Diese Klasse dient zum Erstellen von
 *  Masken für Artikeln
 *
 *  Die Templates welche benötigt werden sind
 *  im Verzeichnis /templates vorhanden.
 *
 *  @author Gobi Selva
 *  @author http://www.square.ch
 *  @author https://github.com/last-hero/square_shop
 */
 
class SSArticleView extends SSView{
	/**
	* siehe Parent
	*/
	const FORM_ID = 'article';
	protected $FORM_ID = self::FORM_ID;
	
	/**
	* Artikel Detail Anzeige
	*/
	
	/** @brief Detailansicht
	 *
	 *  Detailansicht generieren und anzeigen
	 *
	 *  Benötigte Dateien: /templates/article.detail.tmpl.php
	 *
	 *  @param $params: SSHelper::getFormProperties
	 *
	 *  @see SSHelper::getFormProperties
	 */
	public function displayDetailHtml($params = array()){
		$params['FORM_ID'] = SSCartView::FORM_ID;
		try{			
			echo SSGUI::parse(SSArticle::TABLE.'.detail.tmpl.php', $params);
		}catch(SSException $e) {
			echo $e;
		}
	}
	
	/** @brief Listenansicht
	 *
	 *  Listenansicht generieren und anzeigen
	 *
	 *  Benötigte Dateien: /templates/article.list.tmpl.php
	 *
	 *  @param $params: SSHelper::getFormProperties
	 *
	 *  @see SSHelper::getFormProperties
	 */
	public function displayListHtml($params = array()){
		$params['FORM_ID'] = self::FORM_ID;
		try{			
			echo SSGUI::parse(SSArticle::TABLE.'.list.tmpl.php', $params);
		}catch(SSException $e) {
			echo $e;
		}
	}
}

