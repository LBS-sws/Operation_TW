<tr class='clickable-row' data-href='<?php echo $this->getLink('YG05', 'goodsfa/edit', 'goodsfa/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('YG05', 'goodsfa/edit', 'goodsfa/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['goods_code']; ?></td>
	<td><?php echo $this->record['name']; ?></td>
	<td><?php echo $this->record['type']; ?></td>
	<td><?php echo $this->record['unit']; ?></td>
	<td><?php echo sprintf("%.2f", $this->record['price']); ?></td>
</tr>
