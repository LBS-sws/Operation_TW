<tr class='clickable-row <?php echo $this->record["status"]["style"]?>' data-href='<?php echo $this->getLink('YD06', 'areaAudit/edit', 'areaAudit/view', array('index'=>$this->record['id']));?>'>
    <td><?php echo $this->drawEditButton('YD06', 'areaAudit/edit', 'areaAudit/view', array('index'=>$this->record['id'])); ?></td>
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
    <td><?php echo $this->record['status']['status']; ?></td>
</tr>
