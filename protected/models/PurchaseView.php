<?php

class PurchaseView extends CFormModel
{
	public $id;
	public $activity_code;
	public $activity_title;
	public $start_time;
	public $end_time;
	public $num;
	public $order_class;
    public $city_auth;
    public $city_name="全部";
    public $luu;
    public $lcu;
    public $lud;
    public $lcd;

	public function attributeLabels()
	{
		return array(
            'activity_code'=>Yii::t('procurement','Activity Code'),
            'activity_title'=>Yii::t('procurement','Activity Title'),
            'start_time'=>Yii::t('procurement','Start Time'),
            'end_time'=>Yii::t('procurement','End Time'),
            'num'=>Yii::t('procurement','Number Restrictions'),
            'order_class'=>Yii::t('procurement','Order Class'),
            'city_auth'=>Yii::t('user','City')
		);
	}

    public function retrieveData($index) {
        $city = Yii::app()->user->city();
        $row = Yii::app()->db->createCommand()->select("*")
            ->from("opr_order_activity")->where("id=:id",array(":id"=>$index))->queryRow();
        if ($row) {
            $this->id = $row['id'];
            $this->activity_code = $row['activity_code'];
            $this->activity_title = $row['activity_title'];
            $this->start_time = $row['start_time'];
            $this->end_time = $row['end_time'];
            $this->order_class = $row['order_class'];
            $this->num = $row['num'];
            $this->city_auth = empty($row['city_auth'])?"":substr($row['city_auth'],1,-1);
            $this->city_name =empty($this->city_auth)?"全部":"";
            $cityList = explode("~",$this->city_auth);
            foreach ($cityList as $code){
                $this->city_name.=CGeneral::getCityName($code)." ";
            }
        }
        return true;
    }

