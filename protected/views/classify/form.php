<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('Classify/index'));
}
$this->pageTitle=Yii::app()->name . ' - Classify Info';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'classify-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('procurement','Classify Form'); ?></strong>
	</h1>
<!--
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
		<li><a href="#">Layout</a></li>
		<li class="active">Top Navigation</li>
	</ol>
-->
</section>

<section class="content">
	<div class="box"><div class="box-body">
	<div class="btn-group" role="group">
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('classify/index')));
		?>
<?php if ($model->scenario!='view'): ?>
			<?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
				'submit'=>Yii::app()->createUrl('classify/save')));
			?>
<?php endif ?>
        <?php if ($model->scenario=='edit'): ?>
            <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                'submit'=>Yii::app()->createUrl('classify/delete')));
            ?>
        <?php endif ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'name',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textField($model, 'name',
                        array('size'=>40,'maxlength'=>250,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'class_type',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->dropDownList($model, 'class_type',$model->getArrTypeClass(),
                        array('disabled'=>($model->scenario =='view'))
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'level',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->numberField($model, 'level',
                        array('min'=>0,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
		</div>
	</div>
</section>


<?php
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


