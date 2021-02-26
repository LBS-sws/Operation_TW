var repeatId = [];//物品的所有Id

function changeRepeatId() {
    var $brotherInput = $("#table-change input.testInput");
    repeatId = [];
    $brotherInput.each(function () {
        var goods_id = $(this).next("input").val();
        repeatId.push(goods_id);
    })
}

function inputDownList(arr,fn){

    //獲取焦點后
    $("body").delegate(".testInput","focus",function (e) {
        var $that = $(this);
        var div = document.createElement("div");
        var ul = document.createElement("ul");
        $(div).addClass("dropdown-div").css({
            top:$that.offset().top+$that.outerHeight(),
            left:$that.offset().left
        });
        $(ul).addClass("dropdown-menu");
        $(div).append(ul);
        fillToUl(ul,$that);
        $("body").append(div);
        $("body").on("click.menu",function () {
            $(div).remove();
            $("body").off(".menu");
            $that.off("keyup");
            $(".dropdown-div>.dropdown-menu a").undelegate("mousedown");
            validateGoods($that);
            changeRepeatId();
        });
        //選擇菜單的某個元素
        $(".dropdown-div>.dropdown-menu").delegate("a","mousedown",function () {
            $that.val($(this).parent("li").attr("dataname"));
            $that.next("input").val($(this).parent("li").attr("dataid"));
            if(fn != undefined){
                fn($that,$(this).parent("li"));
            }
        });
        //鍵盤事件下拉菜單發送變化
        $(this).on("keyup",function () {
            $(ul).html("");
            fillToUl(ul,$that);
        });

        if($(this).val() != "" && $(this).val() != undefined){
            $(this).trigger("keyup");
        }
    });

    //終止輸入框的事件冒泡
    $("body").delegate(".testInput","click",function (e) {
        e.preventDefault();
        return false;
    });

    //輸入框的驗證
    function validateGoods($element) {
        var id = $element.next("input").val();
        var name = $element.val();
        var bool = true;
        var lastname = "";
        $.each(arr,function (index, obj) {
            if(obj["id"] == id){
                lastname = obj["name"];
                if(lastname == name){
                    bool = false;
                    return false;
                }
            }
        });
        if(bool){
            $element.val(lastname);
        }
    }

    //數據填充
    function fillToUl(ul,$doc) {
        $.each( arr, function(key, val){
            var str = $doc.val();
            var classify = $doc.parent().find(".btn:first").attr("data-id");
            if(val["name"].split(str).length > 1 || str == ""){
                if(classify != val["classify_id"] && classify != "" && classify != undefined){
                    return true;
                }
                if(($.inArray(val["id"],repeatId))>=0 && $doc.next("input").val() != val["id"]){
                    //去除已經存在的物品
                    return true;
                }
                var li = document.createElement("li");
                var content = typeof(val["stickies_id"])=="undefined"?"":val["stickies_id"];
                $(li).attr({
                    "dataid":val["id"],
                    "datacode":val["goods_code"],
                    "dataname":val["name"],
                    "dataunit":val["unit"],
                    "dataprice":val["price"],
                    "classify":val["classify_id"],
                    "content":content,
                    "datatype":val["type"]
                }).append("<a href='javascript:void(0);'>"+val["name"]+"</a>");
                $(ul).append(li);
            }
        });
    }

}
//表格內的物品發生變化，價格隨之變化
function tableGoodsChange($ele,$li) {
    var $tr = $ele.parents("tr");
    var classify = $("#classifyList li[data-id='"+$li.attr("classify")+"']").text();
    if($tr.length > 0){
        var $tr = $ele.parents("tr");
        $tr.find("input.name").val($li.attr("dataname"));
        $tr.find("input.type").val($li.attr("datatype"));
        $tr.find("input.unit").val($li.attr("dataunit"));
        $tr.find("input.classify_id").val($li.attr("classify"));
        $tr.find("input.stickies_id").val($li.attr("content"));
        $tr.find(".bg-fff.dropdown-toggle").attr("data-id",$li.attr("classify"));
        $tr.find(".bg-fff.dropdown-toggle>span:first").text(classify);
        if($li.attr("content")!=""&&$li.attr("content")!=undefined){
            if($ele.parent().find("div.changeHelp").length>0){
                $ele.parent().find("div.changeHelp").attr("content-id",$li.attr("content"));
            }else{
                $ele.parent().append('<div class="input-group-btn changeHelp" content-id="'+$li.attr("content")+'"><span class="fa fa-exclamation-circle"></span></div>');
            }
        }else{
            $ele.parent().find("div.changeHelp").remove();
        }
        var price = $li.attr("dataprice");
        price = addStringToNum(price);
        $tr.find("input.price").val(price);
        goodsTotalPrice();
        changeRepeatId();
    }
}
//
function addStringToNum(str) {
    if(str == "" || str == 0 || str == undefined ||isNaN(str)){
        return "0.00";
    }
    str = Math.round(str*100).toString();
    var one = str.slice(0,-2);
    var two = str.slice(-2);
    return one+"."+two;
}
//添加標籤提示
function addContentHelp($ele,stickiesList) {
    var content_id = $ele.attr("content-id");
    if(typeof (stickiesList[content_id]) === "string"){
        var html = "<div class='content-help'>"+stickiesList[content_id]+"</div>";
        $ele.append(html);
    }
}


