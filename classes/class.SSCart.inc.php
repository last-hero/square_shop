<?php
class SSCart implements Iterator, Countable {
	const SESSION_KEY		   = 'SSCart';
	
	const ERROR_ID_NOT_FOUND    = 5000;
	
	protected $items 			= array();
	protected $pos 			  = 0;
	protected $ids 			  = array();
	
	/**
	* Konstruktor
	*/
    function __construct(){
		$this->items = array();
		$this->ids = array();
    }
	
	/**
	* Fügt Artikel zum Warenkorb
	* param $item: Article Objekt
	*/
	public function addItem(SSItem $item){
		// (Unique) Artikel ID holen
		$id = $item->getId();
		
		// Exception Throw, falls kein ID gefunden
		if(!$id){
			throw new SSException('Article ID not found', self::ERROR_ID_NOT_FOUND);
		}
		
		// Zum Cart neuhinzufügen oder aktualisieren
		if (isset($this->items[$id])){
			$this->updateItem($item, $this->items[$item]['qty'] + 1);
		} else {
			$this->items[$id] = array('item' => $item, 'qty' => 1);
			$this->ids[] = $id; // ID auch abspeichern
		}
	}
	
	/**
	* Fügt di
	* param $item: Article Objekt
	* param $qty: Menge
	*/
	public function updateItem(SSItem $item, $qty){
		// (Unique) Artikel ID holen
		$id = $item->getId();

		// Löschen oder Aktualisieren, je nach Menge
		if($qty === 0){
			$this->deleteItem($item);
		}elseif(($qty > 0) && ($qty != $this->items[$id]['qty'])){
			$this->items[$id]['qty'] = $qty;
		}
	}
	
	/**
	* Fügt di
	* param $item: Article Objekt
	* param $qty: Menge
	*/
	public function deleteItem(SSItem $item){
		// (Unique) Artikel ID holen
		$id = $item->getId();

		// Artikel entfernen
		if (isset($this->items[$id])){
			unset($this->items[$id]);
	
			// Artikel ID ebenfalls entfernen
			$index = array_search($id, $this->ids);
			unset($this->ids[$index]);

			// Array neu sortieren, damit die Lücken gefüllt werden
			$this->ids = array_values($this->ids);
	
		}
		
	}
}

