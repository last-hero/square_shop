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




CSS - Beispiel
--------------------------------
```css
/* --- SS Form --------------------------------------------------------------------- */
*{
	box-sizing: border-box;
	moz-box-sizing: border-box;
}
h1{
	margin-bottom: 30px;
}
h2{
	margin-bottom: 15px;
}
.ss-form-outer{
	position: relative;
}
.ss-form-outer{
	position: relative;
}
.ss-form-outer .ss-form-goback input{
	position: absolute;
	left:0;
	bottom:3px;
	margin:0;
}
form.ss-form p{
	display: inline-block;
	width: 100%;
}
form.ss-form p{
	margin-bottom:5px;
}
form.ss-form p.error label.error{
	color: #FDDFDF;
	margin-left:30%;
}
form.ss-form p label{
	display: inline-block;
	width: 30%;
	float: left;
	cursor: pointer;
	padding-right: 10px;
}
form.ss-form p.required label{
	font-weight: 800;
}
form.ss-form p label.ss-error{
	width: 80%;
}
form.ss-form p label.error{
	width: 65%;
}
form.ss-form input, 
form.ss-form select, 
form.ss-form textarea{
	display: inline-block;
	width: auto;
	border: 1px solid black;
	height: 20px;
	padding:0 5px;
	margin:0;
}
form.ss-form p input, 
form.ss-form p select, 
form.ss-form p textarea{
	float: left;
	display: inline-block;
	width: 65%;
	border: 1px solid black;
	height: 20px;
	padding:0;
	margin:0;
}
form.ss-form p select{
	height: auto;
}
form.ss-form p select[size=1]{
	height: 22px;
}
form.ss-form p input.error, 
form.ss-form p select.error, 
form.ss-form p textarea.error{
	background-color: #FDDFDF;
	border: 1px solid #FDDFDF;
}
form.ss-form p.ss-submit{
	text-align: right;
	width: 95%;
}
form.ss-form p.ss-submit input{
	width: auto;
	float: right;
	cursor: pointer;
}
form.ss-form input.ss-submit{
	width: auto;
	cursor: pointer;
	margin-left:10px;
	padding: 0 10px;
}
.ss-cart form.ss-form input.ss-submit{
	margin-left:0px;
	padding: 0 5px;
}
form.ss-form p.ss-submit label{
	width: 95%;
}
form.ss-form span.info{
	font-size:11px;
}

/* --- SS Form im Header --------------------------------------------------------------------- */
#header #login{
	position: absolute; 
	left:0; 
	bottom: 0px;
}
#header #login form.ss-form p{
	text-align: left;
	width: auto;
	margin:0;
	padding:0;
}
#header #login form.ss-form p label{
	display: block;
	clear: both;
	font-size:12px;
	margin:0;
	padding:0;
}
#header #login form.ss-form p input{
	display: block;
	clear: both;
	width: 100%;
}

/* --- SS Article Detail --------------------------------------------------------------------- */
article.ss-article-detail aside{
	padding:0;
	margin:0;
	width: 50%;
	float: left;
}
article.ss-article-detail figure{
	padding:0;
	margin:0;
	width: 45%;
	float: right;
}
article.ss-article-detail img{
	width: 100%;
}

/* --- SS Article Detail Form --------------------------------------------------------------------- */
article.ss-article-detail form{
	width: 100%;
	display: block;
}
article.ss-article-detail form input{
	width: 10%;
}
article.ss-article-detail form input.ss-submit{
	width: auto;
	cursor: pointer;
}

/* --- SS Form Cart --------------------------------------------------------------------- */
form.ss-form-update_art input{
	width: 20%;
}
form.ss-form input#ss-qty{
	width: 30px;
	text-align: center;
}
/* --- Warenkorb --------------------------------------------------------------------- */
table td
, table th{
	box-sizing: border-box;
	vertical-align: middle;
	padding: 0;
}
table td.ss-img{
	width: 50px;
	padding:0;
}
table td.ss-artno
, table td.ss-title
, table td.ss-price
, table td.ss-qty
, table td.ss-subtotal
, table td.ss-delfromcart{
	width: 50px;
	padding:0;
}
table td.ss-subtotal
, table td.ss-price
, table td.ss-qty{
	white-space:nowrap;
}
table td.ss-delfromcart{
	text-align: right;
}
table td.ss-delfromcart form{
	float: right;
	width: 100%;
}
table td.ss-qty form{
	float: right;
}
table tr:nth-child(2n+1) td{
	background-color: rgba(0,0,0,0.05);
}
table tr th{
	background-color: rgba(0,0,0,0.1);
}
table tr:first-child th
, table tr:last-child th{
	padding: 10px 0;
}
/* --- SS Article List --------------------------------------------------------------------- */

ul.ss-article-list{
	padding:0;
	margin:0;
	list-style:none;
}
ul.ss-article-list li{
	list-style:none;
	padding:0;
	margin:0;
	margin-right: 10px;
	float: left;
	display: inline-block;
	width: 30%;
	height: 200px;
	max-height: 200px;
	min-height: 200px;
	position: relative;
	margin-bottom: 50px;
	background-color: rgba(0,0,0,0.2);
	overflow: hidden;
}
ul.ss-article-list li p a.detail{
	position: absolute;
	right:0;
	bottom:0;
	background-color: rgba(0,0,0,0.8);
	text-decoration: none;
	padding-right: 5px;
	padding-left: 10px;
	z-index: 3;
}
ul.ss-article-list li p span.price{
	position: absolute;
	left:0;
	bottom:0;
	background-color: rgba(0,0,0,0.8);
	padding-right: 10px;
	padding-left: 5px;
	z-index: 3;
}
ul.ss-article-list li p span.no{
	position: absolute;
	left:0;
	top:10px;
	background-color: rgba(0,0,0,0.8);
	padding-right: 10px;
	padding-left: 5px;
	z-index: 3;
}
ul.ss-article-list li h2{
	position: absolute;
	left:0;
	bottom:30px;
	background-color: rgba(0,0,0,0.6);
	padding-right: 10px;
	padding-left: 5px;
	width: 100%;
}
ul.ss-article-list li:nth-child(3n+3){
}
ul.ss-article-list li img{
	float: left;
	display: inline-block;
	width: 100%;
	z-index: 1;
}

/* --- Messages --------------------------------------------------------------------- */
ul.ss-message{
	background-color: rgba(0,0,0,0.5);
	background-color: rgba(0,0,0,0.1);
	border-left: 5px solid rgba(255,255,255,0.2);
	padding: 15px 5px;
	width: 100%;
	list-style: none;
	margin: 20px 0;
}
ul.ss-message li{
	font-size: 12px;
	list-style: none;
	margin:0;
}
ul.ss-message li ul li{
	list-style: disc;
	font-size: 11px;
}

ul.ss-success{
	border-left: 5px solid rgba(0,255,0,0.2);
}
ul.ss-error{
	border-left: 5px solid rgba(255,0,0,0.2);
}

/* --- Checkout --------------------------------------------------------------------- */
ul.ss-checkout
, ul.ss-checkout li{
	list-style: none;
	padding:0;
	margin:0;
	width: 100%;
}
ul.ss-checkout li{
	display: inline-block;
	margin-bottom: 20px;
	width: auto;
}
ul.ss-checkout li form{
	display: inline-block;
	width: auto;
}
ul.ss-checkout li a{
	text-decoration: none;
}
ul.ss-checkout li h2{
	background-color: rgba(0,0,0,0.5);
	display: inline-block;
	width: auto;
	font-weight: 100;
}
ul.ss-checkout li h2 span{
	background-color: rgba(0,0,0,0.5);
	padding:10px 15px;
	color: #90B0BF;
	font-size: 16px;
	border:0;
	margin:0;
}
ul.ss-checkout li input{
	background-color: rgba(0,0,0,0.5);
	padding:10px 15px;
	height: auto;
	color: #90B0BF;
	font-size: 16px;
	border:0;
	margin:0;
}
ul.ss-checkout li h2 strong{
	margin:0 10px;
}
```