	public function getHeaderList() {
		$arr = array(
		    "price_class"=>"Price Class"
        );
		if($this->order_class == "Import"){
		    $arr["weight_class"]="Weight Class";
		    $arr["volume_class"]="Volume Class";
        }
		return $arr;
	}
	public function getBodyList($str) {
	    $html = "";
	    switch ($str){
            case "price_class":
                $goodsClass = $this->getGoodsToActivityIdOnlyPrice($this->id);
                $html="<p>&nbsp;</p>";
                foreach ($goodsClass as $key => $list){
                    $classifyName = ClassifyForm::getClassifyToId($key);
                    $html.="<legend>$classifyName</legend>";
                    $sum = 0;
                    $exSum = 0;
                    $unit = 'US$';
                    if($this->order_class == "Domestic"){
                        $unit = "RMB";
                    }
                    $html.="<table class='table table-bordered'><thead><tr>";
                    $html.="<th>".Yii::t("procurement","Goods Code")."</th>";
                    $html.="<th>".Yii::t("procurement","Goods Name")."</th>";
                    if($this->order_class != "Domestic"){
                        $html.="<th>".Yii::t("procurement","customs code")."</th>";
                        $html.="<th>".Yii::t("procurement","customs name")."</th>";
                        $html.="<th>".Yii::t("procurement","inspection")."</th>";
                    }
                    $html.="<th>".Yii::t("procurement",'Price（'.$unit.'）')."</th>";
                    $html.="<th>".Yii::t("procurement","Goods Number")."</th>";
                    $html.="<th>".Yii::t("procurement","Confirm Number")."</th>";
                    $html.="<th>".Yii::t("procurement",'expected to price（'.$unit.'）')."</th>";
                    $html.="<th>".Yii::t("procurement",'Total（'.$unit.'）')."</th>";
                    $html.="</tr></thead><tbody>";
                    foreach ($list as $goods_price){
                        $num = 0;
                        $count = count($goods_price);
                        foreach ($goods_price as $goods){
                            //sprintf(".2%",$aaa);
                            $sum+=floatval($goods["price"])*intval($goods["confirm_num"]);
                            $exSum+=intval($goods["goods_num"])*floatval($goods["price"]);
                            $html.="<tr>";
                            if($num == 0){
                                $html.="<td rowspan='$count' style='vertical-align: middle'>".$goods["goods_code"]."</td>";
                                $html.="<td rowspan='$count' style='vertical-align: middle'>".$goods["name"]."</td>";
                                if($this->order_class != "Domestic"){
                                    $html.="<td rowspan='$count' style='vertical-align: middle'>".$goods["customs_code"]."</td>";
                                    $html.="<td rowspan='$count' style='vertical-align: middle'>".$goods["customs_name"]."</td>";
                                    $html.="<td rowspan='$count' style='vertical-align: middle'>".$goods["inspection"]."</td>";
                                }
                            }
                            $html.="<td>".sprintf("%.2f",$goods["price"])."</td>";
                            $html.="<td>".$goods["goods_num"]."</td>";
                            $html.="<td>".$goods["confirm_num"]."</td>";
                            $html.="<td>".sprintf("%.2f",intval($goods["goods_num"])*floatval($goods["price"]))."</td>";
                            $html.="<td>".sprintf("%.2f",floatval($goods["price"])*intval($goods["confirm_num"]))."</td>";
                            $html.="</tr>";
                            $num++;
                        }
                    }
                    $colspan = $this->order_class != "Domestic"?8:5;
                    $html.="</tbody><tfoot><tr><td colspan='$colspan'></td><td class='fa-2x'>".sprintf("%.2f",$exSum)."</td><td class='fa-2x'>".sprintf("%.2f",$sum)."</td></tr></tfoot></table>";
                }
                //$html.="<h3>&nbsp;</h3>";
                break;
            case "weight_class":
                $goodsClass = $this->getGoodsToActivityId($this->id);
                $html="<p>&nbsp;</p>";
                foreach ($goodsClass as $key => $list){
                    $classifyName = ClassifyForm::getClassifyToId($key);
                    $html.="<legend>$classifyName</legend>";
                    $sum = 0;
                    $exSum = 0;
                    $html.="<table class='table table-bordered table-striped'><thead><tr>
                            <th>".Yii::t("procurement","Goods Code")."</th>
                            <th>".Yii::t("procurement","Goods Name")."</th>
                            <th>".Yii::t("procurement","Type")."</th>
                            <th>".Yii::t("procurement","Unit")."</th>
                            <th>".Yii::t("procurement","Gross Weight（kg）")."</th>
                            <th>".Yii::t("procurement","Net Weight（kg）")."</th>
                            <th>".Yii::t("procurement","Multiple")."</th>
                            <th>".Yii::t("procurement","Confirm Number")."</th>
                            <th>".Yii::t("procurement","Total Gross Weight（kg）")."</th>
                            <th>".Yii::t("procurement","Total Net Weight（kg）")."</th>
                            </tr></thead><tbody>";
                    foreach ($list as $goods){
                        $sum+=floatval($goods["gross_weight"])*intval($goods["confirm_num"])/intval($goods["multiple"]);
                        $exSum+=intval($goods["confirm_num"])*floatval($goods["net_weight"])/intval($goods["multiple"]);
                        $html.="<tr>
                        <td>".$goods["goods_code"]."</td>
                        <td>".$goods["name"]."</td>
                        <td>".$goods["type"]."</td>
                        <td>".$goods["unit"]."</td>
                        <td>".$goods["gross_weight"]."</td>
                        <td>".$goods["net_weight"]."</td>
                        <td>".$goods["multiple"]."</td>
                        <td>".$goods["confirm_num"]."</td>
                        <td>".(floatval($goods["gross_weight"])*(intval($goods["confirm_num"])/intval($goods["multiple"])))."</td>
                        <td>".(floatval($goods["net_weight"])*intval($goods["confirm_num"])/intval($goods["multiple"]))."</td>
                        </tr>";
                    }
                    $html.="</tbody><tfoot><tr><td colspan='8'></td><td class='fa-2x'>$sum</td><td class='fa-2x'>$exSum</td></tr></tfoot></table>";
                }
                break;
            case "volume_class":
                $goodsClass = $this->getGoodsToActivityId($this->id);
                $html="<p>&nbsp;</p>";
                foreach ($goodsClass as $key => $list){
                    $classifyName = ClassifyForm::getClassifyToId($key);
                    $html.="<legend>$classifyName</legend>";
                    $sum = 0;
                    $html.="<table class='table table-bordered table-striped'><thead><tr>
                            <th>".Yii::t("procurement","Goods Code")."</th>
                            <th>".Yii::t("procurement","Goods Name")."</th>
                            <th>".Yii::t("procurement","Type")."</th>
                            <th>".Yii::t("procurement","Unit")."</th>
                            <th>".Yii::t("procurement","Length×Width×Height（cm）")."</th>
                            <th>".Yii::t("procurement","Volume")."（m³）</th>
                            <th>".Yii::t("procurement","Multiple")."</th>
                            <th>".Yii::t("procurement","Confirm Number")."</th>
                            <th>".Yii::t("procurement","Total Volume")."（m³）</th>
                            </tr></thead><tbody>";
                    foreach ($list as $goods){
                        //$exSum+=intval($goods["confirm_num"])*floatval($goods["net_weight"]);
                        $volume = floatval($goods["len"])*floatval($goods["width"])*floatval($goods["height"])/1000000;
                        $multiple = empty($goods["rules_id"])?$goods["multiple"]:RulesForm::getRulesToId($goods["rules_id"])["multiple"];
                        $multiple = empty($multiple)?1:intval($multiple);
                        $total_multiple = $volume*(intval($goods["confirm_num"])/$multiple);
                        $sum+=$total_multiple;
                        $html.="<tr>
                        <td>".$goods["goods_code"]."</td>
                        <td>".$goods["name"]."</td>
                        <td>".$goods["type"]."</td>
                        <td>".$goods["unit"]."</td>
                        <td>".$goods["len"]."×".$goods["width"]."×".$goods["height"]."</td>
                        <td>".sprintf("%.2f",$volume)."</td>
                        <td>".$multiple."</td>
                        <td>".$goods["confirm_num"]."</td>
                        <td>".sprintf("%.2f",$total_multiple)."</td>
                        </tr>";
                    }
                    $html.="</tbody><tfoot><tr><td colspan='8'></td><td class='fa-2x'>".sprintf("%.2f",$sum)."</td></tr></tfoot></table>";
                }
                break;
        }
        return $html;
	}

