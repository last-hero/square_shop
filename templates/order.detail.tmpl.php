<?
			$order_id = $id;
			
			$table = 'order';
			$table_order_full = SSDBSchema::_getTableAttr($table, 'name', true);
			$query = SSDBSQL::_getSqlDmlQuery(' '.$table_order_full.'.id = '.$order_id.' ', $table, 'detail');
			
			$res_order = SSDBSQL::executeSql($query, false);
			if(is_array($res_order) and sizeof($res_order)==1){
				$res_order = $res_order[0];
			}
			
			
			$table = 'order_item';
			$table_order_full = SSDBSchema::_getTableAttr($table, 'name', true);
			$query = SSDBSQL::_getSqlDmlQuery(' '.$table_order_full.'.order_id = '.$order_id.' ', $table, 'detail');
			$res_order_item = SSDBSQL::executeSql($query, false);
			
			
			$currency = $REX['ADDON']['square_shop']['settings']['currency'];
			$mwst = $REX['ADDON']['square_shop']['settings']['mwst'];
?>
  			<table class="rex-table">
    			<thead>
      				<tr>
                        <th><?=ss_utils::i18l('order')?></th>
      				</tr>
                </thead>
                <tbody>
      				<tr>
                   		<td>
                            <strong style="width:100px;display:inline-block;">BestellNr:</strong> <?=$res_order['no']?><br />
                            <strong style="width:100px;display:inline-block;">Zeit:</strong> <?=$res_order['date']?><br /><br />
                            <strong style="width:100px;display:inline-block;"><?=ss_utils::i18l('label_customer_id')?>:</strong> <?=$res_order['customer_id']?><br />
                            <strong style="width:100px;display:inline-block;"><?=ss_utils::i18l('label_customer_title')?>:</strong> <?=$res_order['customer_title']?><br />
                            <strong style="width:100px;display:inline-block;"><?=ss_utils::i18l('label_customer_firstname')?>:</strong> <?=$res_order['customer_firstname']?><br />
                            <strong style="width:100px;display:inline-block;"><?=ss_utils::i18l('label_customer_lastname')?>:</strong> <?=$res_order['customer_lastname']?><br />
                    	</td>
      				</tr>
                </tbody>
            </table>
            <br /><br />
        
  			<table class="rex-table">
    			<thead>
      				<tr>
                        <th><?=ss_utils::i18l('billing')?></th>
                        <th><?=ss_utils::i18l('delivery')?></th>
      				</tr>
                </thead>
                <tbody>
      				<tr>
                   		<td>
					<? foreach($res_order as $key => $val): ?>
						<? if(substr_count($key, 'billing_')): ?>
                            <strong style="width:100px;display:inline-block;"><?=ss_utils::i18l('label_'.str_replace('billing_', '',$key))?>:</strong> <?=$val?><br />
                        <? endif; ?>
                    <? endforeach; ?>
                    	</td>
                   		<td>
					<? foreach($res_order as $key => $val): ?>
						<? if(substr_count($key, 'delivery_')): ?>
                            <strong style="width:100px;display:inline-block;"><?=ss_utils::i18l('label_'.str_replace('delivery_', '',$key))?>:</strong> <?=$val?><br />
                        <? endif; ?>
                    <? endforeach; ?>
                    	</td>
      				</tr>
                </tbody>
            </table>
            <br /><br />
  			<table class="rex-table">
    			<thead>
      				<tr>
                   		<th colspan="<?=count($res_order_item[0])+2?>"><?=ss_utils::i18l('order_item')?></th>
      				</tr>
                </thead>
                <tbody>
      				<tr>
                        <td style="font-weight:700;">Pos</td>
					<? foreach($res_order_item[0] as $key => $val): ?>
                        <td style="font-weight:700;"><?=$key?></td>
                    <? endforeach; ?>
                        <td style="text-align:right; font-weight:700;"><?=ss_utils::i18l('subtotal')?></td>
      				</tr>
                <? $counter = 1;?>
                <? $total = 0;?>
            	<? foreach($res_order_item as $order_item): ?>
      				<tr>
                        <td><?=$counter++?></td>
					<? foreach($order_item as $key => $val): ?>
                        <td><?=$val?></td>
                    <? endforeach; ?>
					<? 
						$subtotal = (int)$order_item['price'] * (int)$order_item['qty'];
						$total += $subtotal;
                    ?>
                        <td align="right"><?=number_format($subtotal, 2, ',', '&nbsp;')?>&nbsp;<?=$currency?></td>
      				</tr>
            	<? endforeach; ?>
      				<tr>
                   		<td style="font-weight:700;text-align:right;" colspan="<?=count($res_order_item[0])+1?>"><?=ss_utils::i18l('mwst')?></td>
                   		<td style="font-weight:700;text-align:right;" colspan="1"><?=number_format($total/100*$mwst, 2, ',', '&nbsp;')?>&nbsp;<?=$currency?></td>
      				</tr>
      				<tr>
                   		<td style="font-weight:700;text-align:right;" colspan="<?=count($res_order_item[0])+1?>"><?=ss_utils::i18l('total')?></td>
                   		<td style="font-weight:700;text-align:right;" colspan="1"><?=number_format($total, 2, ',', '&nbsp;')?>&nbsp;<?=$currency?></td>
      				</tr>
                </tbody>
            </table>