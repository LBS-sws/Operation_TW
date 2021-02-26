<tr class='clickable-row' data-href='<?php echo $this->getLink('YG06', 'rules/edit', 'rules/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('YG06', 'rules/edit', 'rules/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['name']; ?></td>
	<td><?php echo $this->record['multiple']; ?></td>
	<td><?php echo $this->record['max']; ?></td>
	<td><?php echo $this->record['min']; ?></td>
</tr>
