<tr class='clickable-row' data-href='<?php echo $this->getLink('YS05', 'email/edit', 'email/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('YS05', 'email/edit', 'email/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['name']; ?></td>
	<td><?php echo $this->record['email']; ?></td>
</tr>
