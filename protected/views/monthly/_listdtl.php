<tr class='clickable-row' data-href='<?php echo $this->getLink('YA01', 'monthly/edit', 'monthly/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('YA01', 'monthly/edit', 'monthly/view', array('index'=>$this->record['id'])); ?></td>
<?php if (!Yii::app()->user->isSingleCity()) : ?>
	<td><?php echo $this->record['city_name']; ?></td>
<?php endif ?>
	<td><?php echo $this->record['year_no']; ?></td>
	<td><?php echo $this->record['month_no']; ?></td>
	<td><?php echo $this->record['wfstatusdesc']; ?></td>
</tr>
