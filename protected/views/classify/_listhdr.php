<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('name'),'#',$this->createOrderLink('classify-list','name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('class_type').$this->drawOrderArrow('class_type'),'#',$this->createOrderLink('classify-list','class_type'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('level').$this->drawOrderArrow('level'),'#',$this->createOrderLink('classify-list','level'))
        ;
        ?>
    </th>
</tr>
