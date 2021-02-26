<?php

class CargoCostUserList extends CListPageModel
{
    public $year;//年份
    public $month;//月份
    public $username;//月份
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
            'total_price'=>Yii::t('procurement','Cargo Cost'),
        );
    }

    public function rules(){
        return array(
            array('attr, pageNum, noOfItem, totalRow, searchField, searchValue, orderField, orderType, username, year, month','safe',),
        );
    }

    public function retrieveDataByPage($pageNum=1)
    {
        $this->year = (empty($this->year)||!is_numeric($this->year))?date("Y"):$this->year;
        //order_user = '$userName' OR technician = '$userName'
        $suffix =  Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $username = str_replace("'","\'",$this->username);
        $sql1 = "select a.id,a.order_code,a.order_user,a.technician,a.status,a.lcu,a.lcd,ifnull(SUM(costPrice(c.goods_id,a.lcd)*CONVERT(c.confirm_num,DECIMAL)),0) as total_price,b.disp_name,b.city 
				from opr_order_goods c
				LEFT JOIN opr_order a ON c.order_id = a.id
				LEFT JOIN security$suffix.sec_user b ON a.lcu=b.username 
				where (a.city in ($city_allow) AND a.judge=0 AND a.status IN ('approve','finished')) AND a.lcu = '$username'
			";
        $sql2 = "select a.id
				from opr_order a
				LEFT JOIN security$suffix.sec_user b ON a.lcu=b.username 
				where (a.city in ($city_allow) AND a.judge=0 AND a.status IN ('approve','finished')) AND a.lcu = '$username' 
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
        if (!empty($this->month)) {
            $svalue = str_replace("'","\'",$this->month);
            $svalue = is_numeric($svalue)?intval($svalue):1;
            $svalue = $svalue<10?"0$svalue":$svalue;
            $svalue = $this->year."/".$svalue;
            $clause .= " and date_format(a.lcd,'%Y/%m') ='$svalue' ";
        }else{
            $svalue = $this->year;
            $clause .= " and date_format(a.lcd,'%Y') ='$svalue' ";
        }

        $order = "";
        if (!empty($this->orderField)) {
            $order .= " order by ".$this->orderField." ";
            if ($this->orderType=='D') $order .= "desc ";
        } else
            $order = " order by a.lcd desc";

        $sql = "select count(*) from ($sql2 $clause GROUP BY a.id) ttt";
        $this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();

        $sql = $sql1.$clause." GROUP BY a.id,a.order_code,a.order_user,a.technician,a.status,a.lcu,a.lcd,b.disp_name,b.city".$order;
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
                    'total_price'=>$record['total_price'],
                    'status'=>$record['status'],
                    'city'=>CGeneral::getCityName($record['city']),
                    'lcu'=>$record['disp_name'],
                    'lcd'=>date("Y-m-d",strtotime($record['lcd'])),
                );
            }
        }
        $session = Yii::app()->session;
        $session['cargoCostUser_ya01'] = $this->getCriteria();
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
            'year'=>$this->year,
            'month'=>$this->month,
            'username'=>$this->username,
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

//
    public function getWebHeadTitle()
    {
        $title = "";
        $from =  'security'.Yii::app()->params['envSuffix'].'.sec_user';
        $row = Yii::app()->db->createCommand()->select("disp_name")->from($from)->where("username =:username",array(":username"=>$this->username))->queryRow();
        if($row){
            $title.= $row["disp_name"]."（".$this->year.Yii::t("monthly","Year");
            if (!empty($this->month)){
                $title.=$this->month.Yii::t("monthly","Month");
            }
            $title.="）";
        }
        return $title;
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

    //設置額外的session
    public function setProSession($username,$year,$month){
        $session = Yii::app()->session;
        $cargoList = array(
            "username"=>$username,
            "year"=>$year,
            "month"=>$month,
        );
        if (isset($session['cargoCostUser_pro']) && !empty($session['cargoCostUser_pro'])) {
            $cargoList = $session['cargoCostUser_pro'];
        }
        $criteria = array(
            "username"=>empty($username)?$cargoList["username"]:$username,
            "year"=>empty($year)?$cargoList["year"]:$year,
            "month"=>$month === 'null'?$cargoList["month"]:$month
        );
        $this->year = $criteria['year'];
        $this->username = $criteria['username'];
        $this->month = $criteria['month'];
        $session['cargoCostUser_pro'] = $criteria;
    }
}
