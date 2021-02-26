<?php
$this->pageTitle=Yii::app()->name . ' - Delivery List';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'delivery-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Technician take Goods'); ?></strong>
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
                echo TbHtml::button('<span class="fa fa-paper-plane"></span> '.Yii::t('procurement','All shipments'), array(
                    'submit'=>Yii::app()->createUrl('delivery/allApproved'),
                ));
                ?>
            </div>
            <div class="btn-group pull-right" role="group">
                <?php
                echo TbHtml::button('<span class="fa fa-cloud-download"></span> '.Yii::t('misc','Download'), array(
                    'data-toggle'=>'modal','data-target'=>'#deliveryDownModel'
                ));
                ?>
            </div>
        </div>
    </div>
	<?php
    $search_add_html="";
    if(!Yii::app()->user->isSingleCity()){
        $search_add_html .= TbHtml::dropDownList('DeliveryList[city]',$model->city,$model->getCityAllList(),
            array("class"=>"form-control","id"=>"change_city"))."<span style='display:inline-block;width:20px;'>&nbsp;</span>";
    }
    $search_add_html .= TbHtml::textField('DeliveryList[searchTimeStart]',$model->searchTimeStart,
        array('size'=>15,'placeholder'=>Yii::t('misc','Start Date'),"class"=>"form-control","id"=>"start_time","style"=>"width:100px;"));
    $search_add_html.="<span>&nbsp;&nbsp;-&nbsp;&nbsp;</span>";
    $search_add_html .= TbHtml::textField('DeliveryList[searchTimeEnd]',$model->searchTimeEnd,
        array('size'=>15,'placeholder'=>Yii::t('misc','End Date'),"class"=>"form-control","id"=>"end_time","style"=>"width:100px;"));
    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('procurement','Order List'),
        'model'=>$model,
        'viewhdr'=>'//delivery/_listhdr',
        'viewdtl'=>'//delivery/_listdtl',
        'search_add_html'=>$search_add_html,
        'search'=>array(
            'order_code',
            'lcu',
            'goods_name',
            'status',
        ),
		'hasDateButton'=>true,
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
$js = "
$('#start_time').datepicker({autoclose: true, format: 'yyyy/mm/dd',language: 'zh_cn'});
$('#end_time').datepicker({autoclose: true, format: 'yyyy/mm/dd',language: 'zh_cn'});
$('.checkBoxDown').on('click',function(e){
    e.stopPropagation();
});
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
	$js = Script::genTableRowClick();
	Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

<form class="form-horizontal" action="<?php echo Yii::app()->createUrl('delivery/allDownload');?>" method="get">
    <?php
    $ftrbtn = array();
    $ftrbtn[] = TbHtml::button(Yii::t('dialog','Close'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_DEFAULT,"class"=>"pull-left"));
    $ftrbtn[] = TbHtml::button(Yii::t('procurement','Submit'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY,'submit' => Yii::app()->createUrl('delivery/allDownload')));
    $this->beginWidget('bootstrap.widgets.TbModal', array(
        'id'=>'deliveryDownModel',
        'header'=>Yii::t('misc','Download'),
        'footer'=>$ftrbtn,
        'show'=>false,
    ));
    ?>

    <div class="form-group">
        <?php echo TbHtml::label(Yii::t('procurement','Order Status'),"downType",array('class'=>"col-sm-2 control-label")); ?>
        <div class="col-sm-9">
            <?php echo TbHtml::dropDownList('downType',0,DeliveryForm::downTypeList(),
                array('readonly'=>(false))
            ); ?>
        </div>
    </div>

    <?php $this->endWidget(); ?>
</form>
