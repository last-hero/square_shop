<?php
	/**
	*  TODO
	*  Importieren von Daten über CSV Dateien
	*/
class SSImport {	
	/**
	* Test Daten importieren
	*/
	public static function importSamples(){
		global $REX;
		$query = array();
		$files = array('category.sql', 'article.sql', 'customer.sql');
		for($i=0; $i<sizeof($files); $i++){
			$file = $REX['INCLUDE_PATH'].'/addons/square_shop/test/'.$files[$i];
			if(is_file($file)){
				$query[] = file_get_contents($file);
			}
		}
		
		SSDBSQL::executeSql($query, false);
		
		
		// Order einfügen
		$query = 'SELECT c.id as customer_id, NOW() as no, NOW() as date , c.title, c.firstname, c.lastname, c.street, c.zip, c.city, c.telephone, c.email, c.title as d_title, c.firstname as d_firstname, c.lastname as d_lastname, c.street as d_street, c.zip as d_zip, c.city as d_city, c.telephone as d_telephone, c.email as d_email, NOW() as createdate FROM rex_square_shop_customer c LIMIT 30';
		$res = SSDBSQL::executeSql($query, false);
		
			$query = 'INSERT INTO `rex_square_shop_order` (`customer_id`, no, date, billing_title, billing_firstname, billing_lastname, billing_street, billing_zip, billing_city, billing_telephone, billing_email, delivery_title, delivery_firstname, delivery_lastname, delivery_street, delivery_zip, delivery_city, delivery_telephone, delivery_email,createdate) VALUES ';
		for($x=0; $x<sizeof($res); $x++){
			$query .= '("'.implode('", "', $res[$x]).'"), ';
		}
		$query = array(substr($query, 0, -2));
		$res = SSDBSQL::executeSql($query, false);
		
		
		
		// Order Item einfügen
		$query = 'SELECT id FROM rex_square_shop_order';
		$resorder = SSDBSQL::executeSql($query, false);
		foreach($resorder as $order){
			$q = 'INSERT INTO `rex_square_shop_order_item` (order_id, no, title, description, price, images, qty) VALUES ';
			$order_id = $order['id'];
			for($x=0; $x<rand(1,10); $x++){
				$query = 'SELECT no, title, description, price, images, FLOOR(1+RAND()*10) FROM rex_square_shop_article ORDER BY RAND() LIMIT 1';
				$res = SSDBSQL::executeSql($query, false);
				$q .= '("'.$order_id.'", "'.implode('", "', $res[0]).'"), ';
			}
			$q = array(substr($q, 0, -2));
			$res = SSDBSQL::executeSql($q, false);
		}
		// to do exeption
		return true;
	}
}

