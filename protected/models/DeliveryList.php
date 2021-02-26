<?php

class DeliveryList extends CListPageModel
{
    public $searchTimeStart;//開始日期
    public $searchTimeEnd;//結束日期
    public $goods_name;//結束日期
    public $city;//查詢的城市
    public function attributeLabels()
    {
        return array(
            'order_code'=>Yii::t('procurement','Order Code'),
            'order_class'=>Yii::t('procurement','Order Class'),
            'activity'=>Yii::t('procurement','Activity Code'),
            'goods_list'=>Yii::t('procurement','Goods List'),
            'order_user'=>Yii::t('procurement','Order User'),
            'technician'=>Yii::t('procurement','Technician'),
            'activity_id'=>Yii::t('procurement','Order of Activity'),
            'status'=>Yii::t('procurement','Order Status'),
            'city'=>Yii::t('procurement','Order For City'),
            'lcd'=>Yii::t('procurement','Apply for time'),
            'lcu'=>Yii::t('procurement','Apply for user'),
            'goods_name'=>Yii::t('procurement','Goods Name'),
        );
    }

    public function rules(){
        return array(
            array('attr, pageNum, noOfItem, totalRow, searchField, searchValue, orderField, orderType, city, searchTimeStart, searchTimeEnd, filter, dateRangeValue','safe',),
        );
    }

    public function retrieveDataByPage($pageNum=1)
    {
        //order_user = '$userName' OR technician = '$userName'
        $suffix =  Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $userName = Yii::app()->user->name;
        $sql1 = "select a.*,b.disp_name,b.city AS s_city 
				from opr_order a
				LEFT JOIN security$suffix.sec_user b ON a.lcu=b.username 
				where (a.city in ($city_allow) AND a.judge=0 AND a.status != 'pending' AND a.status != 'cancelled') 
			";
        $sql2 = "select count(id)
				from opr_order a
				LEFT JOIN security$suffix.sec_user b ON a.lcu=b.username 
				where (a.city in ($city_allow) AND a.judge=0 AND a.status != 'pending' AND a.status != 'cancelled') 
			";
        $clause = "";
        if (!empty($this->searchField) && !empty($this->searchValue)) {
            $svalue = str_replace("'","\'",$this->searchValue);
            switch ($this->searchField) {
                case 'lcd':
                    $clause .= General::getSqlConditionClause('a.lcd', $svalue);
                case 'order_code':
                    $clause .= General::getSqlConditionClause('a.order_code', $svalue);
                    break;
                case 'lcu':
                    $clause .= General::getSqlConditionClause('b.disp_name', $svalue);
                    break;
                case 'goods_name':
                    $clause .= ' and a.id in '.$this->getOrderIdSqlLikeGoodsName($svalue);
                    break;
                case 'status':
                    $clause .= ' and a.status in '.$this->getStatusSqlToStr($svalue);
                    break;
            }
        }
        if (!empty($this->searchTimeStart) && !empty($this->searchTimeStart)) {
            $svalue = str_replace("'","\'",$this->searchTimeStart);
            $clause .= " and a.lcd >='$svalue 00:00:00' ";
        }
        if (!empty($this->searchTimeEnd) && !empty($this->searchTimeEnd)) {
            $svalue = str_replace("'","\'",$this->searchTimeEnd);
            $clause .= " and a.lcd <='$svalue 23:59:59' ";
        }
        if (!empty($this->city)) {
            $svalue = str_replace("'","\'",$this->city);
            $clause .= " and a.city ='$svalue' ";
        }
		if (empty($this->searchTimeStart) && empty($this->searchTimeEnd)) {
			$clause .= $this->getDateRangeCondition('a.lcd');
		} else {
			$this->dateRangeValue = '0';
		}

        $order = "";
        if (!empty($this->orderField)) {
            $order .= " order by ".$this->orderField." ";
            if ($this->orderType=='D') $order .= "desc ";
        } else
            $order = " order by a.lcd desc";

        $sql = $sql2.$clause;
        $this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();

        $sql = $sql1.$clause.$order;
        $sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
        $records = Yii::app()->db->createCommand($sql)->queryAll();

        $this->attr = array();
        if (count($records) > 0) {
            foreach ($records as $k=>$record) {
                $this->attr[] = array(
                    'id'=>$record['id'],
                    'order_code'=>$record['order_code'],
                    'goods_list'=>WarehouseForm::getGoodsListToId($record['id']),
                    'order_user'=>$record['order_user'],
                    'technician'=>$record['technician'],
                    'status'=>$record['status'],
                    'city'=>CGeneral::getCityName($record['city']),
                    'lcu'=>$record['disp_name'],
                    'lcd'=>date("Y-m-d",strtotime($record['lcd'])),
                );
            }
        }
        $session = Yii::app()->session;
        $session['delivery_ya01'] = $this->getCriteria();
        return true;
    }