//價格計算
function goodsTotalPrice() {
    var $table = $("#table-change>tbody");
    var totalPrice = 0;
    $table.find("tr").each(function () {
        var price = $(this).find("input.price").val();
        var num = $(this).find("input.goods_num:last").val();
        var sum = 0;
        var tem = "";

        if(price == "" || isNaN(price)){
            price = 0;
        }else{
            price = parseFloat(price);
        }
        if(num == "" || isNaN(num)){
            num = 0;
        }else{
            num = parseFloat(num);
        }

        sum = (price*100000)*num/100000;
        sum = addStringToNum(sum);
        totalPrice=(sum*100000 +totalPrice*100000)/100000;
        totalPrice = addStringToNum(totalPrice);
        $(this).find("input.sum").val(sum);
    });
    $("#table-change>tfoot>tr>td").eq(1).text(totalPrice);
}

//向表格內添加物品
function addGoodsTable(data) {
    if($(this).prop("disabled")){
        return false;
    }
    var num = $("#table-change>tbody>tr:last").attr("datanum");
    num = $("#table-change>tbody>tr").length < 1?0:num;
    if(num == undefined && num == "" && num == undefined && isNaN(num)){
        alert("添加異常，請刷新頁面");
        return false;
    }
    num = parseInt(num)+1;
    $("#classifyList input.stickies_id").attr("name",'OrderForm[goods_list]['+num+'][stickies_id]');
    $("#classifyList input.classify_id").attr("name",'OrderForm[goods_list]['+num+'][classify_id]');
    var html ='<tr datanum="'+num+'">'+
        '<td><div class="input-group">' +$("#classifyList").html()+
        '<input type="text" class="form-control testInput" autocomplete="off" name="OrderForm[goods_list]['+num+'][name]" >' +
        '<input type="hidden" name="OrderForm[goods_list]['+num+'][goods_id]"></div></td>'+
        '<td><input type="text" class="form-control type" name="OrderForm[goods_list]['+num+'][type]" readonly></td>'+
        '<td><input type="text" class="form-control unit" name="OrderForm[goods_list]['+num+'][unit]" readonly></td>'+
        '<td><input type="text" class="form-control" name="OrderForm[goods_list]['+num+'][note]"></td>'+
        '<td><input type="text" class="form-control price" name="OrderForm[goods_list]['+num+'][price]" readonly></td>'+
        '<td><input type="number" min="0" class="form-control numChange goods_num" name="OrderForm[goods_list]['+num+'][goods_num]"></td>'+
        '<td><input type="text" class="form-control sum" readonly></td>'+
        '<td><button type="button" class="btn btn-danger delGoods">'+data.data.btnStr+'</button></td>'+
    '</tr>';

    $("#classifyList input.stickies_id").removeAttr("name");
    $("#classifyList input.classify_id").removeAttr("name");
    $("#table-change>tbody").append(html);
    changeRepeatId();
}

//刪除表格里的某條物品
function delGoodsTable(data) {
    if($(this).prop("disabled")){
        return false;
    }
    var dataId = $(this).next("input");
    if(dataId.length < 1){
        $(this).parents("tr").remove();
    }else{
        if(confirm(data.data)){
            $.ajax({
                type: "post",
                url: "./OrderGoodsDelete",
                data: {id:dataId.val()},
                dataType: "json",
                success: function(data){
                    if(data.status == 1){
                        dataId.parents("tr").remove();
                    }
                }
            });
        }
    }
}


//控制表格能否輸入
function disabledTable(bool) {
    switch (bool){
        case 1:
            $("#table-change").find("input").prop("disabled",true);
            $("#table-change").find("button").prop("disabled",true);
            break
        case 2:
            $("#table-change").find("input").prop("readonly",true);
            $("#table-change").find("button").prop("disabled",true);
            break
    }
}


function goodsIfyChange() {
    $("body").delegate(".goodsIfy>li","click",function () {
        var dataId = $(this).data("id");
        var $btn = $(this).parents(".input-group-btn:first").find(".btn:first");
        var oldDataId = $btn.attr("data-id");
        if(oldDataId != "" && oldDataId!= undefined &&oldDataId != dataId){
            $(this).parents("tr:first").find(".testInput").next("input").val("");
            $(this).parents("tr:first").find(".testInput").val("").focus().blur();
            $("body").trigger("click");
        }
        $(this).parents("tr:first").find("input.classify_id").val(dataId);
        $btn.find("span:first").text($(this).text());
        $btn.attr("data-id",dataId);
    });
}



function validateGoods(url) {
    var bool = false;
    $(".numChange").each(function () {
        if($(this).val() == ""){
            bool = true;
            return false;
        }
    });
    if(bool){
        return false;
    }
    $.ajax({
        type: "post",
        url: url,
        data: $("form:first").serialize(),
        dataType: "json",
        success: function(data){
            if(data.status == 0){
                if($("#shenValidate").length > 0){
                    $("#shenValidate").remove();
                }
                var html = '<div id="shenValidate" role="dialog" tabindex="-1" class="modal fade" style="display: block; padding-right: 17px;">';
                html+='<div class="modal-dialog">';
                html+='<div class="modal-content">';
                html+='<div class="modal-header"><button class="close" data-dismiss="modal" type="button">×</button><h4 class="modal-title">验证信息</h4></div><div class="modal-body"><p></p>';
                html+=data.error;
                html+='<p></p></div><div class="modal-footer"><button data-dismiss="modal" class="btn btn-primary" type="button">确定</button></div></div>';
                html+='</div>';
                html+='</div>';
                $("body").append(html);
                $("#shenValidate").addClass("in");
            }
        }
    });
}

