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
</section>

<section class="content">

    <div class="box">
        <div class="box-body">
            <div class="btn-group" role="group">
                <?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
                    'submit'=>Yii::app()->createUrl('purchase/index')));
                ?>
            </div>
            <div class="btn-group pull-right" role="group">
                <?php echo TbHtml::button('<span class="fa fa-cloud-download"></span> '.Yii::t('procurement','Down'), array(
                    'submit'=>Yii::app()->createUrl('purchase/downactive',array("index"=>$model->id))));
                ?>
            </div>
        </div>
    </div>
	<div class="box box-info">
		<div class="box-body">
            <div class="form-group">
                <?php echo $form->labelEx($model,'activity_code',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textField($model, 'activity_code',
                        array('min'=>0,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'activity_title',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textField($model, 'activity_title',
                        array('size'=>40,'maxlength'=>250,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'order_class',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->dropDownList($model, 'order_class' ,ActivityForm::getOrderClassNotFast(),
                        array('disabled'=>($model->scenario =='view'))
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
            </div>
            <!--統計開始-->
            <?php
            $flag = true;
            $tabs = array();
            foreach($model->getHeaderList() as $sid=>$sname) {
                $content = $model->getBodyList($sid);
                $tabs[] = array(
                    'label'=>Yii::t("procurement",$sname),
                    'content'=>$content,
                    'active'=>$flag,
                );
                $flag = false;
            }
            $this->widget('bootstrap.widgets.TbTabs', array(
                'tabs'=>$tabs,
            ));
            ?>
		</div>
	</div>
</section>


<?php

if ($model->scenario!='view') {
    $js = Script::genDatePicker(array(
        'ActivityForm_start_time',
        'ActivityForm_end_time',
        'ActivityForm_import_start_time',
        'ActivityForm_import_end_time',
        'ActivityForm_domestic_start_time',
        'ActivityForm_domestic_end_time',
    ));
    Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
}
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


