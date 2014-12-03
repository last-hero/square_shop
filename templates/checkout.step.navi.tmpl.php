    <ul class="ss-checkout ss-checkout-steps">
<?
	for($x=1; $x<=5; $x++):
?>
    	<li>
<?
		if($step_active == $x):
?>        
			<h2><span><?=$x?></span><strong><?=$label_steps[$x]?></strong></h2>
<?
		elseif($step_active > $x):
?> 
			<h2><a href="<?=$url_steps[$x]?>" target="_self" title=""><span><?=$x?></span></a></h2>
<?
		else:
?> 
			<h2><span><?=$x?></span></h2>
<?
		endif;
?> 
        </li>
<?
	endfor;
?>
    </ul>