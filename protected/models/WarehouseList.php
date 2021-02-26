<?php

class WarehouseList extends CListPageModel
{
	public function attributeLabels()
	{
		return array(	
			'goods_code'=>Yii::t('procurement','Goods Code'),
			'name'=>Yii::t('procurement','Name'),
			'unit'=>Yii::t('procurement','Unit'),
			'inventory'=>Yii::t('procurement','Inventory'),
			'min_num'=>Yii::t('procurement','min inventory'),
			'price'=>Yii::t('procurement','Price（RMB）'),
			'cost_price'=>Yii::t('procurement','price history'),
            'classify_id'=>Yii::t('procurement','Classify'),
            'display'=>Yii::t('procurement','judge for visible'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$city = Yii::app()->user->city();
		$sql1 = "select *,ifnull(costPrice(id,now()),0) as cost_price 
				from opr_warehouse
				where city = '$city' 
			";
		$sql2 = "select count(id)
				from opr_warehouse
				where city = '$city' 
			";
		$clause = "";
		if($this->searchField == 'inventory'){
		    if(empty($this->searchValue)){
                $svalue = 0;
            }else{
                $svalue = str_replace("'","\'",$this->searchValue);
            }
            $clause .= "and inventory = '$svalue' ";
        }elseif (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'goods_code':
					$clause .= General::getSqlConditionClause('goods_code', $svalue);
					break;
				case 'name':
					$clause .= General::getSqlConditionClause('name', $svalue);
					break;
				case 'display':
                    $svalue = (strpos($svalue,Yii::t("misc","No"))!==false)?0:1;
					$clause .= General::getSqlConditionClause('display', $svalue);
					break;
				case 'type':
					$clause .= General::getSqlConditionClause('type', $svalue);
					break;
				case 'unit':
					$clause .= General::getSqlConditionClause('unit', $svalue);
					break;
				case 'price':
					$clause .= General::getSqlConditionClause('price', $svalue);
					break;
				case 'inventory':
					//$clause .= General::getSqlConditionClause('inventory', $svalue);
					$clause .= "and inventory = '$svalue' ";
					break;
				case 'classify_id':
					$clause .= $this->getClassifyToSql($svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
		    if("inventory" === $this->orderField){
                $order .= " order by CAST(inventory AS DECIMAL) ";
            }else{
                $order .= " order by ".$this->orderField." ";
            }
			if ($this->orderType=='D') $order .= "desc ";
		} else
			$order = " order by z_index desc, id desc";

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
						'name'=>$record['name'],
						'unit'=>$record['unit'],
						'min_num'=>$record['min_num'],
						'display'=>empty($record['display'])?Yii::t("misc","No"):Yii::t("misc","Yes"),
						'price'=>$record['cost_price'],
						'classify_id'=>ClassifyForm::getClassifyToId($record['classify_id']),
						'inventory'=>$record['inventory'],
						'goods_code'=>$record['goods_code'],
						'color'=>$record['z_index'] == 1?"":" text-danger",
						'goodsHistory'=>$this->getGoodsHistory($record['id']),
					);
			}
		}
		$session = Yii::app()->session;
		$session['warehouse_ya01'] = $this->getCriteria();
		return true;
	}

	//獲取物品由訂單扣減的歷史 (最多顯示5條)
	private function getGoodsHistory($goods_id){
	    if (empty($goods_id)){
	        return "";
        }
        $rows = Yii::app()->db->createCommand()->select("*")
            ->from("opr_order_goods")
            ->where('goods_id=:goods_id and order_status="finished"',array(':goods_id'=>$goods_id))
            ->order('lud desc')->limit(5)->queryAll();
	    if($rows){
	        return $rows;
        }else{
	        return "";
        }
    }

    //分類的模糊查詢
    private function getClassifyToSql($str){
        $rows = Yii::app()->db->createCommand()->select("id")
            ->from("opr_classify")
            ->where("class_type = 'Warehouse' and name like '%$str%'")->queryAll();
        if($rows){
            $arr = array();
            foreach ($rows as $row){
                array_push($arr,"'".$row["id"]."'");
            }
            $sqlStr = implode(",",$arr);
            return " and classify_id in ($sqlStr)";
        }else{
            return " and classify_id in ('')";
        }
    }
}