    public function getCriteria() {
        return array(
            'searchField'=>$this->searchField,
            'searchValue'=>$this->searchValue,
            'orderField'=>$this->orderField,
            'orderType'=>$this->orderType,
            'noOfItem'=>$this->noOfItem,
            'pageNum'=>$this->pageNum,
            'filter'=>$this->filter,
            'city'=>$this->city,
            'searchTimeStart'=>$this->searchTimeStart,
            'searchTimeEnd'=>$this->searchTimeEnd,
        );
    }

//用戶名字（模糊查詢）
    public function getUserCodeSqlLikeName($name)
    {
        $from =  'security'.Yii::app()->params['envSuffix'].'.sec_user';
        $rows = Yii::app()->db->createCommand()->select("username")->from($from)->where(array('like', 'disp_name', "%$name%"))->queryAll();
        $arr = array();
        foreach ($rows as $row){
            array_push($arr,"'".$row["username"]."'");
        }
        if(empty($arr)){
            return "('')";
        }else{
            $arr = implode(",",$arr);
            return "($arr)";
        }
    }

//獲取城市列表
    public function getCityAllList()
    {
        $city_allow = Yii::app()->user->city_allow();
        $from =  'security'.Yii::app()->params['envSuffix'].'.sec_city';
        $rows = Yii::app()->db->createCommand()->select("code,name")->from($from)->where("code in ($city_allow)")->queryAll();
        $arr = array(""=>" -- ".Yii::t("user","City")." -- ");
        foreach ($rows as $row){
            $arr[$row["code"]] = $row["name"];
        }
        return $arr;
    }

//物品查詢（模糊查詢）
    public function getOrderIdSqlLikeGoodsName($name)
    {
        $rows = Yii::app()->db->createCommand()->select("a.order_id")->from("opr_order_goods a")->leftJoin("opr_warehouse b","a.goods_id=b.id")
            ->where(array('like', 'b.name', "%$name%"))->queryAll();
        $arr = array();
        foreach ($rows as $row){
            array_push($arr,"'".$row["order_id"]."'");
        }
        if(empty($arr)){
            return "('')";
        }else{
            $arr = implode(",",$arr);
            return "($arr)";
        }
    }

//訂單狀態（模糊查詢）
    public function getStatusSqlToStr($str)
    {
        $state=array(
            "sent"=>Yii::t("procurement","pending approval"),
            "read"=>Yii::t("procurement","Have read,Drop shipping"),
            "approve"=>Yii::t("procurement","Has been approved, Shipped out"),
            "reject"=>Yii::t("procurement","Reject"),
            "finished"=>Yii::t("procurement","finished"),
        );
        $arr = array();
        foreach ($state as $key =>$row){
            if (strpos($row,$str)!==false)
                array_push($arr,"'$key'");
        }
        if(empty($arr)){
            return "('')";
        }else{
            $arr = implode(",",$arr);
            return "($arr)";
        }
    }
}
