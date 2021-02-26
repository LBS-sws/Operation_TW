<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('goods_code').$this->drawOrderArrow('goods_code'),'#',$this->createOrderLink('goodsdo-list','goods_code'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('name'),'#',$this->createOrderLink('goodsdo-list','name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('type').$this->drawOrderArrow('type'),'#',$this->createOrderLink('goodsdo-list','type'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('unit').$this->drawOrderArrow('unit'),'#',$this->createOrderLink('goodsdo-list','unit'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('price').$this->drawOrderArrow('price'),'#',$this->createOrderLink('goodsdo-list','price'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('stickies_id').$this->drawOrderArrow('stickies_id'),'#',$this->createOrderLink('goodsdo-list','stickies_id'))
        ;
        ?>
    </th>
</tr>
