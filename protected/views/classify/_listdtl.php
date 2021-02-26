<tr class='clickable-row' data-href='<?php echo $this->getLink('YG03', 'classify/edit', 'classify/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('YG03', 'classify/edit', 'classify/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['name']; ?></td>
	<td><?php echo Yii::t("procurement",$this->record['class_type']); ?></td>
	<td><?php echo $this->record['level']; ?></td>
</tr>
