
function waitingMessageHeader(url,str) {
    //console.log(str);
	// Percy - 因营业报告审核的數量, jquery加了條件
    var $message = $("#navbar-collapse a[href='"+str+"/index.php/purchase/index'],#navbar-collapse a[href='"+str+"/index.php/monthly/indexa']");
    if($message.length >= 1){
        $.ajax({
            type: "post",
            url: url,
            data: {id:"",time:new Date()},
            dataType: "json",
            success: function(data){
                //console.log(data);
                if(data["fast_num"] != 0){
                    $("#navbar-collapse a[href='"+str+"/index.php/fast/index']").append("<span class='badge'>"+data["fast_num"]+"</span>");
                }
                if(data["imDo_num"] != 0){
                    $("#navbar-collapse a[href='"+str+"/index.php/purchase/index']").append("<span class='badge'>"+data["imDo_num"]+"</span>");
                }
                if(data["take_num"] != 0){
                    $("#navbar-collapse a[href='"+str+"/index.php/order/index']").append("<span class='badge'>"+data["take_num"]+"</span>");
                }
                if(data["deli_num"] != 0){
                    $("#navbar-collapse a[href='"+str+"/index.php/delivery/index']").append("<span class='badge'>"+data["deli_num"]+"</span>");
                }
                if(data["area_num"] != 0){
                    $("#navbar-collapse a[href='"+str+"/index.php/areaAudit/index']").append("<span class='badge'>"+data["area_num"]+"</span>");
                }
                if(data["goods_num"] != 0){
                    $message = $("#navbar-collapse a[href='"+str+"/index.php/technician/index']");
                    $message.append("<span class='badge'>"+data["goods_num"]+"</span>");
                    $message.parents("li.dropdown").find("a:first>span:first").before("<span class='badge'>"+data["goods_num"]+"</span>");
                }
                if(data["fast_num"] != 0 || data["imDo_num"] != 0){
                    $message = $("#navbar-collapse a[href='"+str+"/index.php/purchase/index']");
                    $message.parents("li.dropdown").find("a:first>span:first").before("<span class='badge'>"+(parseInt(data["fast_num"],10)+parseInt(data["imDo_num"],10))+"</span>");
                }
                if(data["take_num"] != 0 || data["deli_num"] != 0 || data["area_num"] != 0){
                    $message = $("#navbar-collapse a[href='"+str+"/index.php/order/index']");
                    $message.parents("li.dropdown").find("a:first>span:first").before("<span class='badge'>"+(parseInt(data["take_num"],10)+parseInt(data["deli_num"],10)+parseInt(data["area_num"],10))+"</span>");
                }
//营业报告审核的數量				
                if(data["rep_num"] != 0){
                    $message = $("#navbar-collapse a[href='"+str+"/index.php/monthly/indexa']");
                    $message.append("<span class='badge'>"+data["rep_num"]+"</span>");
                    $message.parents("li.dropdown").find("a:first>span:first").before("<span class='badge'>"+data["rep_num"]+"</span>");
                }
            }
        });
    }
}
//<kbd>cd</kbd>