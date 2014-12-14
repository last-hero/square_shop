Online Shop AddOn für REDAXO 4
============================

Dieses Addon bietet die Möglichkeit ein sehr einfaches Online-Shop
zu betreiben.


Features im Backend
-------------------
* Artikelverwaltung
	* Einträge erfassen / ändern / löschen
* Kategorieverwaltung
	* Einträge erfassen / ändern / löschen
* Kundenverwaltung
	* Einträge erfassen / ändern / löschen
* Bestellverwaltung
	* Einträge einsehen




```code
on doing
```

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



Hinweise
--------

* Getestet mit REDAXO 4.6
* Frontend-Implementierung folgt demnächst


Changelog
---------

siehe [CHANGELOG.md](CHANGELOG.md)

Credits
-------

* [Parsedown](http://parsedown.org/) Class by Emanuil Rusev
* [babelfish](https://github.com/RexDude/babelfish) AddOn by RexDude


