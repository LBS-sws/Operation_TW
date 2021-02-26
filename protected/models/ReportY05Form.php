<?php
/* Reimbursement Form */

class ReportY05Form extends CReportForm
{
	
	public function init() {
		$this->id = 'RptStorageList';
		$this->name = Yii::t('app','storage Report');
		$this->format = 'EXCEL';
		$this->city = Yii::app()->user->city();
		$this->fields = 'start_dt,end_dt';
		$this->start_dt = date("Y/m").'/01';
		$this->end_dt = date("Y/m/d");
	}
}
