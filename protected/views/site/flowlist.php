<?php
	$ftrbtn = array();
	$ftrbtn[] = TbHtml::button(Yii::t('dialog','Close'), array('id'=>'btnWFClose','data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY));
	$this->beginWidget('bootstrap.widgets.TbModal', array(
					'id'=>'flowinfodialog',
					'header'=>Yii::t('dialog','Flow Info'),
					'footer'=>$ftrbtn,
					'show'=>false,
				));
?>

<div class="box" id="flow-list" style="max-height: 300px; overflow-y: auto;">
	<table id="tblFlow" class="table table-bordered table-striped table-hover">
		<thead>
			<tr>
                <th><?php echo Yii::t("procurement","Order Status"); ?></th>
                <th><?php echo Yii::t("procurement","Operator User"); ?></th>
                <th><?php echo Yii::t("procurement","Operator Time"); ?></th>
                <th><?php echo Yii::t("procurement","Remark"); ?></th>
			</tr>
		</thead>
		<tbody>

        <?php
        if(!empty($model->statusList)){
            foreach ($model->statusList as $statusOne){
                echo "<tr><td>".Yii::t("procurement",$statusOne["status"])."</td><td>".$statusOne["lcu"]."</td><td>".$statusOne["time"]."</td><td>".$statusOne["r_remark"]."</td></tr>";
            }
        }
        ?>
		</tbody>
	</table>
</div>

<?php
	$this->endWidget(); 
?>
