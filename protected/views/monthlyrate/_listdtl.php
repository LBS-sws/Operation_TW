<tr class='clickable-row' data-href='<?php echo $this->getLink('YA04', 'monthlyrate/edit', 'monthlyrate/view', array('index'=>$this->record['city']));?>'>
	<td><?php echo $this->drawEditButton('YA04', 'monthlyrate/edit', 'monthlyrate/view', array('index'=>$this->record['city'])); ?></td>
	<td><?php echo $this->record['city_name']; ?></td>
	<td><?php echo $this->record['rate']; ?></td>
</tr>
