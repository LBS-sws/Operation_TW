<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('goods_code').$this->drawOrderArrow('goods_code'),'#',$this->createOrderLink('goods-list','goods_code'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('name'),'#',$this->createOrderLink('goods-list','name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('goods_class').$this->drawOrderArrow('goods_class'),'#',$this->createOrderLink('goods-list','goods_class'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('type').$this->drawOrderArrow('type'),'#',$this->createOrderLink('goods-list','type'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('unit').$this->drawOrderArrow('unit'),'#',$this->createOrderLink('goods-list','unit'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('price').$this->drawOrderArrow('price'),'#',$this->createOrderLink('goods-list','price'))
        ;
        ?>
    </th>
</tr>
