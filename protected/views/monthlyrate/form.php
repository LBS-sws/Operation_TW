<?php
$this->pageTitle=Yii::app()->name . ' - Monthly Rate Form';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'code-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('monthly','Sales Summary Rate Form'); ?></strong>
	</h1>
</section>

<section class="content">
	<div class="box"><div class="box-body">
	<div class="btn-group" role="group">
		<?php 
			if ($model->scenario!='new' && $model->scenario!='view') {
				echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add Another'), array(
					'submit'=>Yii::app()->createUrl('monthlyrate/new')));
			}
		?>
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('monthlyrate/index'))); 
		?>
<?php if ($model->scenario!='view'): ?>
			<?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
				'submit'=>Yii::app()->createUrl('monthlyrate/save'))); 
			?>
<?php endif ?>
<?php if ($model->scenario=='edit' && $model->city!='~ZZZZ'): ?>
	<?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
			'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
		);
	?>
<?php endif ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>

			<div class="form-group">
				<?php echo $form->labelEx($model,'city',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<?php
						if ($model->scenario=='new') {
							$list = General::getCityListWithNoDescendant();
							echo $form->hiddenField($model, 'city_name');
							echo $form->dropDownList($model, 'city', $model->getCityList());
						} else {
							echo $form->hiddenField($model, 'city');
							echo $form->textField($model, 'city_name',	array('readonly'=>true));
						}
					?>
				</div>
			</div>

<?php 
$modelName = get_class($model);
foreach ($model->rate_cat as $key=>$value) {
		$fldid = $modelName.'_rate_'.$key;
		$fldname = $modelName.'[rate]['.$key.']';
		echo '<div class="form-group">';
		echo '<div class="col-sm-2 control-label">';
		echo  TbHtml::label(Yii::t('monthly',$value),$fldid);
		echo '</div>';
		echo '<div class="col-sm-3">';
		echo TbHtml::textField($fldname,$model->rate[$key],
				array('size'=>40,'maxlength'=>100,'readonly'=>($model->scenario=='view'))
			);		
		echo '</div>';
		echo '</div>';
}
?>
		</div>
	</div>
</section>

<?php $this->renderPartial('//site/removedialog'); ?>

<?php
$js = Script::genDeleteData(Yii::app()->createUrl('monthlyrate/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


