
<?php
 echo '<form action="" target="_blank" method="post" enctype="multipart/form-data" class="form-horizontal" name="'.$name.'">';
?>

<?php
	$ftrbtn = array();
    $ftrbtn[] = TbHtml::button(Yii::t('dialog','Upload'), array('id'=>"importUpPrice",'data-dismiss'=>'modal','submit'=>Yii::app()->createUrl('warehouse/importPrice')));
    $ftrbtn[] = TbHtml::button(Yii::t('dialog','Close'), array('id'=>"btnWFClose",'data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY));
	$this->beginWidget('bootstrap.widgets.TbModal', array(
					'id'=>'importPrice',
					'header'=>Yii::t('procurement','Import Price'),
					'footer'=>$ftrbtn,
					'show'=>false,
				));
?>

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
<script>
    $(function () {
        $("#importUpPrice").on("click",function () {
            console.log(1);
            setTimeout(function () {
                console.log(2);
                $("#importUpPrice").parents("form").eq(0).get(0).reset();
                $("#importUpPrice").css('pointer-events','auto');;
            },1000);
        })
    })
</script>
