<?php
$this->pageTitle=Yii::app()->name . ' - Cargo Cost List';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'cargoCost-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Technician cargo cost'); ?></strong>
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
	<?php
    $search_add_html="";
    if(!Yii::app()->user->isSingleCity()){
        $search_add_html .= TbHtml::dropDownList('CargoCostList[city]',$model->city,$model->getCityAllList(),
            array("class"=>"form-control","id"=>"change_city"))."<span style='display:inline-block;width:20px;'>&nbsp;</span>";
    }
    $search_add_html .= TbHtml::dropDownList('CargoCostList[year]',$model->year,$model->getYearList(),
            array("class"=>"form-control"))."<span style='display:inline-block;width:20px;'>&nbsp;</span>";
    $search_add_html .= TbHtml::dropDownList('CargoCostList[month]',$model->month,$model->getMonthList(),
            array("class"=>"form-control"))."<span style='display:inline-block;width:20px;'>&nbsp;</span>";
    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('procurement','Technician Order List')." - ".Yii::t('procurement','total price')."：".$model->total_price,
        'model'=>$model,
        'viewhdr'=>'//cargoCost/_listhdr',
        'viewdtl'=>'//cargoCost/_listdtl',
        'search_add_html'=>$search_add_html,
        'search'=>array(
            'city',
            'lcu',
        ),
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

