<?php

class ActivityList extends CListPageModel
{
	public function attributeLabels()
	{
		return array(	
			'activity_code'=>Yii::t('procurement','Activity Code'),
			'activity_title'=>Yii::t('procurement','Activity Title'),
			'order_class'=>Yii::t('procurement','Order Class'),
			'start_time'=>Yii::t('procurement','Start Time'),
			'end_time'=>Yii::t('procurement','End Time'),
            'num'=>Yii::t('procurement','Number Restrictions'),
		);
	}

	public function orderClassEnToInput($str){
	    switch (Yii::app()->language){
            case "zh_cn":
                if(strpos("货",$str)||strpos("货",$str)===0){
                    return "";
                }
                if(strpos("进口货",$str)||strpos("进口货",$str)===0){
                    return "Import";
                }
                if(strpos("国内货",$str)||strpos("国内货",$str)===0){
                    return "Domestic";
                }
                if(strpos("快速货",$str)||strpos("快速货",$str)===0){
                    return "Fast";
                }
                break;
            case "zh_tw":
                if(strpos("货",$str)){
                    return "";
                }
                if(strpos("進口貨",$str)||strpos("進口貨",$str)===0){
                    return "Import";
                }
                if(strpos("國內貨",$str)||strpos("國內貨",$str)===0){
                    return "Domestic";
                }
                if(strpos("快速貨",$str)||strpos("快速貨",$str)===0){
                    return "Fast";
                }
                break;
            case "en":
                return $str;
        }
        return "1111";
    }


	public function retrieveDataByPage($pageNum=1)
	{
		$city = Yii::app()->user->city();
        $sql1 = "select *
				from opr_order_activity
				where id>0 
			";
        $sql2 = "select count(id)
				from opr_order_activity
				where id>0 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'activity_code':
					$clause .= General::getSqlConditionClause('activity_code', $svalue);
					break;
				case 'activity_title':
					$clause .= General::getSqlConditionClause('activity_title', $svalue);
					break;
				case 'num':
					$clause .= General::getSqlConditionClause('num', $svalue);
					break;
				case 'order_class':
					$clause .= General::getSqlConditionClause('order_class', $this->orderClassEnToInput($svalue));
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
						'activity_code'=>$record['activity_code'],
						'activity_title'=>$record['activity_title'],
						'order_class'=>Yii::t("procurement",$record['order_class']),
						'num'=>$record['num'],
						'start_time'=>$record['start_time'],
						'end_time'=>$record['end_time'],
                        'activity_status'=>PurchaseList::compareDate($record['start_time'],$record['end_time'])
					);
			}
		}
		$session = Yii::app()->session;
		$session['activity_ya01'] = $this->getCriteria();
		return true;
	}

}
