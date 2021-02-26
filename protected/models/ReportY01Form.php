<?php
/* Reimbursement Form */

class ReportY01Form extends CReportForm
{
	public $region;
	
	protected function labelsEx() {
		return array(
				'region'=>Yii::t('report','Region'),
			);
	}
	
	protected function rulesEx() {
		return array(
				array('region','safe'),
			);
	}
	
	protected function queueItemEx() {
		return array(
				'REGION'=>$this->region,
			);
	}
	
	public function init() {
		$this->id = 'RptSalesSummary';
		$this->name = Yii::t('report','Sales Summary Report');
		$this->format = 'EXCEL';
		$this->city = Yii::app()->user->city();
		$this->fields = 'year_no,month_no,region';
		$this->year = date("Y");
		$this->month = date("m");
		$this->region = Yii::app()->user->validFunction('YN01') ? 1 : 0;
	}
}
