$(function () {
    var goodsList = "";//所有物品列表（按物品分類劃分的二維數組）
    var selectGoodsList = [];//
    jQuery('#selectGoods_div').modal({'backdrop':true,'keyboard':true,'show':false});
    // 選擇框彈出後
    $('#selectGoods_div').on('show.bs.modal', function () {
        //獲取已經選擇的物品
        selectGoodsList = [];
        $("#table-change>tbody>tr").each(function () {
            var id = $(this).find("input.select_id:first").val();
            var num = $(this).find("input.select_num:first").val();
            var remark = $(this).find("input.select_remark:first").val();
            var classify_id = $(this).data("classify");
            if(id == undefined || id=="" || id==null){
                return true;
            }
            selectGoodsList.push({"id":id,"num":num,"remark":remark,"classify_id":classify_id});
        });
        //獲取所有物品
        tableChange();
        //調整表格高度
        var height = $(window).height() ;
        height = height/3.4 + "px";
        $("#selectGoods_table").parent("div").css("height",height);
    });

    //確定物品后，一次性輸入所有選擇物品
    $('#selectGoods_div .btn-primary').on('click', function () {
        var tBody = $("#table-change>tbody");
        if(selectGoodsList.length != 0){
            tBody.html("");
            $.each(selectGoodsList,function (key, value) {
                if(value == undefined || value=="" || value==null){
                    return true;
                }
                var classify_id = value["classify_id"];
                var id = value["id"];
                var goods = goodsList[classify_id]["goods"][id];
                var html = "<tr data-classify='"+classify_id+"'>";
                html+="<td>";
                html+="<input type='hidden' name='TechnicianForm[goods_list]["+key+"][goods_id]' class='select_id' value='"+id+"'>";
                html+="<input type='hidden' name='TechnicianForm[goods_list]["+key+"][name]' value='"+goods["name"]+"'>";
                html+="<input type='hidden' name='TechnicianForm[goods_list]["+key+"][unit]' value='"+goods["unit"]+"'>";
                html+="<input type='hidden' name='TechnicianForm[goods_list]["+key+"][classify_id]' value='"+classify_id+"'>";
                html+=goods["name"];
                html+="</td>";
                html+="<td>"+goods["unit"]+"</td>";
                html+="<td><input class='form-control select_remark' name='TechnicianForm[goods_list]["+key+"][note]' type='text' value='"+value["remark"]+"'></td>";
                html+="<td><input class='form-control select_num' name='TechnicianForm[goods_list]["+key+"][goods_num]' type='number' value='"+value["num"]+"'></td>";
                html+="<td><a class='btn btn-danger goodsDelete' data-id='"+id+"'>刪除</a></td>";
                html+="</tr>";
                tBody.append(html);
            });
        }

        $('#selectGoods_div').modal('hide')
    });

    //獲取物品列表
    function getGoodsList() {
        var list = [];
        $("#goodsListToClassify>li").each(function ($index, $obj) {
            var id = $(this).find("ul>li").eq(0).text();
            var name = $(this).find("ul>li").eq(1).text();
            var goodsObj = $(this).find("ul>li").eq(2);
            var goods =[];
            if(goodsObj.has("ul")){
                goodsObj.find("ul").each(function () {
                    var objList = {};
                    $(this).find("li").each(function () {
                        var objName = $(this).data("str");
                        if(objName==""||objName == undefined||objName ==null){
                            return true;
                        }
                        objList[objName] = $(this).text();
                    });
                    goods[objList["id"]] =objList;
                })
            }
            list[id] ={
                "id":id,
                "name":name,
                "goods":goods
            };
        });
        goodsList = list;
        $("#goodsListToClassify").remove();
    }
    getGoodsList();

    //表格內物品變化
    function tableChange() {
        var classify = $("#selectGoods_select").val();
        var search = $("#selectGoods_search").val();
        var tBody = $("#selectGoods_table>tbody");
        var needGoods = goodsList;
        classify = classify== undefined?0:classify;
        tBody.html("");//初始化表格
        $.each(needGoods,function (key,value) {
            if(classify != key && classify!=0){
                return true;
            }
            if(value == undefined){
                return true;
            }
            var goods = value.goods;
            $.each(goods,function (item, val) {
                if(val == undefined){
                    return true;
                }
                var html = "";
                var goodsName = val["name"];
                var boolArr = $.grep(selectGoodsList,function (aaa, bbb) {
                    if(aaa == undefined || aaa==""|| aaa==null){
                        return false;
                    }else{
                        return aaa["id"] == val["id"];
                    }
                })
                html+="<tr>";
                if(boolArr.length>0){
                    html+="<td><input type='checkbox' value='"+val["id"]+"' data-classify='"+key+"' checked></td>";
                }else{
                    html+="<td><input type='checkbox' value='"+val["id"]+"' data-classify='"+key+"'></td>";
                }
                html+="<td>"+val["goods_code"]+"</td>";
                html+="<td>"+goodsName+"</td>";
                html+="<td>"+value.name+"</td>";
                html+="<td>"+val["unit"]+"</td>";
                html+="</tr>";
                if(goodsName.indexOf(search)>=0 || search == 0){
                    tBody.append(html);
                }
            });
        });
    }

    //搜索查詢
    $("#selectGoods_select").on("change",tableChange);
    $("#selectGoods_search").on("keyup",tableChange);

    //勾選物品事件
    $("#selectGoods_table>tbody").delegate("input","click",function () {
        var id = $(this).val();
        var classify_id = $(this).data("classify");
        if($(this).is(":checked")){
            selectGoodsList.push({"id":id,"num":"","remark":"","classify_id":classify_id});
        }else{
            selectGoodsList = $.grep(selectGoodsList,function (value,key) {
                if(value == undefined || value=="" || value==null){
                    return false;
                }else{
                    return value["id"] != id;
                }
            });
        }
    });

    //點擊刪除按鈕
    $("#table-change>tbody").delegate(".goodsDelete","click",function () {
        var id = $(this).data("id");
        selectGoodsList = $.grep(selectGoodsList,function (value,key) {
            if(value == undefined || value=="" || value==null){
                return false;
            }else{
                return value["id"] != id;
            }
        });
        $(this).parents("tr:first").remove();
    });

});