<?php
/* Reimbursement Form */

class ReportY04Form extends CReportForm
{
    public $city_desc;
	
	protected function labelsEx() {
		return array(
            'city_desc'=>"选择城市",
        );
	}
	
	protected function rulesEx() {
		return array(
            array('city_desc','safe'),
        );
	}
	
	protected function queueItemEx() {
		return array(
				//'REGION'=>$this->region,
			);
	}
	
	public function init() {
		$this->id = 'RptBusiness';
		$this->name = Yii::t('app','Business Report');
		$this->format = 'EXCEL';
        $this->city = "";
        $this->city_desc = Yii::t('misc','All');
		$this->fields = 'year_no,month_no';
		$this->year = date("Y");
		$this->month = intval(date("m"));
	}
}
