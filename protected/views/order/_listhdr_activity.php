<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('activity_code').$this->drawOrderArrow('activity_code'),'#',$this->createOrderLink('order-list','activity_code'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('activity_title').$this->drawOrderArrow('activity_title'),'#',$this->createOrderLink('order-list','activity_title'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('order_class').$this->drawOrderArrow('order_class'),'#',$this->createOrderLink('order-list','order_class'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('start_time').$this->drawOrderArrow('start_time'),'#',$this->createOrderLink('order-list','start_time'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('end_time').$this->drawOrderArrow('end_time'),'#',$this->createOrderLink('order-list','end_time'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('num').$this->drawOrderArrow('num'),'#',$this->createOrderLink('order-list','num'))
        ;
        ?>
    </th>
    <th class="text-primary">
        <?php echo Yii::t("procurement","Status");?>
    </th>
</tr>
