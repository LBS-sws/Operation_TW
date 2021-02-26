<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('fast/index'));
}
$this->pageTitle=Yii::app()->name . ' - Fast Form';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'fast-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('procurement','Fast Form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('fast/index')));
		?>
<?php if ($model->scenario!='view'): ?>
			<?php
            if($model->status == "sent" || $model->status == "read"){
                //存為草稿
                echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('misc','Save'), array(
                    'submit'=>Yii::app()->createUrl('fast/save')));

                //发货
                echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('procurement','Shipments'), array(
                    'submit'=>Yii::app()->createUrl('fast/audit')));

                //通知
                echo TbHtml::button('<span class="fa fa-bullhorn"></span> '.Yii::t('procurement','Notice'), array(
                    'name'=>'btnNotice','id'=>'btnNotice','data-toggle'=>'modal','data-target'=>'#noticedialog'));
            }
            if($model->status == "approve"){
                //退回
                echo TbHtml::button('<span class="fa fa-backward"></span> '.Yii::t('procurement','Back Status'), array(
                    'submit'=>Yii::app()->createUrl('fast/backward')));
            }
			?>
<?php endif ?>
	</div>

            <div class="btn-group pull-right" role="group">
                <?php if ($model->scenario!='new'){
                    if($model->status == "sent" || $model->status == "read") {
                        //拒絕
                        echo TbHtml::button('<span class="fa fa-mail-reply-all"></span> '.Yii::t('procurement','Reject'), array(
                            'name'=>'btnJect','id'=>'btnJect','data-toggle'=>'modal','data-target'=>'#jectdialog'));
                    }
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
            <?php echo $form->hiddenField($model, 'activity_id'); ?>
            <?php echo $form->hiddenField($model, 'order_class'); ?>

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
                            <td width="20%"><?php echo Yii::t("procurement","Goods Name")?></td>
                            <td width="11%"><?php echo Yii::t("procurement","Type")?></td>
                            <td width="8%"><?php echo Yii::t("procurement","Unit")?></td>
                            <td width="10%"><?php echo Yii::t("procurement","Demand Note")?></td>
                            <td width="10%"><?php echo Yii::t("procurement","Headquarters Note")?></td>
                            <td width="10%"><?php echo Yii::t("procurement",'Price（US$）')?></td>
                            <td width="10%"><?php echo Yii::t("procurement","Goods Number")?></td>
                            <td width="10%"><?php echo Yii::t("procurement","Actual Number")?></td>
                            <td width="10%"><?php echo Yii::t("procurement",'Total（US$）')?></td>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                            foreach ($model->goods_list as $key => $val){
                                $con_num = empty($val['id'])?$key:$val['id'];
                                $tableTr = "<tr datanum='$con_num'>";


                                $tableTr.="<td><div class='input-group'>";
                                $tableTr.="<input type='hidden' name='FastForm[goods_list][$con_num][id]' value='".$val['id']."'>";
                                $tableTr.="<input type='hidden' class='stickies_id' name='FastForm[goods_list][$con_num][stickies_id]' value='".$val['stickies_id']."'>";
                                $tableTr.="<input type='text' readonly class='form-control testInput' name='FastForm[goods_list][$con_num][name]' value='".$val['name']."'>";
                                $tableTr.="<input type='hidden' name='FastForm[goods_list][$con_num][goods_id]' value='".$val['goods_id']."'>";
                                $tableTr.="</div></td>";

                                $tableTr.="<td><input type='text' class='form-control type' readonly name='FastForm[goods_list][$con_num][type]' value='".$val['type']."'></td>";
                                $tableTr.="<td><input type='text' class='form-control unit' readonly name='FastForm[goods_list][$con_num][unit]' value='".$val['unit']."'></td>";
                                $tableTr.="<td><input type='text' class='form-control' readonly name='FastForm[goods_list][$con_num][note]' value='".$val['note']."'></td>";
                                if($model->status == "sent" || $model->status == "read"){
                                    $tableTr.="<td><input type='text' class='form-control' name='FastForm[goods_list][$con_num][remark]' value='".$val['remark']."'></td>";
                                }else{
                                    $tableTr.="<td><input type='text' class='form-control' readonly name='FastForm[goods_list][$con_num][remark]' value='".$val['remark']."'></td>";
                                }
                                $tableTr.="<td><input type='text' class='form-control price' readonly name='FastForm[goods_list][$con_num][price]' value='".sprintf("%.2f", $val['price'])."'></td>";
                                $tableTr.="<td><input type='number' class='form-control' readonly name='FastForm[goods_list][$con_num][goods_num]' value='".$val['goods_num']."'></td>";
                                if($model->status == "sent" || $model->status == "read"){
                                    $tableTr.="<td><input type='number' class='form-control numChange goods_num' name='FastForm[goods_list][$con_num][confirm_num]' value='".$val['confirm_num']."'></td>";
                                }else{
                                    $tableTr.="<td><input type='number' class='form-control numChange goods_num' readonly name='FastForm[goods_list][$con_num][confirm_num]' value='".$val['confirm_num']."'></td>";
                                }
                                $tableTr.="<td><input type='text' class='form-control sum' readonly></td>";

                                $tableTr.="</tr>";
                                echo $tableTr;
                            }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="8"></td>
                            <td class="text-success fa-2x">0</td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!--備註-->
            <div class="form-group">
                <?php echo $form->labelEx($model,'remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-7">
                    <?php echo $form->textArea($model, 'remark',
                        array('rows'=>4,'cols'=>50,'maxlength'=>1000,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
		</div>
	</div>
</section>
<?php
    $this->renderPartial('//site/flowlist',array('model'=>$model));
?>
<?php
    $this->renderPartial('//site/ject',array('model'=>$model,'form'=>$form,'submit'=>Yii::app()->createUrl('fast/reject')));
?>
<div id="noticedialog" role="dialog" tabindex="-1" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal" type="button">×</button>
                <h4 class="modal-title"><?php echo Yii::t("procurement","Notice")?></h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <?php echo $form->labelEx($model,'notice',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-9">
                        <?php echo $form->textArea($model, 'notice',
                            array('rows'=>4,'cols'=>50,'maxlength'=>1000,'readonly'=>($model->scenario=='view'))
                        ); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <?php
                echo TbHtml::button(Yii::t('dialog','Close'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_DEFAULT,"class"=>"pull-left"));
                echo TbHtml::button(Yii::t('procurement','sent'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY,'submit' => Yii::app()->createUrl('fast/notice')));
                ?>
            </div>
        </div>
    </div>
</div>
<?php
$js = '
$("body").delegate(".numChange","input",goodsTotalPrice);
goodsTotalPrice();
';
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
?>

<?php
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/goodsChangeTwo.js", CClientScript::POS_END);
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/goodsChange.css");
?>

<?php $this->endWidget(); ?>


