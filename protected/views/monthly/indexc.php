<?php
$this->pageTitle=Yii::app()->name . ' - Sales Summary Enquiry';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'monthly-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('monthly','Sales Summary Enquiry'); ?></strong>
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
<!--
	<div class="box"><div class="box-body">
		<div class="form-group">
			<?php echo $form->labelEx($model,'year_no',array('class'=>"col-sm-1 control-label")); ?>
			<div class="col-sm-3">
				<?php 
					$item = array();
					for ($i=2017;$i<=2027;$i++) {$item[$i] = $i; }
					echo $form->dropDownList($model, 'year_no', $item); 
				?>
			</div>
			<?php echo $form->labelEx($model,'month_no',array('class'=>"col-sm-1 control-label")); ?>
			<div class="col-sm-2">
				<?php 
					$item = array();
					for ($i=1;$i<=12;$i++) {$item[$i] = $i; }
					echo $form->dropDownList($model, 'month_no', $item); 
				?>
			</div>
			<div class="col-sm-3 pull-right">
				<?php 
					echo TbHtml::button('<span class="fa fa-search"></span> '.Yii::t('misc','Search'),
					array('submit'=>Yii::app()->createUrl('monthly/indexc',array('pageNum'=>1)),)); 
				?>
			</div>
		</div>
	</div></div>
-->
	<?php 
		$search = Yii::app()->user->isSingleCity()
				? array('year_no', 'month_no', 'wfstatusdesc', )
				: array('city_name','year_no', 'month_no', 'wfstatusdesc', )
				;
		$this->widget('ext.layout.ListPageWidget', array(
			'title'=>Yii::t('monthly','Sales Summary List'),
			'model'=>$model,
				'viewhdr'=>'//monthly/_listhdrc',
				'viewdtl'=>'//monthly/_listdtlc',
				'search'=>$search,
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

