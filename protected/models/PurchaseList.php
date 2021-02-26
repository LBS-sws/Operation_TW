<?php

class PurchaseList extends CListPageModel
{
	public function attributeLabels()
	{
		return array(
			'activity_code'=>Yii::t('procurement','Activity Code'),
			'activity_title'=>Yii::t('procurement','Activity Title'),
			'start_time'=>Yii::t('procurement','Start Time'),
			'end_time'=>Yii::t('procurement','End Time'),
            'activity_status'=>Yii::t('procurement','Activity Status'),
/*			'order_sum'=>Yii::t('procurement','Order Sum'),
			'order_type_sum'=>Yii::t('procurement','Order Type Sum'),*/
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
		$city = Yii::app()->user->city();
		$sql1 = "select *
				from opr_order_activity
				where id>0 
			";
		$sql2 = "select count(id)
				from opr_order_activity
				where id>0 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'activity_code':
					$clause .= General::getSqlConditionClause('activity_code', $svalue);
					break;
				case 'activity_title':
					$clause .= General::getSqlConditionClause('activity_title', $svalue);
					break;
			}
		}

		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		} else
			$order = " order by id desc";

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();

		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();

		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
			    $OrderSumArr = $this->getOrderSumToId($record['id']);
                $this->attr[] = array(
                    'id'=>$record['id'],
                    'activity_code'=>$record['activity_code'],
                    'activity_title'=>$record['activity_title'],
                    'start_time'=>$record['start_time'],
                    'end_time'=>$record['end_time'],
                    'order_sum'=>$OrderSumArr["sum"],
                    'sentSum'=>$OrderSumArr["sentSum"],
                    'order_type_sum'=>$OrderSumArr["list"],
                    'activity_status'=>$this->compareDate($record['start_time'],$record['end_time'])
                );
			}
		}
		$session = Yii::app()->session;
		$session['purchase_ya01'] = $this->getCriteria();
		return true;
	}

	//對比時間有沒有到期
	public function compareDate($startDate,$endDate){
        $nowDate = date("Y-m-d");
        if (strtotime($nowDate)>strtotime($endDate)){
            return "End";
        }elseif(strtotime($nowDate)<strtotime($startDate)){
            return "Wait";
        }else{
            return "Run";
        }
    }

	//對比時間有沒有到期
	public function getOrderSumToId($activity_id){
	    $arr=array();
        $sentRows = Yii::app()->db->createCommand()->select("id")->from("opr_order")
            ->where('activity_id=:activity_id AND status_type=1 AND judge=1 AND status = "sent"', array(':activity_id'=>$activity_id))->queryAll();
        $rows = Yii::app()->db->createCommand()->select("order_class")->from("opr_order")
            ->where('activity_id=:activity_id AND status_type=1 AND judge=1 AND status != "pending" AND status != "cancelled"', array(':activity_id'=>$activity_id))->queryAll();
        if($rows){
            foreach ($rows as $row){
                if(empty($arr[$row["order_class"]])){
                    $arr[$row["order_class"]] = 0;
                }
                $arr[$row["order_class"]]++;
            }
        }
        return array(
            "sum"=>count($rows),
            "sentSum"=>count($sentRows),
            "list"=>$arr
        );
    }

    /**
     * 獲取外勤領料列表（一個物品一行）
     * @param string $city_allow  訂單城市的範圍  例如：'SH','SZ','BJ'
     * @param string $user_code   用戶編號
     * @param string $start_date  訂單開始日期
     * @param string $end_date    訂單結束日期
     * @param string $username    管理員的username
     * @return array
     */
    public function getOrderListSearch($city_allow,$user_code='',$start_date='',$end_date='',$username=''){
        $suffix = Yii::app()->params['envSuffix'];
        $systemId = Yii::app()->params['systemId'];
        if(empty($city_allow)){
            $city_allow = Yii::app()->user->city_allow();
        }
        $connection = Yii::app()->db;
        $sql="SELECT a.remark AS order_remark,a.order_user,d.disp_name,a.status,a.order_code,a.city,a.lcd,a.audit_time,
b.order_id,b.goods_id,b.goods_num,b.confirm_num,b.note,b.remark,
c.goods_code,c.name AS goods_name,c.costing AS goods_cost,e.name AS classify_name,c.unit,c.price AS goods_price
FROM opr_order a 
LEFT JOIN opr_order_goods b ON a.id = b.order_id 
LEFT JOIN security$suffix.sec_user d ON a.order_user = d.username
LEFT JOIN opr_warehouse c ON c.id = b.goods_id 
LEFT JOIN opr_classify e ON e.id = c.classify_id 
WHERE a.judge = 0 AND (a.status = 'finished' OR a.status = 'approve') AND a.city IN ($city_allow) AND c.goods_code IS NOT NULL ";
        if(!empty($user_code)){
//            $sql.=" d.username like '%$user_code%'";
            $sql.=" AND d.username in ($user_code)";
        }
        if(!empty($start_date)){
            $start_date = date("Y/m/d",strtotime($start_date));
            $sql.=" AND DATE_FORMAT(a.audit_time,'%Y/%m/%d') >= '$start_date'";
        }
        if(!empty($end_date)){
            $end_date = date("Y/m/d",strtotime($end_date));
            $sql.=" AND DATE_FORMAT(a.audit_time,'%Y/%m/%d') <= '$end_date'";
        }
        $sql.=" order by a.audit_time desc";

        $records = $connection->createCommand($sql)->queryAll();
        if($records){
            $priceBool = Yii::app()->db->createCommand()->select("username")->from("security$suffix.sec_user_access")
                ->where("username=:username and system_id='$systemId' and (a_read_only like '%YN02%' or a_read_write like '%YN02%' or a_control like '%YN02%')",
                    array(':username'=>$username))->queryRow(); //判斷是否有顯示價格的權限
            foreach ($records as &$record){
/*                $order_id=$record["order_id"];
                $audit_time = $connection->createCommand("SELECT lcd FROM opr_order_status WHERE order_id=$order_id AND status='approve' order by id desc")->queryRow();
                if($audit_time){
                    $record["audit_time"] = $audit_time["lcd"];
                }else{
                    $record["audit_time"] = "";
                }*/
                $record["cost_year_month"] = "无";
                $record["goods_price"] = 0;
                if($priceBool){
                    $priceList = Yii::app()->db->createCommand()->select("price as cost_price,year,month")->from("opr_warehouse_price")
                        ->where("(year<date_format(:date_time,'%Y') or (year=date_format(:date_time,'%Y') and month<=date_format(:date_time,'%m'))) AND warehouse_id = :id",
                            array(':id'=>$record['goods_id'],':date_time'=>$record['audit_time']))->order("year DESC,month DESC")->queryRow();
                    if(!$priceList){
                        $priceList=array('cost_price'=>0,'year'=>'无','month'=>'无');
                    }
                    $record["cost_year_month"] = $priceList["year"]==="无"?"无":$priceList["year"]."/".$priceList["month"];
                    $record["goods_price"] = $priceList["cost_price"];
                }
                $num = empty($record["confirm_num"])?$record["goods_num"]:$record["confirm_num"];
                $price = floatval($record["goods_price"]);
                $record["goods_sum_price"] = sprintf("%.2f", floatval($num)*$price);
                $record["city_name"] = CGeneral::getCityName($record["city"]);
            }
            return $records;
        }else{
            return array();
        }
    }

	// Percy - 為 RptPickingList.php 讀取資料用
	//
    public function getOrderListSearchX($city_allow,$user_code='',$start_date='',$end_date=''){
        $suffix = Yii::app()->params['envSuffix'];
        if(empty($city_allow)){
            $city_allow = Yii::app()->user->city_allow();
        }
        $connection = Yii::app()->db;
        $sql="SELECT a.remark AS order_remark,a.order_user,d.disp_name,a.status,a.order_code,a.city,a.lcd,a.audit_time,
b.order_id,b.goods_id,b.goods_num,b.confirm_num,b.note,b.remark,
c.goods_code,c.name AS goods_name,c.costing AS goods_cost,e.name AS classify_name,c.unit,c.price AS goods_price
FROM opr_order a 
LEFT JOIN opr_order_goods b ON a.id = b.order_id 
LEFT JOIN security$suffix.sec_user d ON a.order_user = d.username
LEFT JOIN opr_warehouse c ON c.id = b.goods_id 
LEFT JOIN opr_classify e ON e.id = c.classify_id 
WHERE a.judge = 0 AND (a.status = 'finished' OR a.status = 'approve') AND a.city IN ($city_allow) AND c.goods_code IS NOT NULL ";
        if(!empty($user_code)){
//            $sql.=" d.username like '%$user_code%'";
            $sql.=" AND d.username in ($user_code)";
        }
        if(!empty($start_date)){
            $sql.=" AND a.lcd >= '$start_date 00:00:00'";
        }
        if(!empty($end_date)){
            $sql.=" AND a.lcd <= '$end_date 23:59:59'";
        }
        $sql.=" order by a.lcd desc";
        $records = $connection->createCommand($sql)->queryAll();
        if($records){
            foreach ($records as &$record){
                $order_id=$record["order_id"];
                $audit_time = $connection->createCommand("SELECT lcd FROM opr_order_status WHERE order_id=$order_id AND status='approve' order by id desc")->queryRow();
                if($audit_time){
                    $record["audit_time"] = $audit_time["lcd"];
                }else{
                    $record["audit_time"] = "";
                }
                $num = empty($record["confirm_num"])?$record["goods_num"]:$record["confirm_num"];
                $price = floatval($record["goods_price"]);
                $record["goods_sum_price"] = sprintf("%.2f", floatval($num)*$price);
                $record["city_name"] = CGeneral::getCityName($record["city"]);
            }
            return $records;
        }else{
            return array();
        }
    }
	
    //獲取中央訂單列表（一個物品一行）
    public function getOrderHeadListSearch($user_code='',$start_date='',$end_date=''){
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $connection = Yii::app()->db;
        $sql="SELECT b.order_user, c.disp_name, b.status, b.city, b.order_class,b.lcd,a.goods_id,a.order_id,a.goods_num,a.confirm_num FROM opr_order_goods a LEFT JOIN opr_order b ON a.order_id = b.id
 LEFT JOIN security$suffix.sec_user c ON b.order_user = c.username WHERE b.judge = 1 AND b.status != 'pending' AND b.order_class !='' AND  b.city in ($city_allow) ";
        if(!empty($user_code)){
            $sql.=" c.username like '%$user_code%'";
        }
        if(!empty($start_date)){
            $sql.=" b.lcu >= '$start_date'";
        }
        if(!empty($end_date)){
            $sql.=" b.lcu <= '$end_date'";
        }
        $sql.=" order by b.lcd desc";
        $records = $connection->createCommand($sql)->queryAll();
        if($records){
            foreach ($records as &$record){
                $goods_id=$record["goods_id"];
                $order_id=$record["order_id"];
                switch ($record["order_class"]){
                    case "Import":
                        $sqlName="opr_goods_im";
                        break;
                    case "Domestic":
                        $sqlName="opr_goods_do";
                        break;
                    case "Fast":
                        $sqlName="opr_goods_fa";
                        break;
                    default:
                        unset($record);
                        continue;
                }
                $goods_sql = "SELECT b.name AS classify_name,a.* FROM $sqlName a LEFT JOIN opr_classify b ON a.classify_id = b.id WHERE a.id=$goods_id";
                $goods = $connection->createCommand($goods_sql)->queryRow();
                $price_type = 1;
                if($goods){
                    if($record["order_class"]=="Import"){
                        $price_type = Yii::app()->db->createCommand()->select("price_type")->from("opr_city_price")
                            ->where("city=:city",array(":city"=>$record["city"]))->queryScalar();
                        $price_type = $price_type == 2?2:1;
                    }
                    $goods["price"] = $price_type==2?$goods["price_two"]:$goods["price"];
                    $record["goods_code"] = $goods["goods_code"];
                    $record["goods_name"] = $goods["name"];
                    $record["goods_class"] = $goods["classify_name"];
                    $record["goods_price"] = $goods["price"];
                    $num = empty($record["confirm_num"])?$record["goods_num"]:$record["confirm_num"];
                    $price = floatval($goods["price"]);
                    $record["goods_sum_price"] = sprintf("%.2f", floatval($num)*$price);
                    $audit_time = $connection->createCommand("SELECT lcd FROM opr_order_status WHERE order_id=$order_id AND status='head approve'")->queryRow();
                    if($audit_time){
                        $record["audit_time"] = $audit_time["lcd"];
                        $date1 = strtotime($record["lcd"]);
                        $date2 = strtotime($audit_time["lcd"]);
                        $time_difference = $date2 - $date1;
                        $seconds_per_day = 60*60*24;
                        $day = round($time_difference / $seconds_per_day);
                        $record["audit_log"] = $day."天";
                    }else{
                        $record["audit_time"] = "";
                        $record["audit_log"] = "";
                    }
                }else{
                    unset($record);
                    continue;
                }
                $record["city_name"] = CGeneral::getCityName($record["city"]);
            }
            return $records;
        }else{
            return array();
        }
    }
}
