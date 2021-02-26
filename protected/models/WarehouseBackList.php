<?php

class WarehouseBackList extends CListPageModel
{
	public function attributeLabels()
	{
		return array(
            'order_code'=>Yii::t('procurement','Order Code'),
            'goods_code'=>Yii::t('procurement','Goods Code'),
            'name'=>Yii::t('procurement','Name'),
            'unit'=>Yii::t('procurement','Unit'),
            'disp_name'=>Yii::t('report','Order User Name'),
            'back_num'=>Yii::t('procurement','Black Number'),
            'back_user'=>Yii::t('procurement','Black User'),
            'lcd'=>Yii::t('procurement','Black Time'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
        $suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city();
		$sql1 = "select a.order_id,a.lcd,a.id,a.back_num,b.goods_code,b.name,b.unit,c.order_code,d.disp_name,a.lcu 
				from opr_warehouse_back a 
				LEFT JOIN opr_warehouse b ON a.warehouse_id=b.id 
				LEFT JOIN opr_order c ON a.order_id=c.id 
                LEFT JOIN security$suffix.sec_user d ON c.order_user = d.username
				where c.city = '$city' AND a.order_id>0 
			";
		$sql2 = "select count(a.id) 
				from opr_warehouse_back a 
				LEFT JOIN opr_warehouse b ON a.warehouse_id=b.id 
				LEFT JOIN opr_order c ON a.order_id=c.id 
                LEFT JOIN security$suffix.sec_user d ON c.order_user = d.username
				where c.city = '$city' AND a.order_id>0 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'goods_code':
					$clause .= General::getSqlConditionClause('b.goods_code', $svalue);
					break;
				case 'order_code':
					$clause .= General::getSqlConditionClause('c.order_code', $svalue);
					break;
				case 'name':
					$clause .= General::getSqlConditionClause('b.name', $svalue);
					break;
			}
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
                    'order_id'=>$record['order_id'],
                    'order_code'=>$record['order_code'],
                    'goods_code'=>$record['goods_code'],
                    'name'=>$record['name'],
                    'unit'=>$record['unit'],
                    'disp_name'=>$record['disp_name'],
                    'back_num'=>floatval($record['back_num']),
                    'lcu'=>$record["lcu"],
                    'lcd'=>$record["lcd"],
                );
			}
		}
		$session = Yii::app()->session;
		$session['warehouseBack_op01'] = $this->getCriteria();

		$this->resetWarehouseBackwardSql();//因退回是新增需求，需要用新錶重新記錄
		return true;
	}

	private function resetWarehouseBackwardSql(){ //因退回是新增需求，需要用新錶重新記錄
        $bool = Yii::app()->db->createCommand()->select("id")->from("opr_warehouse_back")->queryRow();
        if(!$bool){ //沒有初始庫存，需要記錄庫存
            set_time_limit(0);
            Yii::app()->db->createCommand()->insert("opr_warehouse_back",array(
                "order_id"=>0,
                "warehouse_id"=>0,
            ));

            $status_list =  Yii::app()->db->createCommand()->select("b.id,a.r_remark,a.lcu,a.lcd")->from("opr_order_status a")
                ->leftJoin("opr_order b","a.order_id = b.id")
                ->where("a.status='backward' and a.r_remark like '%退回數量:%' and b.judge = 0 and b.status = 'finished'")->order("a.order_id asc,a.lcd desc")->queryAll();
            if($status_list){
                foreach ($status_list as $status){
                    $add_num =0;
                    $backwardList = explode("退回數量:",$status["r_remark"]);
                    $name = current($backwardList);
                    $back_num = floatval(end($backwardList));
                    $goods = Yii::app()->db->createCommand()->select("a.goods_id,a.order_id,a.confirm_num")->from("opr_order_goods a")
                        ->leftJoin("opr_warehouse b","a.goods_id=b.id")
                        ->where("a.order_id=:id and b.name =:name",array(":id"=>$status["id"],":name"=>$name))->queryRow();
                    if($goods){ //如果存在退回的物品
                        $sumList = Yii::app()->db->createCommand()->select("r_remark")->from("opr_order_status")
                            ->where("order_id=:id and r_remark like '".$name."退回數量:%' and lcd>=:lcd",array(":id"=>$status["id"],":lcd"=>$status["lcd"]))->queryAll();
                        if($sumList){ //如果存在重複退回的物品
                            foreach ($sumList as $addList){
                                $add_num+=floatval(end(explode("退回數量:",$addList["r_remark"])));
                            }
                        }else{
                            $add_num = $back_num;
                        }
                        Yii::app()->db->createCommand()->insert("opr_warehouse_back",array(
                            "order_id"=>$goods["order_id"],
                            "warehouse_id"=>$goods["goods_id"],
                            "back_num"=>$back_num,
                            "old_num"=>floatval($goods["confirm_num"])+$add_num,
                            "lcu"=>$status['lcu'],
                            "lcd"=>$status['lcd'],
                        ));
                    }
                }
            }
        }

    }
}
