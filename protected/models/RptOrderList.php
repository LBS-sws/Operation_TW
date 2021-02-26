<?php
class RptOrderList extends CReport {	protected function fields() {		return array(			'city_name'=>array('label'=>Yii::t('misc','City'),'width'=>15,'align'=>'L'),
			'goods_code'=>array('label'=>Yii::t('procurement','Order Code'),'width'=>22,'align'=>'L'),
			'goods_name'=>array('label'=>Yii::t('procurement','Goods Name'),'width'=>30,'align'=>'L'),
			'class_name'=>array('label'=>Yii::t('procurement','Goods Class'),'width'=>25,'align'=>'L'),
			'goods_num'=>array('label'=>Yii::t('procurement','Goods Number'),'width'=>15,'align'=>'R'),
			'confirm_num'=>array('label'=>Yii::t('procurement','Confirm Number'),'width'=>15,'align'=>'R'),
			'status'=>array('label'=>Yii::t('procurement','Order Status'),'width'=>20,'align'=>'L'),
			'lcd'=>array('label'=>Yii::t('procurement','Apply for time'),'width'=>15,'align'=>'C'),
			'appr_dt'=>array('label'=>Yii::t('report','HQ Approved Date'),'width'=>15,'align'=>'C'),
			'activity_code'=>array('label'=>Yii::t('report','Activity Info'),'width'=>40,'align'=>'L'),
			'req_user'=>array('label'=>Yii::t('report','Requestor'),'width'=>30,'align'=>'L'),
		);	}	
	public function genReport() {
		$this->retrieveData();
		$stslist = ReportY02Form::OrderStatusList();
		$ordsts = $this->criteria['ORDERSTATUS'];
		$status = isset($stslist[$ordsts]) ? $stslist[$ordsts] : '';
		$this->title = $this->getReportName();
		$this->subtitle = Yii::t('report','Date').':'.$this->criteria['START_DT'].' - '.$this->criteria['END_DT'].' / '
			.Yii::t('procurement','Order Status').':'.$status.' / '
			.Yii::t('report','Goods').':'.$this->criteria['GOODSDESC']
			;
		return $this->exportExcel();
	}

	public function retrieveData() {
		$start_dt = $this->criteria['START_DT'];
		$end_dt = $this->criteria['END_DT'];
		$city = $this->criteria['CITY'];
		$goods_id = $this->criteria['GOODS'];
		$order_status = $this->criteria['ORDERSTATUS'];
		
		$citymodel = new City();
		$citylist = $citymodel->getDescendantList($city);
		$citylist = empty($citylist) ? "'$city'" : "$citylist,'$city'";
		
		$suffix = Yii::app()->params['envSuffix'];
		
		$cond_goods = '';
		if (!empty($goods_id)) {
			$ids = explode('~',$goods_id);
			foreach ($ids as $id) {
				$item = explode(':',$id);
				$cond_goods .= ($cond_goods=='' ? '' : ' or ')."(b.order_class='".$item[0]."' and a.goods_id='".$item[1]."')";
			}
			if ($cond_goods!='') $cond_goods = ' and ('.$cond_goods.')';
		}

		$cond_status = '';
		if ($order_status!='all') {
			$item = explode(':',$order_status);
			$cond_status = " and (b.status_type=".$item[0]." and b.status='".$item[1]."')";
		}
		
		$sql = "select a.goods_id, a.goods_num, a.confirm_num, c.activity_code, c.activity_title, 
					b.lcd, b.id, b.order_code, b.order_user, b.order_class, b.status_type, b.status, b.city,
					d.disp_name as req_user, e.name as city_name 
				from opr_order_goods a inner join opr_order b on a.order_id=b.id
					inner join opr_order_activity c on b.activity_id=c.id
					inner join security$suffix.sec_user d on d.username=b.order_user 
					inner join security$suffix.sec_city e on e.code=b.city 
				where b.city in($citylist) and b.order_class in ('Fast','Import','Domestic')
					and b.lcd >= '$start_dt' and b.lcd <= '$end_dt'
					$cond_goods $cond_status 
				order by b.lcd desc, a.id
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$goods_info = $this->getGoodsInfo($row['order_class'],$row['goods_id']);
				$appr_info = $this->getHQApproveInfo($row['id'], $row['status_type'], $row['status']);
				
				$temp = array();
				$temp['city_name'] = $row['city_name'];
				$temp['goods_code'] = $goods_info['code'];
				$temp['goods_name'] = $goods_info['name'];
				$temp['class_name'] = $goods_info['classify_name'];
				$temp['goods_num'] = number_format($row['goods_num'],2,'.','');
				$temp['confirm_num'] = number_format($row['confirm_num'],2,'.','');
				$temp['status'] = OrderList::printOrderStatus($row['status'], $row['status_type']);
				$temp['lcd'] = General::toDate($row['lcd']);
				$temp['appr_dt'] = General::toDate($appr_info['appr_dt']);
				$temp['activity_code'] = '['.$row['activity_code'].'] '.$row['activity_title'];
				$temp['req_user'] = $row['req_user'];
				$this->data[] = $temp;
			}
		}
		return true;	}

	protected function getHQApproveInfo($id, $type, $status) {
		$rtn = array('appr_dt'=>'', 'appr_user'=>'');
		if (($type==1 && $status=='approve') || $status=='finished') {
			$sql = "select a.time, a.lcu
					from opr_order_status a left outer join opr_order_status b
						on a.order_id=b.order_id and a.status=b.status
						and b.time > a.time
					where a.order_id=$id and a.status='approve' and b.time is null
				";
			$row = Yii::app()->db->createCommand($sql)->queryRow();
			if ($row!==false) {
				$rtn['appr_dt'] = $row['time'];
				$rtn['appr_user'] = $row['lcu'];
			}
		}
		return $rtn;
	}
	
	protected function getGoodsInfo($type, $id) {
		switch($type) {
			case 'Fast':
				$tbname = 'opr_goods_fa';
				break;
			case 'Import':
				$tbname = 'opr_goods_im';
				break;
			case 'Domestic':
				$tbname = 'opr_goods_do';
				break;
		}
		$sql = "select a.goods_code, a.name, b.name as classify_name 
				from $tbname a left outer join opr_classify b on a.classify_id=b.id
				where a.id=$id
			";
		$row = Yii::app()->db->createCommand($sql)->queryRow();
		return array(
				'code'=>($row===false ? '' : $row['goods_code']),
				'name'=>($row===false ? '' : $row['name']),
				'classify_name'=>($row===false ? '' : $row['classify_name']),
			);
	}
	
	public function getReportName() {
		$city_name = isset($this->criteria) ? ' - '.General::getCityName($this->criteria['CITY']) : '';
		return (isset($this->criteria) ? Yii::t('report',$this->criteria['RPT_NAME']) : Yii::t('report','Nil')).$city_name;
	}
}
?>