<tr>
    <th></th>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('lcd').$this->drawOrderArrow('a.lcd'),'#',$this->createOrderLink('delivery-list','a.lcd'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('lcu').$this->drawOrderArrow('a.lcu'),'#',$this->createOrderLink('delivery-list','a.lcu'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('order_code').$this->drawOrderArrow('a.order_code'),'#',$this->createOrderLink('delivery-list','a.order_code'))
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
        <?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('a.city'),'#',$this->createOrderLink('delivery-list','a.city'))
        ;
        ?>
    </th>
    <?php endif ?>
    <th>
        <?php echo TbHtml::link($this->getLabelName('status').$this->drawOrderArrow('a.status'),'#',$this->createOrderLink('delivery-list','a.status'))
        ;
        ?>
    </th>
</tr>
