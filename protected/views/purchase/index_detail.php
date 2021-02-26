<?php
if (empty($model->activity_title)){
    $this->redirect(Yii::app()->createUrl('purchase/index'));
}
$this->pageTitle=Yii::app()->name . ' - Purchase List';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'order-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo $model->activity_title; ?></strong>
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
                    'submit'=>Yii::app()->createUrl('purchase/index')));
                ?>
            </div>
            <div class="btn-group" role="group">
                <?php echo TbHtml::button('<span class="fa fa-magnet"></span> '.Yii::t('procurement','All to see'), array(
                    'submit'=>Yii::app()->createUrl('purchase/see',array("index"=>$model->activity_id))));
                ?>
            </div>
        </div>
    </div>
	<?php $this->widget('ext.layout.ListPageWidgetTwo', array(
			'title'=>Yii::t('procurement','Order List'),
			'model'=>$model,
				'viewhdr'=>'//purchase/_listhdr_detail',
				'viewdtl'=>'//purchase/_listdtl_detail',
				'search'=>array(
							'order_code',
							'city',
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
	$js = Script::genTableRowClick();
	Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

