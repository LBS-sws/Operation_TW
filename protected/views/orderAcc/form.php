<?php
$this->pageTitle=Yii::app()->name . ' - OrderAcc Info';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'orderAcc-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','OrderAcc Info'); ?></strong>
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
        <?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
            'submit'=>Yii::app()->createUrl('orderAcc/save')));
        ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'id'); ?>

            <div class="form-group">
                <div class="col-sm-3 col-sm-offset-2">
                    <p class="form-control-static text-danger"><?php echo Yii::t("procurement","Whether multiple orders are allowed simultaneously ?")?></p>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'acc_do',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'acc_do',$model->getOpenSelectList(),
                        array('disabled'=>($model->scenario =='view'))
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'acc_im',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->dropDownList($model, 'acc_im',$model->getOpenSelectList(),
                        array('disabled'=>($model->scenario =='view'))
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


