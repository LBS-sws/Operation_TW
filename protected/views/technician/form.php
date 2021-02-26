<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('technician/index'));
}

$this->pageTitle=Yii::app()->name . ' - Technician Summary Form';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'technician-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<style>
    .input-text-span{display: block;width: 100%;padding: 6px 12px;
        font-size: 14px;
        line-height: 1.42857143;
        border: 1px solid #d2d6de;
        background-color: #eee;
        min-height: 34px;
    }
</style>
<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('procurement','Order Form'); ?></strong>
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
	<div class="box"><div class="box-body">
	<div class="btn-group" role="group">
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('technician/index')));
		?>
<?php if ($model->scenario!='view'): ?>
			<?php
            if($model->status == "pending" || $model->status == "reject"||$model->scenario=='new'){
                //存為草稿
                echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('misc','Save Draft'), array(
                    'submit'=>Yii::app()->createUrl('technician/save')));
            }

            if($model->status == "pending" || $model->status == "reject"){
                //提交審核
                echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('procurement','For audit'), array(
                    'submit'=>Yii::app()->createUrl('technician/audit')));
            }

            if($model->scenario=='edit' && ($model->status == "pending" || $model->status == "cancelled")){
                //刪除
                echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                    'submit'=>Yii::app()->createUrl('technician/delete')));
            }
            if($model->scenario=='edit' && $model->status == "approve"){
                //完成
                echo TbHtml::button('<span class="fa fa-cube"></span> '.Yii::t('procurement','Finish'), array(
                    'submit'=>Yii::app()->createUrl('technician/finish')));
            }
			?>
