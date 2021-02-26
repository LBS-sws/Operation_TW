<tr>
	<th></th>
<?php if (!Yii::app()->user->isSingleCity()) : ?>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('city_name'),'#',$this->createOrderLink('monthly-list','city_name'))
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
</tr>
