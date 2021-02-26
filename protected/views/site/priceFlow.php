<?php
$ftrbtn = array();
$ftrbtn[] = TbHtml::button(Yii::t('dialog','Close'), array('id'=>'btnWFClose','data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY));
$this->beginWidget('bootstrap.widgets.TbModal', array(
    'id'=>'priceFlow',
    'header'=>Yii::t('procurement','price history'),
    'footer'=>$ftrbtn,
    'show'=>false,
));
?>

<div class="box" id="flow-list" style="max-height: 300px; overflow-y: auto;">
    <table id="tblPriceFlow" class="table table-bordered table-striped table-hover">
        <thead>
        <tr>
            <th><?php echo Yii::t("procurement","Goods Code"); ?></th>
            <th><?php echo Yii::t("procurement","Goods Name"); ?></th>
            <th><?php echo Yii::t("workflow","Year/Month"); ?></th>
            <th><?php echo Yii::t("procurement","Price（RMB）"); ?></th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<?php
$this->endWidget();
?>

<script>
    function printPriceTable(id) {
        $('#priceFlow').modal('show');
        $("#tblPriceFlow>tbody").html('<span>加载中....</span>');
        if(id==undefined||id==null){
            id = 0;
        }
        $.ajax({
            type: 'GET',
            url: '<?php echo Yii::app()->createUrl('warehouse/ajaxPriceHistory');?>',
            data: {
                'id':id
            },
            dataType: 'json',
            success: function(data) {
                if(data.status == 1){
                    $("#tblPriceFlow>tbody").html(data.html);
                }else{
                    alert('數據異常');
                }
            },
            error: function(data) { // if error occured
                alert('Error occured.please try again');
            }
        });
    }
    $(function () {
        $(".clickPriceBtn").on("click",function (e) {
            var id = $(this).data("id");
            printPriceTable(id);
            e.stopPropagation();
        })
    })
</script>
