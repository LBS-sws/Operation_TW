<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('lcd').$this->drawOrderArrow('lcd'),'#',$this->createOrderLink('areaAudit-list','lcd'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('order_code').$this->drawOrderArrow('order_code'),'#',$this->createOrderLink('areaAudit-list','order_code'))
        ;
        ?>
    </th>
    <th>
        <?php
        echo "<a href='#'>".Yii::t("procurement","Order of Activity")."</a>";
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('order_class').$this->drawOrderArrow('order_class'),'#',$this->createOrderLink('areaAudit-list','order_class'))
        ;
        ?>
    </th>
    <th>
        <?php
        echo "<a href='#'>".Yii::t("procurement","Goods List")."</a>";
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('status').$this->drawOrderArrow('status'),'#',$this->createOrderLink('areaAudit-list','status'))
        ;
        ?>
    </th>
</tr>
