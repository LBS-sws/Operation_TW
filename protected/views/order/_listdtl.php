<?php
$htmlTrHref = $this->getLink('YD03', 'order/edit', 'order/view', array('index'=>$this->record['id']));
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
    case "expired":
        echo "<tr class='clickable-row text-gray' data-href='$htmlTrHref'>";
        break;
    default:
        echo "<tr class='clickable-row' data-href='$htmlTrHref'>";
}
?>
	<td><?php echo $this->drawEditButton('YD03', 'order/edit', 'order/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['lcd']; ?></td>
	<td><?php echo $this->record['order_code']; ?></td>
	<td><?php echo $this->record['activity_id']; ?></td>
	<td><?php echo $this->record['order_class']; ?></td>
	<td>
        <?php
        $con_num = 0;
            foreach ($this->record["goods_list"] as $goods){
                $con_num++;
                if($con_num == 3){
                    echo ".......";
                    break;
                }
                echo $goods["name"].'  ×  '.$goods["goods_num"]."<br>";
            }
        ?>
    </td>
    <td><?php echo OrderList::printOrderStatus($this->record['status'],$this->record['status_type']);?></td>
</tr>