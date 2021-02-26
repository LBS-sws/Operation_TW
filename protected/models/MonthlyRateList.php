<?php

class MonthlyRateList extends CListPageModel
{
	public function attributeLabels()
	{
		return array(
			'city_name'=>Yii::t('misc','City'),
			'rate'=>Yii::t('monthly','Rate'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$uid = Yii::app()->user->id;
		$sysid = Yii::app()->params['systemId'];
		$suffix = Yii::app()->params['envSuffix'];
		$suffix = $suffix=='dev' ? '_w' : $suffix;
		$sql1 = "select a.city, a.rate, b.name as city_name
				from opr_monthly_rate a
				left outer join security$suffix.sec_city b on a.city=b.code
				where 1=1
			";
		$sql2 = "select count(a.city)
				from opr_monthly_rate a
				left outer join security$suffix.sec_city b on a.city=b.code
				where 1=1
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'city_name':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			switch ($this->orderField) {
				case 'city_name':
					$order .= " order by b.name ";
					break;
				default: 
					$order .= " order by ".$this->orderField." ";
			}
			if ($this->orderType=='D') $order .= "desc ";
		} else {
			$order .= " order by a.city desc ";
		}

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();
		
		$list = array();
		$this->attr = array();
		if (count($records) > 0) {
			$obj = new MonthlyRateForm();
			foreach ($records as $k=>$record) {
				$city_name = $record['city']=='~ZZZZ' ? Yii::t('monthly','Default') : $record['city_name'];
				$rate = '';
				$items = json_decode($record['rate'], true);
				foreach ($items as $key=>$value) {
					if (!empty($rate)) $rate .= ', ';
					$rate .= Yii::t('monthly',$obj->rate_cat[$key]).': '.$value;
				}
				$this->attr[] = array(
					'city'=>$record['city'],
					'city_name'=>$city_name,
					'rate'=>$rate,
				);
			}
		}
		$session = Yii::app()->session;
		$session[$this->criteriaName()] = $this->getCriteria();
		return true;
	}

	public function criteriaName() {
		return Yii::app()->params['systemId'].'_criteria_ya04';
	}
}
