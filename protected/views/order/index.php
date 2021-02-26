<?php
$this->pageTitle=Yii::app()->name . ' - Order List';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'order-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Order List'); ?></strong>
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
	<?php $this->widget('ext.layout.ListPageWidget', array(
			'title'=>Yii::t('procurement','Order List'),
			'model'=>$model,
				'viewhdr'=>'//order/_listhdr',
				'viewdtl'=>'//order/_listdtl',
				'search'=>array(
							'order_code',
							'activity_id',
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

