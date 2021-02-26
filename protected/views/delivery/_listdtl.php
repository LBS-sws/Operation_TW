<?php
$htmlTrHref = $this->getLink('YD02', 'delivery/edit', 'delivery/view', array('index'=>$this->record['id']));
switch ($this->record['status']){
    case "sent":
        echo "<tr class='clickable-row text-primary' data-href='$htmlTrHref'>";
        break;
    case "approve":
        echo "<tr class='clickable-row text-green' data-href='$htmlTrHref'>";
        break;
    case "reject":
        echo "<tr class='clickable-row text-danger' data-href='$htmlTrHref'>";
        break;
    case "read":
        echo "<tr class='clickable-row text-yellow' data-href='$htmlTrHref'>";
        break;
    case "finished":
        echo "<tr class='clickable-row text-success' data-href='$htmlTrHref'>";
        break;
    default:
        echo "<tr class='clickable-row' data-href='$htmlTrHref'>";
}
?>
	<td>
        <?php
        if($this->record['status'] == "sent"||$this->record['status'] == "read"){
            echo TbHtml::checkBox("DeliveryList[checkBoxDown][]",false,array("value"=>$this->record['id'],"class"=>"checkBoxDown"));
        }
        ?>
    </td>
	<td><?php echo $this->drawEditButton('YD02', 'delivery/edit', 'delivery/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['lcd']; ?></td>
	<td><?php echo $this->record['lcu']; ?></td>
	<td><?php echo $this->record['order_code']; ?></td>
	<td>
        <?php
        $con_num = 0;
            foreach ($this->record["goods_list"] as $goods){
                $num = ($goods["confirm_num"]===""||$goods["confirm_num"]===null)?floatval($goods["goods_num"]):floatval($goods["confirm_num"]);
                $con_num++;
                if($con_num == 3){
                    echo ".......";
                    break;
                }
                echo $goods["name"].'  ×  '.$num."<br>";
            }
        ?>
    </td>
<?php if (!Yii::app()->user->isSingleCity()): ?>
    <td><?php echo $this->record['city']; ?></td>
<?php endif ?>
    <td><?php echo OrderList::printPurchaseStatus($this->record['status']);?></td>
</tr>