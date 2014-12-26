<?php
/** @file SSCartView.php
 *  @brief View Klasse
 *
 *  Dies Klasse enthält alle Views für den Warenkorb
 *
 *  Die Templates welche benötigt werden sind
 *  im Verzeichnis /templates vorhanden.
 *
 *  @author Gobi Selva
 *  @author http://www.square.ch
 *  @author https://github.com/last-hero/square_shop
 */

class SSCartView extends SSView{
	/**
	 * @see SSView::$FORM_ID
	*/
	const FORM_ID = 'cart';
	protected $FORM_ID = self::FORM_ID;
	
	/** @brief Warenkorb anzeigen
	 *
	 *  Warenkorb mit Artikeln (Menge anpassen, Artikel entfernen, Warenkorb leeren, Zur Kasse gehen)
	 *
	 *  Benötigte Dateien: /templates/cart.complete.tmpl.php
	 *
	 *  @param $params
	 */
	public function displayCartHtml($params = array()){
		$params['FORM_ID'] = self::FORM_ID;
		try{			
			echo SSGUI::parse(self::FORM_ID.'.complete.tmpl.php', $params);
		}catch(SSException $e) {
			echo $e;
		}
	}
}

