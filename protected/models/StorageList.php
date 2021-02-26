<?php

class StorageList extends CListPageModel
{
	public function attributeLabels()
	{
		return array(
			'code'=>Yii::t('procurement','storage code'),
            'apply_time'=>Yii::t('procurement','storage time'),
            'status_type'=>Yii::t('procurement','storage type'),
            'storage_name'=>Yii::t('procurement','storage goods'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$city = Yii::app()->user->city();
		$sql1 = "select *
				from opr_storage
				where city = '$city' 
			";
		$sql2 = "select count(id)
				from opr_storage
				where city = '$city' 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'code':
					$clause .= General::getSqlConditionClause('code', $svalue);
					break;
				case 'apply_time':
					$clause .= General::getSqlConditionClause('apply_time', $svalue);
					break;
				case 'storage_name':
					$clause .= General::getSqlConditionClause('storage_name', $svalue);
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
			    $list = explode(",",$record['storage_name']);
			    if(count($list)>3){
			        $list = array_slice($list,0,3);
                    $list[]=".....";
                }
                $this->attr[] = array(
                    'id'=>$record['id'],
                    'code'=>$record['code'],
                    'apply_time'=>$record['apply_time'],
                    'storage_name'=>implode("<br>",$list),
                    'status_type'=>$record['status_type']==1?Yii::t("procurement","put in storage"):Yii::t("procurement","Draft"),
                    'color'=>$record['status_type']==1?"text-primary":"",
                );
			}
		}
		$session = Yii::app()->session;
		$session['storage_op01'] = $this->getCriteria();

		$this->resetStorageSql();//因库存不可修改，记录老旧的库存
		return true;
	}

	private function resetStorageSql(){ //記錄倉庫的初始庫存
        $bool = Yii::app()->db->createCommand()->select("id")->from("opr_storage_info")->where('storage_id=0 or id >0')->queryRow();
        if(!$bool){ //沒有初始庫存，需要記錄庫存
            set_time_limit(0);
            Yii::app()->db->createCommand()->insert("opr_storage_info",array(
                "storage_id"=>0,
                "warehouse_id"=>0,
                "add_num"=>0,
            ));

            $goods_list = Yii::app()->db->createCommand()->select("id,inventory")->from("opr_warehouse")->queryAll();
            if($goods_list){
                foreach ($goods_list as $goods){
                    Yii::app()->db->createCommand()->insert("opr_storage_info",array(
                        "storage_id"=>0,
                        "warehouse_id"=>$goods["id"],
                        "add_num"=>floatval($goods["inventory"]),
                    ));
                }
            }
        }

    }
}
