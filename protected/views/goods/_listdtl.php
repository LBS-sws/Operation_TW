<tr class='clickable-row' data-href='<?php echo $this->getLink('YA02', 'goods/edit', 'goods/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('YA02', 'goods/edit', 'goods/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['goods_code']; ?></td>
	<td><?php echo $this->record['name']; ?></td>
	<td><?php echo $this->record['goods_class']; ?></td>
	<td><?php echo $this->record['type']; ?></td>
	<td><?php echo $this->record['unit']; ?></td>
	<td><?php echo $this->record['price']; ?></td>
</tr>
