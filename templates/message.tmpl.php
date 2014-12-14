	<ul class="ss-message ss-<?=$msg_type?>">
		<li>
<?
	if(strlen(trim($label_title))):
?>
        	<h2><?=$label_title?></h2>
<?
	endif;
?>
        	<?=$label_text?>           
        </li>
<?
	for($x=0; $x<sizeof($label_errors); $x++):
		$propertyLabel = $label_errors[$x]['label'];
		foreach($label_errors[$x]['label_errors'] as $label_error):
?>
		<li>
        	<?=$propertyLabel?>
            <ul>
				<li><?=$label_error?></li>
			</ul>
        </li>
<?
		endforeach;
	endfor;
?> 
	</ul>