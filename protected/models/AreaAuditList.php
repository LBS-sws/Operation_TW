<?php

class AreaAuditList extends CListPageModel
{
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
        );
	}
    public function retrieveDataByPage($pageNum=1)
    {
        //order_user = '$userName' OR technician = '$userName'
        $city = Yii::app()->user->city();
        $userName = Yii::app()->user->name;
        $sql1 = "select *
				from opr_order
				where (city = '$city' AND judge=1 AND status != 'pending' AND status != 'cancelled' AND status != 'finished') 
			";
        $sql2 = "select count(id)
				from opr_order
				where (city = '$city' AND judge=1 AND status != 'pending' AND status != 'cancelled' AND status != 'finished') 
			";
        $clause = "";
        if (!empty($this->searchField) && !empty($this->searchValue)) {
            $svalue = str_replace("'","\'",$this->searchValue);
            switch ($this->searchField) {
                case 'lcd':
                    $clause .= General::getSqlConditionClause('lcd', $svalue);
                case 'order_code':
                    $clause .= General::getSqlConditionClause('order_code', $svalue);
                    break;
                case 'activity_id':
                    $activityIdSql = OrderList::getActivityIdSql($svalue);
                    $clause .= " and activity_id in ($activityIdSql)";
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
                $this->attr[] = array(
                    'id'=>$record['id'],
                    'order_code'=>$record['order_code'],
                    'order_class'=>Yii::t("procurement",$record['order_class']),
                    'activity_id'=>OrderList::getActivityTitleToId($record['activity_id']),
                    'goods_list'=>OrderForm::getGoodsListToId($record['id']),
                    'order_user'=>$record['order_user'],
                    'technician'=>$record['technician'],
                    'status'=>$this->getListStatus($record['status'],$record['status_type']),
                    'city'=>$record['city'],
                    'lcd'=>date("Y-m-d",strtotime($record['lcd'])),
                );
            }
        }
        $session = Yii::app()->session;
        $session['areaAudit_ya01'] = $this->getCriteria();
        return true;
    }


    public function getListStatus($status,$status_type){
        if($status_type == 1){
            switch ($status){
                case "sent":
                    return array(
                        "status"=>Yii::t("procurement","Waiting for central audit"),
                        "style"=>" text-yellow"
                    );//已提交，待審核
                case "read":
                    return array(
                        "status"=>Yii::t("procurement","Central checked"),
                        "style"=>" text-yellow"
                    );//中央已查看
                case "approve":
                    return array(
                        "status"=>Yii::t("procurement","Shipped out, Wait for receiving"),
                        "style"=>" text-yellow"
                    );//已发货，待收货
                case "reject":
                    return array(
                        "status"=>Yii::t("procurement","Central refused order"),
                        "style"=>" text-yellow"
                    );//中央拒绝订单
                case "expired":
                    return array(
                        "status"=>Yii::t("procurement","expired"),
                        "style"=>" text-gray"
                    );//中央拒绝订单
                default:
                    return array(
                        "status"=>Yii::t("procurement","Error Status"),
                        "style"=>" text-yellow"
                    );
            }
        }else{
            switch ($status){
                case "sent":
                    return array(
                        "status"=>Yii::t("procurement","pending approval"),
                        "style"=>" text-primary"
                    );//已发送，待审核
                case "reject":
                    return array(
                        "status"=>Yii::t("procurement","Area rejected"),
                        "style"=>" text-red"
                    );//地区已拒绝
                case "expired":
                    return array(
                        "status"=>Yii::t("procurement","expired"),
                        "style"=>" text-gray"
                    );//地区已拒绝
                default:
                    return array(
                        "status"=>Yii::t("procurement","Error Status"),
                        "style"=>" text-red"
                    );
            }
        }
    }
}
