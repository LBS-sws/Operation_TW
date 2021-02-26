
<?php
 echo '<form action="" method="post" enctype="multipart/form-data" class="form-horizontal" name="'.$name.'">';
?>

<?php
	$ftrbtn = array();
    $ftrbtn[] = TbHtml::button(Yii::t('dialog','Upload'), array('id'=>"importUp",'submit'=>Yii::app()->createUrl('Warehouse/importGoods')));
    $ftrbtn[] = TbHtml::button(Yii::t('dialog','Close'), array('id'=>"btnWFClose",'data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY));
	$this->beginWidget('bootstrap.widgets.TbModal', array(
					'id'=>'importGoods',
					'header'=>Yii::t('procurement','Import File'),
					'footer'=>$ftrbtn,
					'show'=>false,
				));
?>

<?php echo TbHtml::hiddenField($name.'[id]',$model->id); ?>
<?php
if(empty($model->orderClass)){
    echo TbHtml::hiddenField($name.'[orderClass]',"Warehouse");
}else{
    echo TbHtml::hiddenField($name.'[orderClass]',$model->orderClass);
}
?>
<?php echo TbHtml::hiddenField('prevUrl',$prevUrl); ?>
<div class="form-group">
    <label class="col-sm-2 control-label"><?php echo Yii::t("procurement","file");?></label>
    <div class="col-sm-6">
        <?php echo TbHtml::fileField($name.'[file]',"",array("class"=>"form-control")); ?>
    </div>
</div>

<?php
	$this->endWidget(); 
?>
<?php
echo '</form>';
?>