	public function getGoodsToActivityId($activity_id){
        $arr=array();
        $rows = Yii::app()->db->createCommand()->select("b.city,c.goods_id as id,b.order_class as goods_class,c.goods_num,c.confirm_num")
            ->from("opr_order_goods c")
            ->leftJoin("opr_order b","b.id = c.order_id")
            ->where('b.activity_id = :activity_id AND b.status_type=1 AND b.judge=1 AND b.status != "pending" AND b.status != "cancelled"',
                array(':activity_id'=>$activity_id))
            ->order("b.city desc")
            ->queryAll();
        // AND c.goods_id = d.id
        if($rows){
            foreach ($rows as $row){
                $goods=OrderForm::getOneGoodsToId($row["id"],$row["goods_class"]);//$row["id"]
                if(empty($arr[$goods["classify_id"]][$row["id"]])){
                    $arr[$goods["classify_id"]][$row["id"]] = array(
                        "goods_code"=>$goods["goods_code"],
                        "price"=>$goods["price"],
                        "name"=>$goods["name"],
                        "type"=>$goods["type"],
                        "unit"=>$goods["unit"],
                        "multiple"=>$goods["multiple"],
                        "rules_id"=>$goods["rules_id"],
                        "gross_weight"=>empty($goods["gross_weight"])?"":$goods["gross_weight"],
                        "net_weight"=>empty($goods["net_weight"])?"":$goods["net_weight"],
                        "len"=>empty($goods["len"])?"":$goods["len"],
                        "width"=>empty($goods["width"])?"":$goods["width"],
                        "height"=>empty($goods["height"])?"":$goods["height"],
                    );
                }
                if(empty($arr[$goods["classify_id"]][$row["id"]]["goods_num"])){
                    $arr[$goods["classify_id"]][$row["id"]]["goods_num"] = 0;
                }
                if(empty($arr[$goods["classify_id"]][$row["id"]]["confirm_num"])){
                    $arr[$goods["classify_id"]][$row["id"]]["confirm_num"] = 0;
                }
                $arr[$goods["classify_id"]][$row["id"]]["goods_num"]+=intval($row["goods_num"]);
                $arr[$goods["classify_id"]][$row["id"]]["confirm_num"]+=intval($row["confirm_num"]);
            }
        }
        return $arr;
    }

