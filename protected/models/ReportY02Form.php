<?php
/* Reimbursement Form */

class ReportY02Form extends CReportForm
{
	public $goods;
	public $goods_desc;
	public $order_status;
	
	protected function labelsEx() {
		return array(
				'goods'=>Yii::t('report','Goods'),
				'order_status'=>Yii::t('procurement','Order Status'),
			);
	}
	
	protected function rulesEx() {
		return array(
				array('goods, goods_desc, order_status','safe'),
			);
	}
	
	protected function queueItemEx() {
		return array(
				'GOODS'=>$this->goods,
				'GOODSDESC'=>$this->goods_desc,
				'ORDERSTATUS'=>$this->order_status,
			);
	}
	
	public function init() {
		$this->id = 'RptOrderList';
		$this->name = Yii::t('report','Order Records Report');
		$this->format = 'EXCEL';
		$this->city = Yii::app()->user->city();
		$this->fields = 'start_dt,end_dt,goods,goods_desc';
		$this->start_dt = date("Y/m/d");
		$this->end_dt = date("Y/m/d");
		$this->goods = '';
		$this->goods_desc = Yii::t('misc','All');
		$this->order_status = 'all';
	}
	
	public function OrderStatusList(){
		return array(
			"all"=>Yii::t('misc','All'),
			"1:sent"=>Yii::t("procurement","Waiting for central audit"),
			"1:read"=>Yii::t("procurement","Central checked"),
			"1:approve"=>Yii::t("procurement","Shipped out, Wait for receiving"),
			"1:reject"=>Yii::t("procurement","Central refused order"),
//			"1:finished"=>Yii::t("procurement","finished"),
			"0:pending"=>Yii::t("procurement","Draft, not sent"),
			"0:sent"=>Yii::t("procurement","Waiting area audit"),
            "0:reject"=>Yii::t("procurement","Area rejected"),
            "0:finished"=>Yii::t("procurement","finished"),
        );
    }
}
