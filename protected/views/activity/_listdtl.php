<?php
$href=$this->getLink('YS03', 'activity/edit', 'activity/view', array('index'=>$this->record['id']));
if($this->record['activity_status'] == "Run"){
    echo "<tr class='clickable-row text-primary' data-href='$href'>";
}elseif($this->record['activity_status'] == "Wait"){
    echo "<tr class='clickable-row text-yellow' data-href='$href'>";
}else{
    echo "<tr class='clickable-row text-warning' data-href='$href'>";
}
?>
	<td><?php echo $this->drawEditButton('YS03', 'activity/edit', 'activity/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['activity_code']; ?></td>
	<td><?php echo $this->record['activity_title']; ?></td>
	<td><?php echo $this->record['order_class']; ?></td>
	<td><?php echo $this->record['start_time']; ?></td>
	<td><?php echo $this->record['end_time']; ?></td>
	<td><?php echo $this->record['num']; ?></td>
	<td><?php echo Yii::t("procurement",$this->record['activity_status']); ?></td>
</tr>
