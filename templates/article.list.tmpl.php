        <ul>
<?
		foreach($articles as $art):
?>
            <li>
                <img src="" />
                <h2><?=$art['title']?></h2>
                <p><?=$art['price']?> <?=$art['currency']?></p>
            </li>
<?
		endforeach;
?>
        </ul>