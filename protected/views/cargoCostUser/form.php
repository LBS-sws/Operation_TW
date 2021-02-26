<?php
if (empty($model->id)){
    $this->redirect(Yii::app()->createUrl('cargoCostUser/index'));
}
$this->pageTitle=Yii::app()->name . ' - cargoCostUser Form';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'cargoCostUser-form',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>
<style>
    .input-text-span{display: block;width: 100%;padding: 6px 12px;
        font-size: 14px;
        line-height: 1.42857143;
        border: 1px solid #d2d6de;
        background-color: #eee;
        min-height: 34px;
    }
</style>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('procurement','Delivery Form'); ?></strong>
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
                    'submit'=>Yii::app()->createUrl('cargoCostUser/index')));
                ?>
            </div>

            <div class="btn-group pull-right" role="group">
                <?php if ($model->scenario!='new'){
                    //流程
                    echo TbHtml::button('<span class="fa fa-file-text-o"></span> '.Yii::t('misc','Flow'), array(
                        'name'=>'btnFlow','id'=>'btnFlow','data-toggle'=>'modal','data-target'=>'#flowinfodialog'));
                } ?>
            </div>
        </div></div>

    <div class="box box-info">
        <div class="box-body">
            <?php echo $form->hiddenField($model, 'scenario'); ?>
            <?php echo $form->hiddenField($model, 'id'); ?>
            <?php echo $form->hiddenField($model, 'status'); ?>


            <?php if (!empty($model->ject_remark)): ?>
                <div class="form-group has-error">
                    <?php echo $form->labelEx($model,'ject_remark',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-6">
                        <?php echo $form->textArea($model, 'ject_remark',
                            array('readonly'=>true)
                        ); ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($model->scenario!='new'): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'order_code',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->textField($model, 'order_code',
                            array('readonly'=>true)
                        ); ?>
                    </div>
                </div>
            <?php endif ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'lcd',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'lcd',
                        array('readonly'=>true)
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'lcu',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'lcu',
                        array('readonly'=>true)
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'goods_list',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-8">
                    <table class="table table-bordered table-striped disabled" id="table-change">
                        <thead>
                        <tr>
                            <td><?php echo Yii::t("procurement","Goods Code")?></td>
                            <td><?php echo Yii::t("procurement","Goods Name")?></td>
                            <td><?php echo Yii::t("procurement","Unit")?></td>
                            <td><?php echo Yii::t("procurement","Goods Cost")?></td>
                            <td><?php echo Yii::t("procurement","Confirm Number")?></td>
                            <td><?php echo Yii::t("procurement","Sum Cargo Cost")?></td>
                        </tr>
                        </thead>
                        <?php
                        echo $model->printTable();
                        ?>
                    </table>
                </div>
            </div>

            <!--備註-->
            <div class="form-group">
                <?php echo $form->labelEx($model,'remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-7">
                    <?php echo $form->textArea($model, 'remark',
                        array('rows'=>4,'cols'=>50,'maxlength'=>1000,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$this->renderPartial('//site/flowlist',array('model'=>$model));
?>

<?php
$js = "
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>
