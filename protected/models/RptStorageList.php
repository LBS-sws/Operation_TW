<?php
class RptStorageList extends CReport {
	protected function fields() {
		return array(
			'city_name'=>array('label'=>Yii::t('misc','City'),'width'=>15,'align'=>'L'),
			'storage_code'=>array('label'=>Yii::t('procurement','storage code'),'width'=>15,'align'=>'L'),
			'storage_time'=>array('label'=>Yii::t('procurement','storage time'),'width'=>15,'align'=>'L'),
			'storage_user'=>array('label'=>Yii::t('procurement','storage user'),'width'=>15,'align'=>'L'),
			'goods_code'=>array('label'=>Yii::t('report','Item Code'),'width'=>15,'align'=>'L'),
			'goods_name'=>array('label'=>Yii::t('report','Item Name'),'width'=>20,'align'=>'L'),
			'unit'=>array('label'=>Yii::t('procurement','Unit'),'width'=>10,'align'=>'L'),
			'goods_class'=>array('label'=>Yii::t('report','Item Class'),'width'=>15,'align'=>'L'),
			'supplier'=>array('label'=>Yii::t('procurement','supplier'),'width'=>15,'align'=>'L'),
			'old_storage_num'=>array('label'=>Yii::t('procurement','old storage num'),'width'=>15,'align'=>'C'),
			'storage_num'=>array('label'=>Yii::t('procurement','storage num'),'width'=>15,'align'=>'C'),
			'now_storage_num'=>array('label'=>Yii::t('procurement','now storage num'),'width'=>15,'align'=>'C'),
		);
	}
	
	public function genReport() {
		$this->retrieveData();
		$this->title = $this->getReportName();
		$this->subtitle = Yii::t('report','Date').':'.$this->criteria['START_DT'].' - '.$this->criteria['END_DT'];
		return $this->exportExcel();
	}

	public function retrieveData() {
        $suffix = Yii::app()->params['envSuffix'];
        $start_date = $this->criteria['START_DT'];
        $end_date = $this->criteria['END_DT'];
		$city = $this->criteria['CITY'];
        $citySql = empty($city)?"":" and b.city='$city' ";

        $sql="SELECT a.add_num as storage_num,f.name as supplier_name,a.warehouse_id,
b.city,b.code as storage_code,b.apply_time as storage_time,d.disp_name as storage_user,
c.goods_code,c.name AS goods_name,e.name AS classify_name,c.unit 
FROM opr_storage_info a 
LEFT JOIN opr_storage b ON b.id = a.storage_id 
LEFT JOIN security$suffix.sec_user d ON b.lcu = d.username
LEFT JOIN opr_warehouse c ON c.id = a.warehouse_id 
LEFT JOIN opr_classify e ON e.id = c.classify_id 
LEFT JOIN swoper$suffix.swo_supplier f ON a.supplier_id = f.id 
WHERE b.status_type = 1 $citySql";
        if(!empty($start_date)){
            $start_date = date("Y/m/d",strtotime($start_date));
            $sql.=" AND DATE_FORMAT(b.apply_time,'%Y/%m/%d') >= '$start_date'";
        }
        if(!empty($end_date)){
            $end_date = date("Y/m/d",strtotime($end_date));
            $sql.=" AND DATE_FORMAT(b.apply_time,'%Y/%m/%d') <= '$end_date'";
        }
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$temp = array();
				$temp['city_name'] = CGeneral::getCityName($row["city"]);
				$temp['storage_code'] = $row['storage_code'];
				$temp['storage_time'] = $row['storage_time'];
				$temp['storage_user'] = $row['storage_user'];
				$temp['goods_code'] = $row['goods_code'];
				$temp['goods_name'] = $row['goods_name'];
				$temp['unit'] = $row['unit'];
				$temp['goods_class'] = $row['classify_name'];
				$temp['supplier'] = $row['supplier_name'];

				$temp['old_storage_num'] = $this->getOldStorageSum($row);
				$temp['storage_num'] = $row['storage_num'];
				$temp['now_storage_num'] = floatval($temp['old_storage_num'])+floatval($temp['storage_num']);
				$this->data[] = $temp;
			}
		}
		return true;
	}

	private function getOldStorageSum($row){
        $sum = Yii::app()->db->createCommand()->select("sum(a.add_num)")
            ->from("opr_storage_info a")
            ->leftJoin("opr_storage b","a.storage_id = b.id")
            ->where("(b.apply_time<:time or a.storage_id = 0) and a.warehouse_id=:id",array(":time"=>$row["storage_time"],":id"=>$row["warehouse_id"]))->order("a.id asc")->queryScalar();
        return $sum?$sum:0;
	}

	public function getReportName() {
		$city_name = isset($this->criteria) ? ' - '.General::getCityName($this->criteria['CITY']) : '';
		return (isset($this->criteria) ? Yii::t('report',$this->criteria['RPT_NAME']) : Yii::t('report','Nil')).$city_name;
	}
}
?>