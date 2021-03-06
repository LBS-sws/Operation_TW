<?php
$this->pageTitle=Yii::app()->name . ' - Add Order';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'order-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Add Order'); ?></strong>
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
                <?php
                //var_dump(Yii::app()->session['rw_func']);
                echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('procurement','Add Fast Order'), array(
                    'submit'=>Yii::app()->createUrl('order/new?index=0'),
                ));
                ?>
            </div>
        </div>
    </div>
	<?php $this->widget('ext.layout.ListPageWidget', array(
			'title'=>Yii::t('procurement','Order Activity List'),
			'model'=>$model,
				'viewhdr'=>'//order/_listhdr_activity',
				'viewdtl'=>'//order/_listdtl_activity',
				'search'=>array(
							'activity_code',
							'activity_title',
							'order_class',
							'num',
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

