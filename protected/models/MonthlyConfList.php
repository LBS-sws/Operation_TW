<?php

class MonthlyConfList extends CListPageModel
{
	public $year_no;
	public $month_no;
	
	public function rules()	{
		$rtn1 = parent::rules();
		$rtn2 = array(
			array('year_no, month_no','safe'),
		);
		return array_merge($rtn1, $rtn2);
	}

	public function attributeLabels()
	{
		return array(	
			'city_name'=>Yii::t('misc','City'),
			'year_no'=>Yii::t('report','Year'),
			'month_no'=>Yii::t('report','Month'),
			'status'=>Yii::t('misc','Status'),
			'wfstatusdesc'=>Yii::t('workflow','Flow Status'),
		);
	}

	public function init() {
		$this->year_no = date("Y");
		$this->month_no = date("m") + 0;
		parent::init();
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$citylist = Yii::app()->user->city_allow();
//		$year = $this->year_no;
//		$month = $this->month_no;

		$sql1 = "select a.*, b.name as city_name, 
					(select case workflow$suffix.RequestStatus('OPRPT',a.id,a.lcd)
							when '' then '4DF' 
							when 'PH' then '1PH' 
							when 'PA' then '2PA' 
							when 'PS' then '0PS' 
							when 'ED' then '3ED' 
					end) as wfstatus,
					c.data_value as val_1,
					d.data_value as val_2,
					e.data_value as val_3,
					f.data_value as val_4,
					g.data_value as val_5,
					h.data_value as val_6,
					i.data_value as val_11,
					workflow$suffix.RequestStatusDesc('OPRPT',a.id,a.lcd) as wfstatusdesc
				from opr_monthly_hdr a inner join security$suffix.sec_city b on a.city=b.code 
					left outer join opr_monthly_dtl c on a.id=c.hdr_id and c.data_field='10001' 
					left outer join opr_monthly_dtl d on a.id=d.hdr_id and d.data_field='10002' 
					left outer join opr_monthly_dtl e on a.id=e.hdr_id and e.data_field='10003' 
					left outer join opr_monthly_dtl f on a.id=f.hdr_id and f.data_field='10004' 
					left outer join opr_monthly_dtl g on a.id=g.hdr_id and g.data_field='10005' 
					left outer join opr_monthly_dtl h on a.id=h.hdr_id and h.data_field='10008' 
					left outer join opr_monthly_dtl i on a.id=i.hdr_id and i.data_field='10011' 
				where a.city in ($citylist) and a.city=b.code and (a.year_no<>year(now()) or a.month_no<>month(now()))
			";
		$sql2 = "select count(a.id)
				from opr_monthly_hdr a, security$suffix.sec_city b
				where a.city in ($citylist) and a.city=b.code
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'city_name':
					$clause .= General::getSqlConditionClause('b.name', $svalue);
					break;
				case 'year_no':
					$clause .= General::getSqlConditionClause('a.year_no', $svalue);
					break;
				case 'month_no':
					$clause .= General::getSqlConditionClause('a.month_no', $svalue);
					break;
				case 'wfstatusdesc':
					$clause .= General::getSqlConditionClause("workflow$suffix.RequestStatusDesc('OPRPT',a.id,a.lcd)",$svalue);
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		} else
			$order = " order by a.year_no desc, a.month_no desc, a.city";

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
						'city'=>$record['city'],
						'city_name'=>$record['city_name'],
						'year_no'=>$record['year_no'],
						'month_no'=>$record['month_no'],
						'wfstatus'=>$record['wfstatus'],
						'wfstatusdesc'=>$record['wfstatusdesc'],
						'val_1'=>is_numeric($record['val_1']) ? number_format($record['val_1'],2,".","") : $record['val_1'],
						'val_2'=>is_numeric($record['val_2']) ? number_format($record['val_2'],2,".","") : $record['val_2'],
						'val_3'=>is_numeric($record['val_3']) ? number_format($record['val_3'],2,".","") : $record['val_3'],
						'val_4'=>is_numeric($record['val_4']) ? number_format($record['val_4'],2,".","") : $record['val_4'],
						'val_5'=>is_numeric($record['val_5']) ? number_format($record['val_5'],2,".","") : $record['val_5'],
						'val_6'=>is_numeric($record['val_6']) ? number_format($record['val_6'],2,".","") : $record['val_6'],
						'val_11'=>is_numeric($record['val_11']) ? number_format($record['val_11'],2,".","") : $record['val_11'],
					);
			}
		}
		$session = Yii::app()->session;
		$session['criteria_ya02'] = $this->getCriteria();
		return true;
	}

	public function getCriteria() {
		$rtn1 = parent::getCriteria();
		$rtn2 = array(
				'year_no'=>$this->year_no,
				'month_no'=>$this->month_no,
			);
		return array_merge($rtn1, $rtn2);
	}
}