	private function getGoodsToActivityIdOnlyPrice($activity_id){
	    $city = '';
        $arr=array();
        $rows = Yii::app()->db->createCommand()->select("b.city,c.goods_id as id,b.order_class as goods_class,c.goods_num,c.confirm_num")
            ->from("opr_order_goods c")
            ->leftJoin("opr_order b","b.id = c.order_id")
            ->where('b.activity_id = :activity_id AND b.status_type=1 AND b.judge=1 AND b.status != "pending" AND b.status != "cancelled"',
                array(':activity_id'=>$activity_id))
            ->order("b.city desc")
            ->queryAll();
        // AND c.goods_id = d.id
        if($rows){
            $price_type = 1;//单价1
            foreach ($rows as $row){
                if((empty($city)||$city!=$row["city"])&&$row["goods_class"]=="Import"){
                    $price_type = Yii::app()->db->createCommand()->select("price_type")->from("opr_city_price")
                        ->where("city=:city",array(":city"=>$row["city"]))->queryScalar();
                    $city = $row["city"];
                    $price_type = $price_type == 2?2:1;
                }

                $goods=OrderForm::getOneGoodsToId($row["id"],$row["goods_class"]);//$row["id"]
                if(empty($arr[$goods["classify_id"]][$row["id"]][$price_type])){
                    $goods_list = array(
                        "goods_code"=>$goods["goods_code"],
                        "price"=>$price_type == 2?$goods["price_two"]:$goods["price"],
                        "price_type"=>$price_type,
                        "name"=>$goods["name"],
                        "unit"=>$goods["unit"],
                        "goods_num"=>0,
                        "confirm_num"=>0,
                    );
                    if($row["goods_class"]=="Import"){
                        $goods_list["customs_code"] = $goods["customs_code"];
                        $goods_list["customs_name"] = $goods["customs_name"];
                        $goods_list["inspection"] = $goods["inspection"];
                    }
                    $arr[$goods["classify_id"]][$row["id"]][$price_type] = $goods_list;
                }
                $arr[$goods["classify_id"]][$row["id"]][$price_type]["goods_num"]+=intval($row["goods_num"]);
                $arr[$goods["classify_id"]][$row["id"]][$price_type]["confirm_num"]+=intval($row["confirm_num"]);
            }
        }
        return $arr;
    }

