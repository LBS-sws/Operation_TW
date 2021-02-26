<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('purchase/index'));
}
$this->pageTitle=Yii::app()->name . ' - Purchase Form';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
    'id'=>'purchase-form',
    'enableClientValidation'=>true,
    'clientOptions'=>array('validateOnSubmit'=>true,),
    'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
    <h1>
        <strong><?php echo Yii::t('procurement','Purchase Form'); ?></strong>
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
                    'submit'=>Yii::app()->createUrl('areaAudit/index')));
                ?>
                <?php if ($model->scenario!='view'): ?>
                    <?php
                    if($model->status == "sent" && $model->status_type != 1){
                        //審核
                        echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('workflow','approval'), array(
                            'submit'=>Yii::app()->createUrl('areaAudit/audit')));
                    }
                    ?>
                <?php endif ?>
            </div>

            <div class="btn-group pull-right" role="group">
                <?php
                if ($model->scenario!='new'){
                    if($model->status == "sent" && $model->status_type != 1) {
                        echo TbHtml::button('<span class="fa fa-mail-reply-all"></span> '.Yii::t('procurement','Reject'), array(
                            'name'=>'btnJect','id'=>'btnJect','data-toggle'=>'modal','data-target'=>'#jectdialog'));
                    }
                    //流程
                    echo TbHtml::button('<span class="fa fa-file-text-o"></span> '.Yii::t('misc','Flow'), array(
                        'name'=>'btnFlow','id'=>'btnFlow','data-toggle'=>'modal','data-target'=>'#flowinfodialog'));

                    //下載
                    echo TbHtml::button('<span class="fa fa-cloud-download"></span> '.Yii::t('procurement','Down'), array(
                        'submit'=>Yii::app()->createUrl('Purchase/downorder',array("index"=>$model->id))));
                }
                ?>
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
                            <?php
                            $currencyType = $model->order_class=="Domestic"?"RMB":"US$";
                            ?>
                            <td width="20%"><?php echo Yii::t("procurement","Goods Name")?></td>
                            <td width="11%"><?php echo Yii::t("procurement","Type")?></td>
                            <td width="8%"><?php echo Yii::t("procurement","Unit")?></td>
                            <td width="10%"><?php echo Yii::t("procurement","Demand Note")?></td>

                            <td width="10%"><?php echo Yii::t("procurement","Price（".$currencyType."）")?></td>
                            <td width="10%"><?php echo Yii::t("procurement","Goods Number")?></td>
                            <td width="10%"><?php echo Yii::t("procurement","Total（".$currencyType."）")?></td>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        $classify = ClassifyForm::getClassifyList();
                        $stickiesContentList = StickiesForm::getStickiesContentList();
                        foreach ($model->goods_list as $key => $val){
                            $con_num = empty($val['id'])?$key:$val['id'];
                            $tableTr = "<tr datanum='$con_num'>";

                            $tableTr.="<td><div class='input-group'>";
                            $tableTr.="<input type='hidden' value='".$val['id']."'>";
                            $tableTr.="<input type='hidden' class='stickies_id' value='".$val['stickies_id']."'>";
                            $tableTr.="<input type='text' readonly class='form-control testInput' value='".$val['name']."'>";
                            $tableTr.="<input type='hidden' value='".$val['goods_id']."'>";
                            if(!empty($val['stickies_id'])){
                                $tableTr.='<div class="input-group-btn changeHelp" content-id="'.$val['stickies_id'].'"><span class="fa fa-exclamation-circle"></span></div>';
                            }
                            $tableTr.="</div></td>";

                            $tableTr.="<td><input type='text' class='form-control type' readonly value='".$val['type']."'></td>";
                            $tableTr.="<td><input type='text' class='form-control unit' readonly value='".$val['unit']."'></td>";
                            $tableTr.="<td><input type='text' class='form-control' readonly value='".$val['note']."'></td>";
                            $tableTr.="<td><input type='text' class='form-control price' readonly value='".sprintf("%.2f", $val['price'])."'></td>";
                            $tableTr.="<td><input type='number' class='form-control numChange goods_num' readonly value='".$val['goods_num']."'></td>";
                            $tableTr.="<td><input type='text' class='form-control sum' readonly></td>";

                            $tableTr.="</tr>";
                            echo $tableTr;
                        }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="6"></td>
                            <td class="text-success fa-2x">0</td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'activity_id',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <input class="form-control" type="text" readonly value="<?php echo OrderList::getActivityTitleToId($model->activity_id);?>">
                </div>
            </div>

            <!--備註-->
            <div class="form-group">
                <?php echo $form->labelEx($model,'remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-7">
                    <?php echo $form->textArea($model, 'remark',
                        array('rows'=>4,'cols'=>50,'maxlength'=>1000,'readonly'=>(true))
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
$this->renderPartial('//site/ject',array('model'=>$model,'form'=>$form,'submit'=>Yii::app()->createUrl('areaAudit/reject')));
?>
<?php
$js = '
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


