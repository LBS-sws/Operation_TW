<?php
	$ftrbtn = array();
	$ftrbtn[] = TbHtml::button(Yii::t('dialog','Close'), array('id'=>'btnWFClose','data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_DEFAULT,"class"=>"pull-left"));
	$ftrbtn[] = TbHtml::button(Yii::t('procurement','Submit'), array('id'=>'btnWFSubmit','data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY,'submit' => $submit));
	$this->beginWidget('bootstrap.widgets.TbModal', array(
					'id'=>'jectdialog',
					'header'=>Yii::t('procurement','Reject'),
					'footer'=>$ftrbtn,
					'show'=>false,
				));
?>

<div class="form-group">
    <?php echo $form->labelEx($model,'ject_remark',array('class'=>"col-sm-2 control-label")); ?>
    <div class="col-sm-9">
        <?php echo $form->textArea($model, 'ject_remark',
            array('rows'=>4,'cols'=>50,'maxlength'=>1000,'readonly'=>($model->scenario=='view'))
        ); ?>
    </div>
</div>

<?php
	$this->endWidget(); 
?>