<?php endif ?>
	</div>

            <div class="btn-group pull-right" role="group">
                <?php if ($model->scenario!='new'){
                    //流程
                    echo TbHtml::button('<span class="fa fa-file-text-o"></span> '.Yii::t('misc','Flow'), array(
                        'name'=>'btnFlow','id'=>'btnFlow','data-toggle'=>'modal','data-target'=>'#flowinfodialog'));
                    //下載
                    echo TbHtml::button('<span class="fa fa-cloud-download"></span> '.Yii::t('procurement','Down'), array(
                        'submit'=>Yii::app()->createUrl('delivery/downorder',array("index"=>$model->id))));
                } ?>
            </div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'status'); ?>


            <div class="form-group">
                <div class="col-sm-10 col-sm-offset-2">
                    <div class="text-danger form-control-static"><span class="required">*</span>
                        <?php echo Yii::t("procurement","Mobile phone users please horizontal screen operation");?>
                    </div>
                </div>
            </div>

            <?php if (!empty($model->ject_remark)): ?>
                <div class="form-group has-error">
                    <?php echo $form->labelEx($model,'ject_remark',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-6">
                        <?php echo $form->textArea($model, 'ject_remark',
                            array('readonly'=>true)
                        ); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($model->scenario!='new'): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'order_code',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->textField($model, 'order_code',
                            array('readonly'=>true)
                        ); ?>
                    </div>
                </div>
            <?php endif ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'goods_list',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-10">
                    <table class="table table-bordered table-striped disabled" id="table-change">
                        <thead>
                        <tr>
                            <td width="20%"><?php echo Yii::t("procurement","Goods Name")?></td>
                            <td width="11%" ><?php echo Yii::t("procurement","Unit")?></td>
                            <td width="12%"><?php echo Yii::t("procurement","Demand Note")?></td>
                            <?php if ($model->scenario=='edit' && ($model->status == "approve" || $model->status == "finished" || $model->status == "read")): ?>
                                <td width="12%"><?php echo Yii::t("procurement","Headquarters Note")?></td>
                            <?php endif ?>
                            <td width="8%"><?php echo Yii::t("procurement","Goods Number")?></td>
                            <?php if ($model->scenario=='edit' && ($model->status == "approve" || $model->status == "finished" || $model->status == "read")): ?>
                                <td width="8%"><?php echo Yii::t("procurement","Actual Number")?></td>
                            <?php endif ?>
                            <?php if (!$model->getInputBool()): ?>
                                <td width="1%">&nbsp;</td>
                            <?php endif ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                            if(!empty($model->goods_list)){
                                $spanInput = "";
                                if($model->getInputBool()){
                                    $spanInput = "spanInput";
                                }
                                foreach ($model->goods_list as $key =>$goodsList){
                                    echo "<tr data-classify='".$goodsList["classify_id"]."'>";
                                    echo "<td>";
                                    echo TbHtml::hiddenField("TechnicianForm[goods_list][$key][goods_id]",$goodsList["goods_id"],array("class"=>"select_id"));
                                    echo TbHtml::hiddenField("TechnicianForm[goods_list][$key][name]",$goodsList["name"]);
                                    echo TbHtml::hiddenField("TechnicianForm[goods_list][$key][unit]",$goodsList["unit"]);
                                    echo TbHtml::hiddenField("TechnicianForm[goods_list][$key][classify_id]",$goodsList["classify_id"]);
                                    echo $goodsList["name"];
                                    echo "</td>";
                                    echo "<td>".$goodsList["unit"]."</td>";
                                    echo "<td>";
                                    echo TbHtml::textField( "TechnicianForm[goods_list][$key][note]",$goodsList["note"],
                                        array('class'=>"select_remark $spanInput",'readonly'=>($model->getInputBool()))
                                    );
                                    echo "</td>";
                                    if ($model->scenario=='edit' && ($model->status == "approve" || $model->status == "finished" || $model->status == "read")){
                                        echo "<td>";
                                        echo TbHtml::textField( "TechnicianForm[goods_list][$key][remark]",$goodsList["remark"],
                                            array('readonly'=>(true),'class'=>'spanInput')
                                        );
                                        echo "</td>";
                                    }
                                    echo "<td>";
                                    echo TbHtml::textField( "TechnicianForm[goods_list][$key][goods_num]",$goodsList["goods_num"],
                                        array('class'=>"select_num $spanInput",'readonly'=>($model->getInputBool()))
                                    );
                                    echo "</td>";
                                    if ($model->scenario=='edit' && ($model->status == "approve" || $model->status == "finished" || $model->status == "read")){
                                        echo "<td>";
                                        echo TbHtml::textField( "TechnicianForm[goods_list][$key][confirm_num]",$goodsList["confirm_num"],
                                            array('readonly'=>(true),'class'=>'spanInput')
                                        );
                                        echo "</td>";
                                    }
                                    if(!$model->getInputBool()){
                                        echo "<td><a class='btn btn-danger goodsDelete' data-id='".$goodsList["goods_id"]."'>刪除</a></td>";
                                    }
                                    echo "</tr>";
                                }
                            }
                        ?>
                        </tbody>
                        <tfoot>
                        <?php if (!$model->getInputBool()): ?>
                        <tr>
                            <td colspan="4" class="">&nbsp;</td>
                            <td class="">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#selectGoods_div" id="addGoods">
                                    <?php echo Yii::t("misc","Add")?>
                                </button>
                            </td>
                         </tr>
                        <?php endif ?>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!--備註-->
            <div class="form-group">
                <?php echo $form->labelEx($model,'remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-7">
                    <?php echo $form->textArea($model, 'remark',
                        array('rows'=>4,'cols'=>50,'maxlength'=>1000,'readonly'=>($model->getInputBool()))
                    ); ?>
                </div>
            </div>
		</div>
	</div>

</section>
<!--選擇物品彈框-->
<?php
 $goodsListToClassify = ClassifyForm::getGoodsListToClassify("Warehouse");
 $classifyList = array();
 echo "<ul class='hide' id='goodsListToClassify'>";
 foreach ($goodsListToClassify as $classify){
     $classifyList[$classify["id"]] = $classify["name"];
     echo "<li><ul class='list-inline'>";
     echo "<li>".$classify["id"]."</li>";
     echo "<li>".$classify["name"]."</li>";
     echo "<li>";
     foreach ($classify["list"] as $goods){
         echo "<ul class='list-inline'>";
         echo "<li data-str='goods_code'>".$goods["goods_code"]."</li>";
         echo "<li data-str='name'>".$goods["name"]."</li>";
         echo "<li data-str='unit'>".$goods["unit"]."</li>";
         echo "<li data-str='id'>".$goods["id"]."</li>";
         echo "</ul>";
     }
     echo "</li>";
     echo "</ul></li>";
 }
echo "</ul>";
?>
<div id="selectGoods_div" role="dialog" tabindex="-1" class="modal fade" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal" type="button">×</button>
                <h4 class="modal-title">選擇物品</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="col-xs-6 col-lg-4">
                        <?php echo TbHtml::dropDownList('selectGoods_select',0,$classifyList,
                            array('disabled'=>(false))
                        ); ?>
                    </div>
                    <div class="col-xs-6 col-lg-4">
                        <input class="form-control" id="selectGoods_search" type="text" placeholder="物品名稱">
                    </div>
                </div>
                <div class="box" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-bordered table-striped" id="selectGoods_table">
                        <thead>
                        <tr>
                            <th width="5%">&nbsp;</th>
                            <th width="25%">物品編號</th>
                            <th width="32%">物品名稱</th>
                            <th width="25%">物品分類</th>
                            <th width="13%">物品單位</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>sss</td>
                            <td>ddd</td>
                            <td>ddd</td>
                            <td>ddd</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary pull-right" type="button">確定</button>
            </div>
        </div>
    </div>
</div>

<?php
if ($model->scenario!='new')
    $this->renderPartial('//site/flowlist',array('model'=>$model));
?>
<?php
$js = "
$('.spanInput').each(function(){
    var text = $(this).val();
    $(this).hide();
    $(this).parent('td').append('<span class=\'input-text-span\'>'+text+'</span>');
});
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
?>

<?php
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/goodsChange.js?4", CClientScript::POS_END);
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/goodsChange.css");
?>

<?php $this->endWidget(); ?>


