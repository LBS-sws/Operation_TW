<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('name'),'#',$this->createOrderLink('stickies-list','name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('content').$this->drawOrderArrow('content'),'#',$this->createOrderLink('stickies-list','content'))
        ;
        ?>
    </th>
</tr>
