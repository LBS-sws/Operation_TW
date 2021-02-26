<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('name'),'#',$this->createOrderLink('email-list','name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('email').$this->drawOrderArrow('email'),'#',$this->createOrderLink('email-list','email'))
        ;
        ?>
    </th>
</tr>
