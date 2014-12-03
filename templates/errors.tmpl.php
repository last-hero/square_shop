	<ul class="ss-error">
<?
	// $formErrors
	// $errorLabels
	foreach($errorLabels as $f):
			
?>
		<li>
        	<?=$f['label']?>
            <ul>
<?
		foreach($f['label_error'] as $errLabel):?>
				<li><?=$errLabel?></li>
<?
		endforeach;
?>
			</ul>
        </li>
<?
	endforeach;
?>
	</ul>