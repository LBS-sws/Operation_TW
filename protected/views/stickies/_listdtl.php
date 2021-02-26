<tr class='clickable-row' data-href='<?php echo $this->getLink('YG02', 'stickies/edit', 'stickies/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('YG02', 'stickies/edit', 'stickies/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['name']; ?></td>
	<td><?php echo $this->record['content']; ?></td>
</tr>
