<?php

class TechnicianList extends CListPageModel
{
    public $searchTimeStart;//開始日期
    public $searchTimeEnd;//結束日期
    public $goods_name;//結束日期
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
            'goods_name'=>Yii::t('procurement','Goods Name'),
        );
    }

    public function rules(){
        return array(
            array('attr, pageNum, noOfItem, totalRow, searchField, searchValue, orderField, orderType, searchTimeStart, searchTimeEnd','safe',),
        );
    }

    public function retrieveDataByPage($pageNum=1)
    {
        //order_user = '$userName' OR technician = '$userName'
        $city = Yii::app()->user->city();
        $userName = Yii::app()->user->name;
        $sql1 = "select *
				from opr_order
				where (city = '$city' AND judge=0 AND lcu='$userName') 
			";
        $sql2 = "select count(id)
				from opr_order
				where (city = '$city' AND judge=0 AND lcu='$userName') 
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
                case 'goods_name':
                    $clause .= ' and id in '.DeliveryList::getOrderIdSqlLikeGoodsName($svalue);
                    break;
            }
        }
        if (!empty($this->searchTimeStart) && !empty($this->searchTimeStart)) {
            $svalue = str_replace("'","\'",$this->searchTimeStart);
            $clause .= " and lcd >='$svalue 00:00:00' ";
        }
        if (!empty($this->searchTimeEnd) && !empty($this->searchTimeEnd)) {
            $svalue = str_replace("'","\'",$this->searchTimeEnd);
            $clause .= " and lcd <='$svalue 23:59:59' ";
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
                    //'activity_id'=>$this->getActivityTitleToId($record['activity_id']),
                    'goods_list'=>WarehouseForm::getGoodsListToId($record['id']),
                    'order_user'=>$record['order_user'],
                    'technician'=>$record['technician'],
                    'status'=>$record['status'],
                    'city'=>$record['city'],
                    'lcd'=>date("Y-m-d",strtotime($record['lcd'])),
                );
            }
        }
        $session = Yii::app()->session;
        $session['technician_ya01'] = $this->getCriteria();
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
            'searchTimeStart'=>$this->searchTimeStart,
            'searchTimeEnd'=>$this->searchTimeEnd,
        );
    }
}
