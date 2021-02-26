<?php
$href=$this->getLink('YD04', 'order/new', '', array('index'=>$this->record['id']));
if($this->record['activity_status'] == "Run"){
    echo "<tr class='clickable-row text-primary' data-href='$href'>";
    echo "<td>".TbHtml::link('<span class="glyphicon glyphicon-plus"></span> ', $href)."</td>";
}elseif($this->record['activity_status'] == "Wait"){
    echo "<tr class='text-yellow'>";
    echo "<td></td>";
}else{
    echo "<tr class='text-warning'>";
    echo "<td></td>";
}
?>
	<td><?php echo $this->record['activity_code']; ?></td>
	<td><?php echo $this->record['activity_title']; ?></td>
	<td><?php echo $this->record['order_class']; ?></td>
	<td><?php echo $this->record['start_time']; ?></td>
	<td><?php echo $this->record['end_time']; ?></td>
	<td><?php echo $this->record['num']; ?></td>
<td><?php echo Yii::t("procurement",$this->record['activity_status']); ?></td>
</tr>
