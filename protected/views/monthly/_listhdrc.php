<tr>
	<th></th>
<?php if (!Yii::app()->user->isSingleCity()) : ?>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('city_name'),'#',$this->createOrderLink('staff-list','city_name'))
			;
		?>
	</th>
<?php endif ?>
	<th>
		<?php echo TbHtml::link($this->getLabelName('year_no').$this->drawOrderArrow('year_no'),'#',$this->createOrderLink('monthly-list','year_no'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('month_no').$this->drawOrderArrow('month_no'),'#',$this->createOrderLink('monthly-list','month_no'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link('清洁收入'.$this->drawOrderArrow('val_1'),'#',$this->createOrderLink('monthly-list','val_1'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link('灭虫收入'.$this->drawOrderArrow('val_2'),'#',$this->createOrderLink('monthly-list','val_2'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link('纸品收入'.$this->drawOrderArrow('val_6'),'#',$this->createOrderLink('monthly-list','val_6'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link('杂项及其他销售收入'.$this->drawOrderArrow('val_3'),'#',$this->createOrderLink('monthly-list','val_3'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link('飘盈香收入'.$this->drawOrderArrow('val_4'),'#',$this->createOrderLink('monthly-list','val_4'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link('甲醛收入'.$this->drawOrderArrow('val_5'),'#',$this->createOrderLink('monthly-list','val_5'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link(Yii::t("monthly",'空气净化机租赁').$this->drawOrderArrow('val_13'),'#',$this->createOrderLink('monthly-list','val_13'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link('收入合計'.$this->drawOrderArrow('val_11'),'#',$this->createOrderLink('monthly-list','val_11'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('wfstatusdesc').$this->drawOrderArrow('wfstatusdesc'),'#',$this->createOrderLink('monthly-list','wfstatusdesc'))
			;
		?>
	</th>
</tr>
