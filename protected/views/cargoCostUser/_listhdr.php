<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('lcd').$this->drawOrderArrow('a.lcd'),'#',$this->createOrderLink('cargoCostUser-list','a.lcd'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('lcu').$this->drawOrderArrow('a.lcu'),'#',$this->createOrderLink('cargoCostUser-list','a.lcu'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('order_code').$this->drawOrderArrow('a.order_code'),'#',$this->createOrderLink('cargoCostUser-list','a.order_code'))
        ;
        ?>
    </th>
    <th>
        <?php
            echo "<a href='#'>".Yii::t("procurement","Goods List")."</a>";
        ?>
    </th>
    <?php if (!Yii::app()->user->isSingleCity()): ?>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('a.city'),'#',$this->createOrderLink('cargoCostUser-list','a.city'))
        ;
        ?>
    </th>
    <?php endif ?>
    <th>
        <?php echo TbHtml::link($this->getLabelName('status').$this->drawOrderArrow('a.status'),'#',$this->createOrderLink('cargoCostUser-list','a.status'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('total_price').$this->drawOrderArrow('total_price'),'#',$this->createOrderLink('cargoCostUser-list','total_price'))
        ;
        ?>
    </th>
</tr>
