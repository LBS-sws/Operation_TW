<?php
class RptPickingList extends CReport {
	protected function fields() {
		return array(
			'city_name'=>array('label'=>Yii::t('misc','City'),'width'=>15,'align'=>'L'),
			'order_code'=>array('label'=>Yii::t('procurement','Order Code'),'width'=>15,'align'=>'L'),
			'order_user'=>array('label'=>Yii::t('report','Order User ID'),'width'=>15,'align'=>'L'),
			'disp_name'=>array('label'=>Yii::t('report','Order User Name'),'width'=>30,'align'=>'L'),
			'goods_code'=>array('label'=>Yii::t('report','Item Code'),'width'=>25,'align'=>'L'),
			'goods_name'=>array('label'=>Yii::t('report','Item Name'),'width'=>30,'align'=>'L'),
			'unit'=>array('label'=>Yii::t('procurement','Unit'),'width'=>15,'align'=>'L'),
			'goods_class'=>array('label'=>Yii::t('report','Item Class'),'width'=>25,'align'=>'L'),
// Percy 2018/2/8 - 报表里面的货品成本价格设置成物品设置里的单价
//			'goods_cost'=>array('label'=>Yii::t('report','Item Cost'),'width'=>15,'align'=>'R'),
			'goods_price'=>array('label'=>Yii::t('procurement','Price'),'width'=>15,'align'=>'R'),
			'cost_year_month'=>array('label'=>Yii::t('procurement','Price year month'),'width'=>15,'align'=>'C'),
			'goods_num'=>array('label'=>Yii::t('report','Req. Qty.'),'width'=>15,'align'=>'R'),
			'confirm_num'=>array('label'=>Yii::t('report','Act. Qty.'),'width'=>15,'align'=>'R'),
			'goods_sum_price'=>array('label'=>Yii::t('report','Total Cost'),'width'=>15,'align'=>'R'),
			'lcd'=>array('label'=>Yii::t('report','Order Date'),'width'=>15,'align'=>'C'),
			'audit_time'=>array('label'=>Yii::t('report','Approved Date'),'width'=>15,'align'=>'C'),
            'note'=>array('label'=>Yii::t('procurement','Demand Note'),'width'=>50,'align'=>'L'),
            'remark'=>array('label'=>Yii::t('procurement','Headquarters Note'),'width'=>50,'align'=>'L'),
            'order_remark'=>array('label'=>Yii::t('procurement','Remark'),'width'=>50,'align'=>'L'),
		);
	}
	
	public function genReport() {
		$this->retrieveData();
		$this->title = $this->getReportName();
		$this->subtitle = Yii::t('report','Date').':'.$this->criteria['START_DT'].' - '.$this->criteria['END_DT'].' / '
			.Yii::t('report','Order Person').':'.$this->criteria['USER_NAMES']
			;
		return $this->exportExcel();
	}

	public function retrieveData() {
		$start_dt = $this->criteria['START_DT'];
		$end_dt = $this->criteria['END_DT'];
		$city = $this->criteria['CITY'];
		$user_ids = $this->criteria['USER_IDS'];
        $lcu = $this->criteria['UID'];
		$citymodel = new City();
		$citylist = $citymodel->getDescendantList($city);
		$citylist = empty($citylist) ? "'$city'" : "$citylist,'$city'";
		if (!empty($user_ids)) $user_ids = "'".str_replace("~","','",$user_ids)."'";
		
		$rows = PurchaseList::getOrderListSearch($citylist,$user_ids,$start_dt,$end_dt,$lcu);
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$temp = array();
				$temp['city_name'] = $row['city_name'];
				$temp['order_code'] = $row['order_code'];
				$temp['order_user'] = $row['order_user'];
				$temp['disp_name'] = $row['disp_name'];
				$temp['goods_code'] = $row['goods_code'];
				$temp['goods_name'] = $row['goods_name'];
				$temp['unit'] = $row['unit'];
				$temp['goods_class'] = $row['classify_name'];
				$temp['goods_price'] = $row['goods_price'];  //價格
				$temp['cost_year_month'] = $row['cost_year_month']; //價格對應的日期
				$temp['goods_num'] = number_format($row['goods_num'],4,'.','');
				$temp['confirm_num'] = number_format($row['confirm_num'],4,'.','');
// Percy 2018/2/8 - 报表里面的货品成本价格设置成物品设置里的单价
//				$temp['goods_sum_price'] = number_format($row['goods_sum_price'],2,'.','');
                $num = empty($row["confirm_num"])?$row["goods_num"]:$row["confirm_num"];
                $price = floatval($row["goods_price"]);
                $temp["goods_sum_price"] = sprintf("%.2f", floatval($num)*$price);
//
				$temp['lcd'] = General::toDate($row['lcd']);
				$temp['audit_time'] = General::toDate($row['audit_time']);
                $temp['note'] = $row['note'];
                $temp['remark'] = $row['remark'];
                $temp['order_remark'] = $row['order_remark'];
				$this->data[] = $temp;
			}
		}
		return true;
	}

	public function getReportName() {
		$city_name = isset($this->criteria) ? ' - '.General::getCityName($this->criteria['CITY']) : '';
		return (isset($this->criteria) ? Yii::t('report',$this->criteria['RPT_NAME']) : Yii::t('report','Nil')).$city_name;
	}
}
?>