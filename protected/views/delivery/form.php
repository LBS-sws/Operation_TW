<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('delivery/index'));
}
$this->pageTitle=Yii::app()->name . ' - Delivery Form';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'delivery-form',
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
        <strong><?php echo Yii::t('procurement','Delivery Form'); ?></strong>
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
                    'submit'=>Yii::app()->createUrl('delivery/index')));
                ?>
                <?php if ($model->scenario!='view'): ?>
                    <?php
                    if($model->status == "sent" || $model->status == "read"){
                        //存為草稿
                        echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('misc','Save'), array(
                            'submit'=>Yii::app()->createUrl('delivery/save')));

                        //发货
                        echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('procurement','Shipments'), array(
                            'submit'=>Yii::app()->createUrl('delivery/audit')));
                    }
                    if($model->status == "approve"){
                        //退回
                        echo TbHtml::button('<span class="fa fa-backward"></span> '.Yii::t('procurement','Back Status'), array(
                            'submit'=>Yii::app()->createUrl('delivery/backward')));
                    }
                    ?>
                <?php endif ?>
            </div>

            <div class="btn-group pull-right" role="group">
                <?php if ($model->scenario!='new'){
                    if(($model->status == "sent" || $model->status == "read")&&$model->scenario!='view') {
                        //拒絕
/*                        echo TbHtml::button('<span class="fa fa-mail-reply-all"></span> ' . Yii::t('procurement', 'Reject'), array(
                            'submit' => Yii::app()->createUrl('delivery/reject')));*/
                        echo TbHtml::button('<span class="fa fa-mail-reply-all"></span> '.Yii::t('procurement','Reject'), array(
                            'name'=>'btnJect','id'=>'btnJect','data-toggle'=>'modal','data-target'=>'#jectdialog'));
                    }
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
                <?php echo $form->labelEx($model,'lcd',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'lcd',
                        array('readonly'=>true)
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'lcu',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'lcu',
                        array('readonly'=>true)
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'goods_list',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-10">
                    <table class="table table-bordered table-striped disabled" id="table-change">
                        <thead>
                        <tr>
                            <td width="15%"><?php echo Yii::t("procurement","Goods Name")?></td>
                            <td width="8%"><?php echo Yii::t("procurement","Unit")?></td>
                            <td width="8%"><?php echo Yii::t("procurement","Inventory")?></td>
                            <td width="12%"><?php echo Yii::t("procurement","Demand Note")?></td>
                            <td width="12%"><?php echo Yii::t("procurement","Headquarters Note")?></td>
                            <td width="8%"><?php echo Yii::t("procurement","Goods Number")?></td>
                            <td width="8%"><?php echo Yii::t("procurement","Actual Number")?></td>
                            <?php if ($model->status == "finished"): ?>
                                <td width="1%">&nbsp;</td>
                            <?php endif ?>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        foreach ($model->goods_list as $key => $val){
                            $con_num = empty($val['id'])?$key:$val['id'];
                            $tableTr = "<tr datanum='$con_num'>";
                            $val['confirm_num'] = (empty($val['confirm_num']) && $val['confirm_num'] !== "0")?$val['goods_num']:$val['confirm_num'];
                            $tableTr.="<td><input type='text' class='form-control testInput spanInput' readonly name='DeliveryForm[goods_list][$con_num][name]' value='".$val['name']."'>";
                            $tableTr.="<input type='hidden' name='DeliveryForm[goods_list][$con_num][goods_id]' value='".$val['goods_id']."'>";
                            $tableTr.="<input type='hidden' name='DeliveryForm[goods_list][$con_num][id]' value='".$val['id']."'></td>";
                            $tableTr.="<td><input type='text' class='form-control unit spanInput' readonly name='DeliveryForm[goods_list][$con_num][unit]' value='".$val['unit']."'></td>";
                            $tableTr.="<td><input type='text' class='form-control unit spanInput' readonly name='DeliveryForm[goods_list][$con_num][inventory]' value='".$val['inventory']."'></td>";
                            $tableTr.="<td><input type='text' class='form-control spanInput' readonly name='DeliveryForm[goods_list][$con_num][note]' value='".$val['note']."'></td>";
                            if($model->status == "sent" || $model->status == "read"){
                                $tableTr.="<td><input type='text' class='form-control' name='DeliveryForm[goods_list][$con_num][remark]' value='".$val['remark']."'></td>";
                            }else{
                                $tableTr.="<td><input type='text' class='form-control spanInput' readonly name='DeliveryForm[goods_list][$con_num][remark]' value='".$val['remark']."'></td>";
                            }


                            $tableTr.="<td><input type='number' class='form-control spanInput' readonly name='DeliveryForm[goods_list][$con_num][goods_num]' value='".$val['goods_num']."'></td>";
                            if($model->status == "sent" || $model->status == "read"){
                                $tableTr.="<td><input type='number' class='form-control numChange goods_num' name='DeliveryForm[goods_list][$con_num][confirm_num]' value='".$val['confirm_num']."'></td>";
                            }else{
                                $tableTr.="<td><input type='number' class='form-control numChange goods_num spanInput' readonly name='DeliveryForm[goods_list][$con_num][confirm_num]' value='".$val['confirm_num']."'></td>";
                            }

                            if($model->status == "finished"){
                                //退回
                                $tableTr.="<td>";
                                $tableTr.="<a class='btn btn-danger go-balck' data-id='".$val["id"]."'>".Yii::t('procurement','Back Status')."</a>";
                                $tableTr.="</td>";
                            }
                            $tableTr.="</tr>";
                            echo $tableTr;
                        }
                        ?>
                        </tbody>
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
$this->renderPartial('//site/ject',array('model'=>$model,'form'=>$form,'submit'=>Yii::app()->createUrl('delivery/reject')));
?>
<?php
$js = "
$('.go-balck').on('click',function(){
    var trObj = $(this).parents('tr:first');
    var id = $(this).data('id');
    var name = trObj.find('.testInput').val();
    var num = trObj.find('.goods_num').val();
    $('#black_form input[name=\"black_id\"]').val(id);
    $('#black_form input[name=\"name\"]').val(name);
    $('#black_form input[name=\"num\"]').attr('max',num);
    $('#black_form').modal('show');
});
$('.spanInput').each(function(){
    var text = $(this).val();
    $(this).hide();
    $(this).parent('td').append('<span class=\'input-text-span\'>'+text+'</span>');
});
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

<?php if ($model->status == "finished"): ?>
    <form id="black_form" role="dialog" tabindex="-1" method="post" class="modal fade form-horizontal" action="<?php echo Yii::app()->createUrl('delivery/black');?>">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <input type="hidden" name="id" value="<?php echo $model->id;?>">
                    <input type="hidden" name="black_id">
                    <button class="close" data-dismiss="modal" type="button">×</button>
                    <h4 class="modal-title">退回</h4></div><div class="modal-body"><p></p>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="DeliveryForm_lcd">物品名称</label>
                        <div class="col-sm-4">
                            <input class="form-control" type="text" readonly name="name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="DeliveryForm_lcd">退回数量</label>
                        <div class="col-sm-4">
                            <input class="form-control" name="num" type="number" max="3">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-define pull-left" type="button">关闭</button>
                    <button class="btn btn-primary" type="submit">提交</button>
                </div>
            </div>
        </div>
    </form>
<?php endif ?>
