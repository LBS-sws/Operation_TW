<?php
$htmlTrHref = Yii::app()->createUrl('cargoCostUser/index',array('username'=>$this->record['username'],'year'=>$this->model->year,'month'=>$this->model->month));
echo "<tr class='clickable-row' data-href='$htmlTrHref'>";
?>
	<td><?php echo TbHtml::link("<span class='glyphicon glyphicon-eye-open'></span>",$htmlTrHref) ?></td>
	<td><?php echo $this->record['lcu']; ?></td>
    <td><?php echo $this->record['city']; ?></td>
    <td><?php echo $this->record['total_price']; ?></td>
</tr>