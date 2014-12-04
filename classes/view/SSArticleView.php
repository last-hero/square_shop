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
 *
 *  @bug Keine Bugs bekannt.
 */
 
class SSArticleView {
	// Form Array Key Name
	const FORM_ID = 'article';
	
	/**
	* Artikel Detail Anzeige
	*/
	public function displayDetailHtml($params = array()){
		$params['FORM_ID'] = SSCartView::FORM_ID;
		try{			
			echo SSGUI::parse(SSArticle::TABLE.'.detail.tmpl.php', $params);
		}catch(SSException $e) {
			echo $e;
		}
	}
	
	/**
	* Artikel Liste Anzeige
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

