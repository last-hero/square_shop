	<ul class="ss-message ss-<?=$msg_type?>">
		<li>
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
    
   <!-- <ul class="messages"><li class="success-msg"><ul><li><span>Gut zum Druck auf Originalpapier wurde in den Warenkorb gelegt.</span></li></ul></li></ul>-->