	public function getCityClassToActivityId($activity_id){
        $arr=array();
        $rows = Yii::app()->db->createCommand()->select("id,order_class,city")
            ->from("opr_order")
            ->where("activity_id = :activity_id AND status_type=1 AND judge=1 AND status != 'pending' AND status != 'cancelled'", array(':activity_id'=>$activity_id))->queryAll();
        if($rows){
            foreach ($rows as $row){
                $cityName = CGeneral::getCityName($row['city']);
                if(empty($arr[$row['city']])){
                    $arr[$row['city']]=array();
                    $goodList = array();
                }else{
                    $goodList = $arr[$row['city']]["goodList"];
                }
                $idList = Yii::app()->db->createCommand()->select("goods_id,goods_num,confirm_num,etd,batch_code")->from("opr_order_goods")->where("order_id=:order_id",array(":order_id"=>$row["id"]))->queryAll();
                foreach ($idList as $goods_id){
                    $goodId=$goods_id["goods_id"];
                    $goods=OrderForm::getOneGoodsToId($goodId,$row["order_class"],$row['city']);//$row["id"]
                    if(empty($goodList[$goodId])){
                        $goodList[$goodId] = $goods;
                        $goodList[$goodId]["goods_num"] = intval($goods_id["goods_num"]);
                        $goodList[$goodId]["confirm_num"] = intval($goods_id["confirm_num"]);
                    }else{
                        $goodList[$goodId]["goods_num"] += intval($goods_id["goods_num"]);
                        $goodList[$goodId]["confirm_num"] += intval($goods_id["confirm_num"]);
                    }
                    if(!empty($goods["rules_id"])){
                        $rules = RulesForm::getRulesToId($goods["rules_id"]);
                        $goodList[$goodId]["multiple"] = $rules["multiple"];
                    }
                    $goodList[$goodId]["order_city"] = $cityName;
                    $goodList[$goodId]["etd"] = empty($goods_id["etd"])?$this->getDefaultValueToCustoms($activity_id,$goodId,"etd"):$goods_id["etd"];
                    $goodList[$goodId]["batch_code"] = empty($goods_id["batch_code"])?$this->getDefaultValueToCustoms($activity_id,$goodId,"batch_code"):$goods_id["batch_code"];
                }
                $company = PurchaseView::getCompanyToCity($row['city']);
                $arr[$row['city']]["cityCode"]=$row['city'];
                $arr[$row['city']]["city"]=$company["city"];
                $arr[$row['city']]["cityName"]=$company["cityName"];
                $arr[$row['city']]["cityTel"]=$company["cityTel"];
                $arr[$row['city']]["cityAdr"]=$company["cityAdr"];
                $arr[$row['city']]["cityAdrTwo"]=$company["cityAdrTwo"];
                $arr[$row['city']]["company_postal"]=$company["postal2"];
                $arr[$row['city']]["cityUser"]=array("name"=>$company["userName"],"email"=>$company["email"]);
                $arr[$row['city']]["goodList"]=$goodList;
            }
        }
        return $arr;
    }

    private function getDefaultValueToCustoms($activity_id,$goods_id,$str){
        $row = Yii::app()->db->createCommand()->select("a.$str")->from("opr_order_goods a")
            ->leftJoin("opr_order c","a.order_id = c.id")
            ->where("c.activity_id=:activity_id and a.goods_id=:goods_id  AND a.$str is not null and a.$str != ''",
                array(":activity_id"=>$activity_id,":goods_id"=>$goods_id))->order("a.lud desc")->queryScalar();
        return $row?$row:"";
    }


    //根據城市獲取公司信息
    public function getCompanyToCity($city){
        $suffix = Yii::app()->params['envSuffix'];
        $from = "hr".$suffix.".hr_company";
        $suffix = "security".$suffix;
        $arr = array(
            "city"=>"",//地區名字
            "cityName"=>"",//公司名字
            "cityTel"=>"",//公司電話
            "cityAdr"=>"",//公司地址
            "cityAdrTwo"=>"",//收貨地址
            "postal2"=>"",//收貨姓名
            "userName"=>"",//負責人姓名
            "email"=>""//負責人郵箱
        );
        if (!empty($city)){
            $rs = Yii::app()->db->createCommand()->select("name,incharge")->from($suffix.".sec_city")->where("code=:code",array(":code"=>$city))->queryAll();
            if($rs){
                $arr["city"] = $rs[0]["name"];
            }
            $company = Yii::app()->db->createCommand()->select()->from($from)->where("city=:city",array(":city"=>$city))->order('tacitly desc')->queryAll();
            if($company){
                $arr["cityName"] = $company[0]["name"];
                $arr["cityTel"] = $company[0]["phone"];
                $arr["cityAdr"] = $company[0]["address"];
                $arr["cityAdrTwo"] = $company[0]["address2"];
                $arr["postal2"] = $company[0]["postal2"];
                $arr["userName"] = $company[0]["head"];
                $arr["email"] = $company[0]["head_email"];
/*                $incharge = $company[0]["head"];//負責人id
                $email = Yii::app()->db->createCommand()->select("email,disp_name")->from($suffix.".sec_user")->where("username=:username",array(":username"=>$incharge))->queryAll();
                if($email){
                    $arr["userName"] = $email[0]["disp_name"];
                    $arr["email"] = $email[0]["email"];
                }*/
            }
        }

        return $arr;
    }
}
