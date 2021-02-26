<?php
class RptBackward extends CReport {
	protected function fields() {
		return array(
			'city_name'=>array('label'=>Yii::t('misc','City'),'width'=>15,'align'=>'L'),
			'order_code'=>array('label'=>Yii::t('procurement','Order Code'),'width'=>15,'align'=>'L'),
			'disp_name'=>array('label'=>Yii::t('report','Order User Name'),'width'=>30,'align'=>'L'),
			'goods_code'=>array('label'=>Yii::t('report','Item Code'),'width'=>25,'align'=>'L'),
			'goods_name'=>array('label'=>Yii::t('report','Item Name'),'width'=>30,'align'=>'L'),
			'unit'=>array('label'=>Yii::t('procurement','Unit'),'width'=>15,'align'=>'L'),
			'goods_class'=>array('label'=>Yii::t('report','Item Class'),'width'=>25,'align'=>'L'),
			'back_num'=>array('label'=>Yii::t('procurement','Black Number'),'width'=>15,'align'=>'R'),
			'back_user'=>array('label'=>Yii::t('procurement','Black User'),'width'=>15,'align'=>'R'),
			'lcd'=>array('label'=>Yii::t('procurement','Black Time'),'width'=>15,'align'=>'C'),
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
		
		$rows = PurchaseList::getOrderListSearchToBackward($citylist,$user_ids,$start_dt,$end_dt,$lcu);
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
//
                $temp['back_num'] = floatval($row['back_num']);
				$temp['lcd'] = $row['lcd'];
				$temp['back_user'] = $row['back_user'];
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