<?php
$this->pageTitle=Yii::app()->name . ' - Classify Info';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'classify-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Classify Summary Entry'); ?></strong>
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
                if (Yii::app()->user->validRWFunction('YG03'))
                    echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add'), array(
                        'submit'=>Yii::app()->createUrl('classify/new'),
                    ));
                ?>
            </div>
        </div>
    </div>
	<?php $this->widget('ext.layout.ListPageWidget', array(
			'title'=>Yii::t('procurement','Classify List'),
			'model'=>$model,
				'viewhdr'=>'//classify/_listhdr',
				'viewdtl'=>'//classify/_listdtl',
				'search'=>array(
							'name',
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

