        <ul class="ss-article-list">
<?
		foreach($articles as $art):
?>
            <li>
<?
			if(count($art['imgs'])):
				foreach($art['imgs'] as $img){
					if(!empty($img)){
						break;
					}
				}
			
?>
            	<img src="index.php?rex_img_type=ss-article-list&rex_img_file=<?=$img?>" />
<?
			endif;
?>
                <h2><?=$art['title']?></h2>
                <p>
                	<span class="no"><?=$art['no']?></span>
                	<br />
                	<span class="price"><?=$currency?> <?=$art['price']?></span>
                	<br />
                    <a class="detail" href="<?=$art['url']?>"><?=$label_detail?></a>
                </p>
            </li>
<?
		endforeach;
?>
        </ul>