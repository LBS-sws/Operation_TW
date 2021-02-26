<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('city_name'),'#',$this->createOrderLink('code-list','city_name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('rate').$this->drawOrderArrow('rate'),'#',$this->createOrderLink('code-list','rate'))
			;
		?>
	</th>
</tr>
