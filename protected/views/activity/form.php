<?php
$this->pageTitle=Yii::app()->name . ' - Order Activity Form';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'activity-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('procurement','Order Activity Form'); ?></strong>
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
		<?php
        echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('procurement','Add Activity'), array(
            'submit'=>Yii::app()->createUrl('activity/new'),
        ));
		?>
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('activity/index')));
		?>
<?php if ($model->scenario!='view'): ?>
			<?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
				'submit'=>Yii::app()->createUrl('activity/save')));
			?>
<?php endif ?>
        <?php if ($model->scenario=='edit'): ?>
            <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                'submit'=>Yii::app()->createUrl('activity/delete')));
            ?>
        <?php endif ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'city_auth'); ?>

            <?php if ($model->scenario!='new'): ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'activity_code',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textField($model, 'activity_code',
                        array('readonly'=>true)
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'activity_title',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textField($model, 'activity_title',
                        array('readonly'=>true)
                    ); ?>
                </div>
            </div>
            <?php endif ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'order_class',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->dropDownList($model, 'order_class',$model->getOrderClassNotFast(),
                        array('disabled'=>($model->scenario !='new'))
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'start_time',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php echo $form->textField($model, 'start_time',
                            array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),));
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'end_time',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php echo $form->textField($model, 'end_time',
                            array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'num',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->numberField($model, 'num',
                        array('min'=>1,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'city_auth',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-6">
                    <?php
                    echo $form->textArea($model, 'city_name',
                        array('rows'=>4,'cols'=>80,'maxlength'=>1000,'readonly'=>true)
                    );
                    ?>
                </div>
                <div class="col-sm-2">
                    <?php
                    echo TbHtml::button('<span class="fa fa-search"></span> '.Yii::t('user','City'),
                        array('name'=>'btnCity','id'=>'btnCity',)
                    );
                    ?>
                </div>
            </div>
		</div>
	</div>
</section>


<?php $this->renderPartial('//site/lookup'); ?>
<?php

if ($model->scenario!='view') {
    $js = Script::genLookupSearchEx();
    Yii::app()->clientScript->registerScript('lookupSearch',$js,CClientScript::POS_READY);

    $js = Script::genLookupButtonEx('btnCity', 'citySearch', 'city_auth', 'city_name',
        array(),
        true
    );
    Yii::app()->clientScript->registerScript('lookupUsers',$js,CClientScript::POS_READY);

    $js = Script::genLookupSelect();
    Yii::app()->clientScript->registerScript('lookupSelect',$js,CClientScript::POS_READY);
    $js = Script::genDatePicker(array(
        'ActivityForm_start_time',
        'ActivityForm_end_time',
    ));
    Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
}
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


