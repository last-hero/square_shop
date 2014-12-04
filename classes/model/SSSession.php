<?php
/** @file SSSession.php
 *  @brief Session Handling
 *
 *  Diese Klasse dient für das Session Handling
 *  + fungiert als Singleton
 *
 *  @author Gobi Selva
 *  @author http://www.square.ch
 *  @author https://github.com/last-hero/square_shop
 *
 *  @bug Keine Bugs bekannt.
 */

class SSSession {
	/**
	 * Singleton-Objekt wird in dieser Variable abgelegt.
	 */
	protected static $objInstance;
	
	/**
	 * Enthält alle geladenen Properties und Values.
	 */
	protected $data;
	
	/** @brief Konstruktor
	 *
	 *  Lädt alle Session Variablen von dieses AddOn 
	 *  aus $_SESSION
	 */
	protected function __construct(){
		$this->data = (array) $_SESSION['CUSTOM']['square_shop'];
	}
	
	/** @brief Destruktor
	 *
	 *  Speichert alle Session Variablen von dieses AddOn 
	 *  in $_SESSION
	 */
	public function __destruct(){
		$_SESSION['CUSTOM']['square_shop'] = $this->data;
	}
	
	/** @brief Instanz holen - Singleton
	 *
	 *  Gibt die aktuelle Instanz zurück,
	 *  falls keine existiert, dann wird sie
	 *  erstellt.
	 *
	 *  @return SSSession Object
	 */
	public static function getInstance()
	{
		if (static::$objInstance === null)
		{
			static::$objInstance = new static();
		}
		return static::$objInstance;
	}
	
	/** @brief Wert holen
	 *
	 *  Gibt einen Wert nach Propertyname zurück.
	 *  
	 *  @param (string) $key
	 *  @return (mixed) $value
	 *  
	 *  @see set()
	 *  @see remove()
	 */
	public function get($key){
		return $this->data[$key];
	}
	
	/** @brief Wert setzen
	 *
	 *  Setzt einen Wert nach Propertyname.
	 *  
	 *  @param (string) $key
	 *  @param (mixed) $val
	 *  
	 *  @see get()
	 *  @see remove()
	 */
	public function set($key, $val){
		$this->data[$key] = $val;
	}
	
	/** @brief Wert löschen
	 *
	 *  Löscht einen Wert nach Propertyname.
	 *  
	 *  @param (string) $key
	 *  
	 *  @see set()
	 *  @see get()
	 */
	public function remove($key){
		unset($this->data[$key]);
	}
}