<?php

class StickiesList extends CListPageModel
{
	public function attributeLabels()
	{
		return array(
			'name'=>Yii::t('procurement','Name'),
			'content'=>Yii::t('procurement','Content'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$city = Yii::app()->user->city();
		$sql1 = "select *
				from opr_stickies
				where id >= 0 
			";
		$sql2 = "select count(id)
				from opr_stickies
				where id >= 0 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'content':
					$clause .= General::getSqlConditionClause('content', $svalue);
					break;
				case 'name':
					$clause .= General::getSqlConditionClause('name', $svalue);
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
						'content'=>$record['content'],
					);
			}
		}
		$session = Yii::app()->session;
		$session['stickies_ya01'] = $this->getCriteria();
		return true;
	}

}
