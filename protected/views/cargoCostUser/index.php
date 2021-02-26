<?php
$this->pageTitle=Yii::app()->name . ' - Cargo Cost List';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'cargoCostUser-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Technician cargo cost') ?></strong>
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
    <div class="box">
        <div class="box-body">
            <div class="btn-group" role="group">
                <?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
                    'submit'=>Yii::app()->createUrl('cargoCost/index')));
                ?>
            </div>
        </div>
    </div>
	<?php
    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('procurement','Order List')." - ".$model->getWebHeadTitle(),
        'model'=>$model,
        'viewhdr'=>'//cargoCostUser/_listhdr',
        'viewdtl'=>'//cargoCostUser/_listdtl',
        'search'=>array(
            'order_code',
            'lcu',
            'goods_name',
            'status',
        ),
	));
	?>
</section>
<?php
	echo $form->hiddenField($model,'pageNum');
	echo $form->hiddenField($model,'totalRow');
	echo $form->hiddenField($model,'orderField');
	echo $form->hiddenField($model,'orderType');
?>
<?php $this->endWidget(); ?>

<?php
$js = "
$('#start_time').datepicker({autoclose: true, format: 'yyyy/mm/dd',language: 'zh_cn'});
$('#end_time').datepicker({autoclose: true, format: 'yyyy/mm/dd',language: 'zh_cn'});
$('.checkBoxDown').on('click',function(e){
    e.stopPropagation();
});
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
	$js = Script::genTableRowClick();
	Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

