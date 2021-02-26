<?php
$this->pageTitle=Yii::app()->name . ' - Goods List';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'goodsim-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Goods Summary Entry')." - ".Yii::t('procurement','Import'); ?></strong>
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
            <?php
            //var_dump(Yii::app()->session['rw_func']);
            if (Yii::app()->user->validRWFunction('YG01'))
                echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add').Yii::t("procurement","Import"), array(
                    'submit'=>Yii::app()->createUrl('goodsim/new'),
                ));
            ?>
        </div>
    </div>
	<?php $this->widget('ext.layout.ListPageWidget', array(
			'title'=>Yii::t('procurement','Goods List'),
			'model'=>$model,
				'viewhdr'=>'//goodsim/_listhdr',
				'viewdtl'=>'//goodsim/_listdtl',
				'search'=>array(
							'goods_code',
							'name',
							'type',
							'unit',
							'price',
							'net_weight',
							'gross_weight',
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

