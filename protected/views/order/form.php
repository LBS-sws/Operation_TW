<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('order/index'));
}
$this->pageTitle=Yii::app()->name . ' - Order Summary Form';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'order-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('procurement','Order Summary Form'); ?></strong>
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
        <?php if ($model->scenario =='new'): ?>
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('order/activity')));
		?>
        <?php else:?>
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('order/index')));
		?>
        <?php endif;?>
<?php if ($model->scenario!='view'): ?>
			<?php
            if($model->status == "pending" || $model->status == "cancelled" || $model->status == "reject"||$model->scenario=='new'){
                //存為草稿
                echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('misc','Save Draft'), array(
                    'submit'=>Yii::app()->createUrl('order/save')));
                //提交審核
                echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('procurement','For audit'), array(
                    'submit'=>Yii::app()->createUrl('order/audit')));
            }

            if($model->scenario=='edit' && ($model->status == "pending" || $model->status == "cancelled")){
                //刪除
                echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                    'submit'=>Yii::app()->createUrl('order/delete')));
            }
            if($model->scenario=='edit' && $model->status == "approve"){
                //刪除
                echo TbHtml::button('<span class="fa fa-cube"></span> '.Yii::t('procurement','Finish'), array(
                    'submit'=>Yii::app()->createUrl('order/finish')));
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
                        'submit'=>Yii::app()->createUrl('Purchase/downorder',array("index"=>$model->id))));
                } ?>
            </div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'status'); ?>
			<?php echo $form->hiddenField($model, 'order_class'); ?>
			<?php echo $form->hiddenField($model, 'activity_id'); ?>

            <div class="form-group">
                <div class="col-sm-10 col-sm-offset-1"><?php echo $model->getHeadHtml();?></div>
            </div>

            <?php if ($model->status == "approve"||$model->status == "finished"): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'fish_remark',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-6">
                        <?php echo $form->textField($model, 'fish_remark',
                            array('readonly'=>($model->status == "finished"||$model->scenario=='view'))
                        ); ?>
                    </div>
                </div>
            <?php endif; ?>

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

            <?php if (!empty($model->activity_id)): ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'activity_id',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->dropDownList($model, 'activity_id',$model->getActivityToNow(),
                        array('disabled'=>(true))
                    ); ?>
                </div>
            </div>
            <?php endif ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'order_class',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->dropDownList($model, 'order_class',OrderGoods::getArrGoodsClass(),
                        array('disabled'=>true)
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'goods_list',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-10">
                    <table class="table table-bordered table-striped disabled" id="table-change">
                        <thead>
                        <tr>
                            <?php
                            $currencyType = $model->order_class=="Domestic"?"RMB":'US$';
                            ?>
                            <td width="20%"><?php echo Yii::t("procurement","Goods Name")?></td>
                            <td width="11%"><?php echo Yii::t("procurement","Type")?></td>
                            <td width="8%"><?php echo Yii::t("procurement","Unit")?></td>

                            <td width="12%"><?php echo Yii::t("procurement","Demand Note")?></td>
                            <?php if ($model->scenario=='edit' && ($model->status == "approve" || $model->status == "finished" || $model->status == "read")): ?>
                                <td width="12%"><?php echo Yii::t("procurement","Headquarters Note")?></td>
                            <?php endif ?>

                            <td width="10%"><?php echo Yii::t("procurement","Price（".$currencyType."）")?></td>
                            <td width="10%"><?php echo Yii::t("procurement","Goods Number")?></td>

                            <?php if ($model->scenario=='edit' && ($model->status == "approve" || $model->status == "finished" || $model->status == "read")): ?>
                            <td width="10%"><?php echo Yii::t("procurement","Actual Number")?></td>
                            <?php endif ?>

                            <td width="10%"><?php echo Yii::t("procurement","Total（".$currencyType."）")?></td>
                            <td width="8%">&nbsp;</td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $classify = ClassifyForm::getClassifyList($model->order_class);
                        $stickiesContentList = StickiesForm::getStickiesContentList();
                            foreach ($model->goods_list as $key => $val){
                                $con_num = empty($val['id'])?$key:$val['id'];
                                $tableTr = "<tr datanum='$con_num'>";
                                $tableTr.="<td><div class='input-group'>";
                                $tableTr.='<div class="input-group-btn">';
                                $tableTr.="<input type='hidden' class='stickies_id' name='OrderForm[goods_list][$con_num][stickies_id]' value='".$val['stickies_id']."'>";
                                $tableTr.="<input type='hidden' class='classify_id' name='OrderForm[goods_list][$con_num][classify_id]' value='".$val['classify_id']."'>";

                                if($model->scenario=='new' || $model->status == "pending" || $model->status == "reject"){
                                    $tableTr.='<button type="button" class="btn btn-default bg-fff dropdown-toggle" data-toggle="dropdown" data-id="'.$val["classify_id"].'">';
                                    $testNBSP = empty($classify[$val["classify_id"]])?"&nbsp;":$classify[$val["classify_id"]];
                                    $tableTr.='<span>'.$testNBSP.'</span><span class="caret"></span></button><ul class="dropdown-menu goodsIfy">';
                                    foreach ($classify as $classify_id =>$classify_name){
                                        $classify_name=empty($classify_name)?"&nbsp;":$classify_name;
                                        $tableTr.="<li data-id='$classify_id'><a>$classify_name</a></li>";
                                    }
                                    $tableTr.='</ul>';
                                }

                                $tableTr.='</div>';
                                $tableTr.="<input type='text' class='form-control testInput' autocomplete='off' name='OrderForm[goods_list][$con_num][name]' value='".$val['name']."'>";
                                $tableTr.="<input type='hidden' name='OrderForm[goods_list][$con_num][goods_id]' value='".$val['goods_id']."'>";
                                if(!empty($val['stickies_id'])){
                                    $tableTr.='<div class="input-group-btn changeHelp" content-id="'.$val['stickies_id'].'"><span class="fa fa-exclamation-circle"></span></div>';
                                }
                                $tableTr.="</div></td>";
                                $tableTr.="<td><input type='text' class='form-control type' readonly name='OrderForm[goods_list][$con_num][type]' value='".$val['type']."'></td>";
                                $tableTr.="<td><input type='text' class='form-control unit' readonly name='OrderForm[goods_list][$con_num][unit]' value='".$val['unit']."'></td>";
                                $tableTr.="<td><input type='text' class='form-control' name='OrderForm[goods_list][$con_num][note]' value='".$val['note']."'></td>";
                                if($model->scenario=='edit' && ($model->status == "approve" || $model->status == "finished" || $model->status == "read")){
                                    $tableTr.="<td><input type='text' class='form-control' name='OrderForm[goods_list][$con_num][remark]' value='".$val['remark']."'></td>";
                                }
                                $tableTr.="<td><input type='text' class='form-control price' readonly name='OrderForm[goods_list][$con_num][price]' value='".sprintf("%.2f", $val['price'])."'></td>";
                                $tableTr.="<td><input type='number' class='form-control numChange goods_num' name='OrderForm[goods_list][$con_num][goods_num]' value='".$val['goods_num']."'></td>";
                                if($model->scenario=='edit' && ($model->status == "approve" || $model->status == "finished" || $model->status == "read")) {
                                    $tableTr .= "<td><input type='number' class='form-control goods_num' name='OrderForm[goods_list][$con_num][confirm_num]' value='" . $val['confirm_num'] . "'></td>";
                                }
                                $tableTr.="<td><input type='text' class='form-control sum' readonly></td>";
                                $tableTr.="<td><button type='button' class='btn btn-danger delGoods'>".Yii::t("misc","Delete")."</button>";
                                if(!empty($val['id'])){
                                    $tableTr.="<input type='hidden' name='OrderForm[goods_list][$con_num][id]' value='".$val['id']."'>";
                                }
                                $tableTr.="</td>";

                                $tableTr.="</tr>";
                                echo $tableTr;
                            }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <?php
                            if ($model->scenario=='edit' && ($model->status == "approve" || $model->status == "finished" || $model->status == "read")){
                                echo '<td colspan="8"></td>';
                            }else{
                                echo '<td colspan="6"></td>';
                            }
                            ?>
                            <td class="text-success fa-2x">0</td>
                            <td class="text-center"><button type="button" class="btn btn-primary" id="addGoods"><?php echo Yii::t("misc","Add")?></button></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!--備註-->
            <?php if ($model->scenario =='new' || $model->status=='approve' || $model->status=='pending' || $model->status=='reject'): ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-7">
                    <?php echo $form->textArea($model, 'remark',
                        array('rows'=>4,'cols'=>50,'maxlength'=>1000,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <?php endif ?>
		</div>
	</div>

    <div class="hide" id="classifyList">
        <div class="input-group-btn">
            <input type="hidden" class="stickies_id">
            <input type="hidden" class="classify_id">
            <button type="button" class="btn btn-default bg-fff dropdown-toggle" data-toggle="dropdown"><span>&nbsp;</span><span class="caret"></span></button>
            <ul class="dropdown-menu goodsIfy">
                <?php

                foreach ($classify as $classify_id =>$classify_name){
                    $classify_name=empty($classify_name)?"&nbsp;":$classify_name;
                    echo "<li data-id='$classify_id'><a>$classify_name</a></li>";
                }
                ?>
            </ul>
        </div>
    </div>
</section>
<?php
if ($model->scenario!='new')
    $this->renderPartial('//site/flowlist',array('model'=>$model));
?>
<?php
//OrderForm_activity_id
$goodList = json_encode($model->getGoodsList($model->order_class));
$stickiesList = json_encode(StickiesForm::getStickiesContentList());
$tableBool = 1;//表格內的輸入框能否輸入
if($model->status == "pending" || $model->status == "cancelled"||$model->scenario=='new'||$model->status=='reject'){
    $tableBool = 0;
}
$js = '
var orderClass = "'.$model->order_class.'";
inputDownList('.$goodList.',tableGoodsChange,true);
$("#addGoods").on("click",{btnStr:"'.Yii::t("misc","Delete").'"},addGoodsTable);
$("body").delegate(".delGoods","click","'.Yii::t("procurement","Are you sure you want to delete this data?").'",delGoodsTable);
$("body").delegate(".numChange","input",goodsTotalPrice);
$("body").delegate(".changeHelp","mouseover",function () {
    addContentHelp($(this),'.$stickiesList.');
  });
$("body").delegate(".changeHelp","mouseout",function () {
    $(this).find(".content-help").remove();
  });
goodsTotalPrice();
goodsIfyChange();
disabledTable('.$tableBool.');

//物品數量即時驗證
$("#table-change").delegate("input","blur",function(){
    validateGoods("'.Yii::app()->createUrl('order/validateAjax').'");
});
$("body").delegate("#shenValidate,#shenValidate button","click",function(){
    $("#shenValidate").fadeOut(200,function(){
        $("#shenValidate").remove();
    });
});
';
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
?>
<?php
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/goodsChangeTwo.js?2", CClientScript::POS_END);
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/goodsChange.css");
?>

<?php $this->endWidget(); ?>


