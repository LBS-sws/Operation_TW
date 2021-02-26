<?php
$this->pageTitle=Yii::app()->name . ' - Technician List';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'technician-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('procurement','Technician'); ?></strong>
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
                //var_dump(Yii::app()->session['rw_func']);
                if (Yii::app()->user->validRWFunction('YC02'))
                    echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('procurement','Add Order'), array(
                        'submit'=>Yii::app()->createUrl('technician/new'),
                    ));
                ?>
            </div>
        </div>
    </div>
	<?php
    $search_add_html="";
    $search_add_html .= TbHtml::textField('TechnicianList[searchTimeStart]',$model->searchTimeStart,
        array('size'=>15,'placeholder'=>Yii::t('misc','Start Date'),"class"=>"form-control","id"=>"start_time"));
    $search_add_html.="<span>&nbsp;&nbsp;-&nbsp;&nbsp;</span>";
    $search_add_html .= TbHtml::textField('TechnicianList[searchTimeEnd]',$model->searchTimeEnd,
        array('size'=>15,'placeholder'=>Yii::t('misc','End Date'),"class"=>"form-control","id"=>"end_time"));

    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('procurement','Order List'),
        'model'=>$model,
        'viewhdr'=>'//technician/_listhdr',
        'viewdtl'=>'//technician/_listdtl',
        'search_add_html'=>$search_add_html,
        'search'=>array(
            'order_code',
            'goods_name',
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
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
	$js = Script::genTableRowClick();
	Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

