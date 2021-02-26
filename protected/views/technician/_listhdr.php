<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('lcd').$this->drawOrderArrow('lcd'),'#',$this->createOrderLink('technician-list','lcd'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('order_code').$this->drawOrderArrow('order_code'),'#',$this->createOrderLink('technician-list','order_code'))
        ;
        ?>
    </th>
    <th>
        <?php
            echo "<a href='#'>".Yii::t("procurement","Goods List")."</a>";
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('status').$this->drawOrderArrow('status'),'#',$this->createOrderLink('technician-list','status'))
        ;
        ?>
    </th>
</tr>
