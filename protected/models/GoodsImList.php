<?php

class GoodsImList extends CListPageModel
{
	public function attributeLabels()
	{
		return array(	
			'goods_code'=>Yii::t('procurement','Goods Code'),
			'name'=>Yii::t('procurement','Name'),
			'type'=>Yii::t('procurement','Type'),
			'unit'=>Yii::t('procurement','Unit'),
			'price'=>Yii::t('procurement','Price（US$）'),
			'stickies_id'=>Yii::t('procurement','Stickies'),
			'net_weight'=>Yii::t('procurement','Net Weight（kg）'),
			'gross_weight'=>Yii::t('procurement','Gross Weight（kg）'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$city = Yii::app()->user->city();
		$sql1 = "select *
				from opr_goods_im
				where id>0 
			";
		$sql2 = "select count(id)
				from opr_goods_im
				where id>0 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'goods_code':
					$clause .= General::getSqlConditionClause('goods_code', $svalue);
					break;
				case 'name':
					$clause .= General::getSqlConditionClause('name', $svalue);
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
				case 'net_weight':
					$clause .= General::getSqlConditionClause('net_weight', $svalue);
					break;
				case 'gross_weight':
					$clause .= General::getSqlConditionClause('gross_weight', $svalue);
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
						'name'=>$record['name'],
						'type'=>$record['type'],
						'unit'=>$record['unit'],
						'price'=>$record['price'],
						'goods_code'=>$record['goods_code'],
						'net_weight'=>$record['net_weight'],
						'gross_weight'=>$record['gross_weight'],
					);
			}
		}
		$session = Yii::app()->session;
		$session['goodsim_ya01'] = $this->getCriteria();
		return true;
	}

}
