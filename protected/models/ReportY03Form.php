<?php
/* Reimbursement Form */

class ReportY03Form extends CReportForm
{
	public $user_ids;
	public $user_names;
	
	protected function labelsEx() {
		return array(
				'user_ids'=>Yii::t('report','Order Person'),
			);
	}
	
	protected function rulesEx() {
		return array(
				array('user_ids, user_names','safe'),
			);
	}
	
	protected function queueItemEx() {
		return array(
				'USER_IDS'=>$this->user_ids,
				'USER_NAMES'=>$this->user_names,
			);
	}
	
	public function init() {
		$this->id = 'RptPickingList';
		$this->name = Yii::t('report','Picking Records Report');
		$this->format = 'EXCEL';
		$this->city = Yii::app()->user->city();
		$this->fields = 'start_dt,end_dt,user_ids,user_names';
		$this->start_dt = date("Y/m").'/01';
		$this->end_dt = date("Y/m/d");
		$this->user_ids = '';
		$this->user_names = Yii::t('misc','All');
	}
}
