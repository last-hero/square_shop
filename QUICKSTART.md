Quickstart
============================

Artikel nach Kategorie einbinden
--------------------------------

Code für Moduleingabe
```php
<?
	$SSCategory = new SSCategory();
	$cats = $SSCategory->getCategories();
?>
<strong style="float:left;width:120px;line-height:23px;">Kategorie</strong>
<select name='VALUE[3]' style="float-left;line-height:21px;height:23px;">
	<option value=""<? if('REX_VALUE[3]'==''){ ?> selected="selected"<? } ?>>Bitte wählen</option>
<?
	foreach($cats as $cat):
?>
	<option value="<?=$cat['id']?>"<? if('REX_VALUE[3]'==$cat['id']){ ?> selected="selected"<? } ?>><?=$cat['title']?></option>
<?
	endforeach;
?>
</select>
```

Code für Modulausgabe
```php
<?
	$SSCartController = new SSCartController();
	$SSCartController->cartHandler();
	$SSCartController->messageHandler();
	
	$category_id = 'REX_VALUE[3]';
	
	$SSArticleController = new SSArticleController($category_id);
	$SSArticleController->setCategoryId($category_id);
	$SSArticleController->invoke();
?>
```


Login- / Logout-Maske einbinden
--------------------------------

Code für Modulausgabe
```php
<?	
	$customerLoginController = new SSCustomerLoginController();
	$customerLoginController->invoke();
?>
```




Registrierungsmaske einbinden
--------------------------------

Code für Modulausgabe
```php
<?
	$SSCustomerRegisterController = new SSCustomerRegisterController();
	$SSCustomerRegisterController->invoke();
?>
```




Warenkorb einbinden
--------------------------------

Code für Modulausgabe
```php
<?
	$SSCartController = new SSCartController();
	// Artikel ID auf der Checkout eingebunden ist
	$SSCartController->setCheckoutPageId(/*REX_ARTICLE_ID*/); 
	$SSCartController->invoke();
?>
```




Checkout einbinden
--------------------------------

Code für Modulausgabe
```php
<?	
	$SSCheckoutController = new SSCheckoutController();
	$SSCheckoutController->invoke();
?>
```