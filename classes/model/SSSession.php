<?php
#
#
# SSSession
# https://github.com/last-hero/square_shop
#
# (c) Gobi Selva
# http://www.square.ch
#
# Diese Klasse dient für das Session Handling
# + fungiert als Singleton
#
#

class SSSession {
	protected static $objInstance;
	
	protected $data;
	
	/**
	* Konstruktor --> holt alle Session Variable
	*/
	protected function __construct(){
		$this->data = (array) $_SESSION['CUSTOM']['square_shop'];
	}
	
	/**
	* Destruktor --> speichert alle Variable in Session
	*/
	public function __destruct(){
		$_SESSION['CUSTOM']['square_shop'] = $this->data;
	}
	
	/**
	* Singleton --> holt das Objekt
	*/
	public static function getInstance()
	{
		if (static::$objInstance === null)
		{
			static::$objInstance = new static();
		}
		return static::$objInstance;
	}
	
	/**
	* Variable aus Session holen
	* param string $key  Variable Name
	*/
	public function get($key){
		return $this->data[$key];
	}
	
	/**
	* Session Variable erstellen und Wert setzen
	* param string $key  Variable Name
	* param mixed  $val  Wert
	*/
	public function set($key, $val){
		$this->data[$key] = $val;
	}
	
	/**
	* Variable aus Session löschen
	* param string $key  Variable Name
	*/
	public function remove($key){
		unset($this->data[$key]);
	}
}