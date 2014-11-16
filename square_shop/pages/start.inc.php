<div class="rex-addon-output">
	<h2 class="rex-hl2"><?=ss_utils::i18l('start_title')?></h2>
	<div class="rex-area-content">
        <h2><?=ss_utils::i18l('quicklinks')?></h2>
		<ul>
        <? $list = array('article', 'category', 'order', 'client', 'settings', 'help'); ?>
		<? for($i=0;$i<sizeof($list);$i++): ?>
			<li>
            	<a href="index.php?page=square_shop&subpage=<?=$list[$i]?>">
					<?=ss_utils::i18l($list[$i])?>
                </a>
            </li>
		<? endfor; ?>
			<!--
            <li>
            	<a class="extern" target="_blank" href="http://www.redaxo.org/de/download/addons/?addon_id=1194">
					<?=ss_utils::i18l('extlink_to_addon')?>
                </a>
           	</li>
            -->
		</ul>
		<br />
	</div>
	<div class="rex-area-content">
    	<table>
        	<tr>
            	
            </tr>
        </table>
        <a href="http://square.ch" target="_blank" style="position: absolute; right:20px; bottom: 20px;">
            <img src="../<?php echo $REX['MEDIA_ADDON_DIR']; ?>/square_shop/logo.png" alt="" />
        </a>
		<br />
	</div>
</div>