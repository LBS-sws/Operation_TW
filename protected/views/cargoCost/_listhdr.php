<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('lcu').$this->drawOrderArrow('b.disp_name'),'#',$this->createOrderLink('cargoCost-list','b.disp_name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('c.name'),'#',$this->createOrderLink('cargoCost-list','c.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('total_price').$this->drawOrderArrow('total_price'),'#',$this->createOrderLink('cargoCost-list','total_price'))
        ;
        ?>
    </th>
</tr>
