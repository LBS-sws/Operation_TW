<?php

class FastList extends CListPageModel
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
            'lcu'=>Yii::t('procurement','Apply for user'),
        );
    }

    public function retrieveDataByPage($pageNum=1)
    {
        //order_user = '$userName' OR technician = '$userName'
        $city = Yii::app()->user->city();
        $userName = Yii::app()->user->name;
        $sql1 = "select *
				from opr_order
				where (order_class = 'Fast' AND status_type=1 AND judge=1 AND status != 'pending' AND status != 'cancelled') 
			";
        $sql2 = "select count(id)
				from opr_order
				where (order_class = 'Fast' AND status_type=1 AND judge=1 AND status != 'pending' AND status != 'cancelled') 
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
                case 'city':
                    $citySql = OrderList::getCitySql($svalue);
                    $clause .= " and city in ($citySql)";
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
                    'goods_list'=>OrderForm::getGoodsListToId($record['id']),
                    'order_user'=>$record['order_user'],
                    'technician'=>$record['technician'],
                    'status'=>$record['status'],
                    'city'=>OrderList::getCityNameToCode($record['city']),
                    'lcu'=>$record['lcu'],
                    'lcd'=>date("Y-m-d",strtotime($record['lcd'])),
                );
            }
        }
        $session = Yii::app()->session;
        $session['fast_ya01'] = $this->getCriteria();
        return true;
    }

}
