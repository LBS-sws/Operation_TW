<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('goods_code').$this->drawOrderArrow('goods_code'),'#',$this->createOrderLink('goodsim-list','goods_code'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('name'),'#',$this->createOrderLink('goodsim-list','name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('type').$this->drawOrderArrow('type'),'#',$this->createOrderLink('goodsim-list','type'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('unit').$this->drawOrderArrow('unit'),'#',$this->createOrderLink('goodsim-list','unit'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('price').$this->drawOrderArrow('price'),'#',$this->createOrderLink('goodsim-list','price'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('net_weight').$this->drawOrderArrow('net_weight'),'#',$this->createOrderLink('goodsim-list','net_weight'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('gross_weight').$this->drawOrderArrow('gross_weight'),'#',$this->createOrderLink('goodsim-list','gross_weight'))
        ;
        ?>
    </th>
</tr>
