<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('activity_code').$this->drawOrderArrow('activity_code'),'#',$this->createOrderLink('purchase-list','activity_code'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('activity_title').$this->drawOrderArrow('activity_title'),'#',$this->createOrderLink('purchase-list','activity_title'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('start_time').$this->drawOrderArrow('start_time'),'#',$this->createOrderLink('purchase-list','start_time'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('end_time').$this->drawOrderArrow('end_time'),'#',$this->createOrderLink('purchase-list','end_time'))
        ;
        ?>
    </th>
    <th class="text-primary">
        <?php echo Yii::t("procurement","Order Sum");?>
    </th>
    <th class="text-primary">
        <?php echo Yii::t("procurement","Send Num");?>
    </th>
    <th class="text-primary">
        <?php echo Yii::t("procurement","Status");?>
    </th>
    <th></th>
</tr>
