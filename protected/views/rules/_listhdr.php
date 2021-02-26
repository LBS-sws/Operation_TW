<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('name'),'#',$this->createOrderLink('rules-list','name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('multiple').$this->drawOrderArrow('multiple'),'#',$this->createOrderLink('rules-list','multiple'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('max').$this->drawOrderArrow('max'),'#',$this->createOrderLink('rules-list','max'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('min').$this->drawOrderArrow('min'),'#',$this->createOrderLink('rules-list','min'))
        ;
        ?>
    </th>
</tr>
