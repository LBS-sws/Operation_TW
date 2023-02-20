<?php

class MonthlyForm extends CFormModel
{
	public $id;
	public $year_no;
	public $month_no;
	public $lcd;
	public $record = array();
	public $wfstatus;
	public $wfstatusdesc;
	public $city;
	public $city_name;
	public $listform;
	public $reason;
	
	public $wfactionuser;

	public $files;

	public $docMasterId = array(
							'oper1'=>0,
							'oper2'=>0,
							'oper3'=>0,
							'oper4'=>0
						);
	public $removeFileId = array(
							'oper1'=>0,
							'oper2'=>0,
							'oper3'=>0,
							'oper4'=>0
						);
	public $no_of_attm = array(
							'oper1'=>0,
							'oper2'=>0,
							'oper3'=>0,
							'oper4'=>0
						);
	
	public function attributeLabels()
	{
		return array(
			'city'=>Yii::t('misc','City'),
			'city_name'=>Yii::t('misc','City'),
			'year_no'=>Yii::t('report','Year'),
			'month_no'=>Yii::t('report','Month'),
			'wfstatusdesc'=>Yii::t('workflow','Flow Status'),
			'reason'=>Yii::t('workflow','Reject Reason'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, year_no, month_no, lcd, city, city_name, wfstatus, wfstatusdesc, listform, reason, wfactionuser','safe'),
			array('record','validateRecord'),
			array('files, removeFileId, docMasterId','safe'), 
			array ('no_of_attm','validateAttachment'),
		);
	}

	public function validateRecord($attribute, $params){
		$message = '';
		foreach ($this->record as $data) {
			if (isset($data['updtype']) && $data['updtype']=='M') {
				if (isset($data['fieldtype'])) {
					switch($data['fieldtype']) {
						case 'N':
							if (isset($data['datavalue']) && !empty($data['datavalue']) && !is_numeric($data['datavalue'])) {
								$message = $data['name'].Yii::t('monthly',' is invalid');
								$this->addError($attribute,$message);
							}
						break;
					}
				}
			}
		}
	}

	public function validateAttachment($attribute, $params) {
		$count1 = $this->no_of_attm['oper1'];
		$count2 = $this->no_of_attm['oper2'];
		$count3 = $this->no_of_attm['oper3'];
		$count4 = $this->no_of_attm['oper4'];
		$val_4 = $this->record[4]['datavalue'];
		$val_5 = $this->record[5]['datavalue'];
		if (($this->scenario=='submit' || $this->scenario=='resubmit') && (empty($count1) || $count1==0)) {
			$this->addError($attribute, Yii::t('monthly','Please upload').' '.Yii::t('monthly','System Report'));
		}
		if (($this->scenario=='submit' || $this->scenario=='resubmit') && (!empty($val_4) && $val_4 > 0) && (empty($count2) || $count2==0)) {
			$this->addError($attribute, Yii::t('monthly','Please upload').' '.Yii::t('monthly','Puriscent Report'));
		}
		if (($this->scenario=='submit' || $this->scenario=='resubmit') && (!empty($val_5) && $val_5 > 0) && (empty($count3) || $count3==0)) {
			$this->addError($attribute, Yii::t('monthly','Please upload').' '.Yii::t('monthly','Purification Report'));
		}
		if (($this->scenario=='accept') && (empty($count4) || $count4==0)) {
			$this->addError($attribute, Yii::t('monthly','Please upload').' '.Yii::t('monthly','Statement'));
		}
	}

	public function retrieveData($index) {
		$suffix = Yii::app()->params['envSuffix'];
		$citylist = Yii::app()->user->city_allow();
		$sql = "select a.year_no, a.month_no, b.id, b.hdr_id, b.data_field, b.data_value, c.name, c.upd_type, c.field_type, c.function_name, b.manual_input, a.lcd, 
				a.city, d.name as city_name, workflow$suffix.RequestStatus('OPRPT',a.id,a.lcd) as wfstatus,
				workflow$suffix.RequestStatusDesc('OPRPT',a.id,a.lcd) as wfstatusdesc,
				docman$suffix.countdoc('OPER1',a.id) as oper1countdoc,
				docman$suffix.countdoc('OPER2',a.id) as oper2countdoc,
				docman$suffix.countdoc('OPER3',a.id) as oper3countdoc,
				docman$suffix.countdoc('OPER4',a.id) as oper4countdoc
				from opr_monthly_hdr a, opr_monthly_dtl b, opr_monthly_field c, security$suffix.sec_city d  
				where a.id=$index and a.city in ($citylist)
				and a.id=b.hdr_id and b.data_field=c.code
				and a.city=d.code 
				and c.status='Y'
				order by a.year_no, a.month_no, c.code
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			$hid = 0;
			foreach ($rows as $row) {
				if ($hid!=$row['hdr_id']) {
					$hid = $row['hdr_id'];
					$this->id = $hid;
					$this->year_no = $row['year_no'];
					$this->month_no = $row['month_no'];
					$this->city = $row['city'];
					$this->city_name = $row['city_name'];
					$this->lcd = $row['lcd'];
					$this->wfstatus = $row['wfstatus'];
					$this->wfstatusdesc = $row['wfstatusdesc'];
					$this->no_of_attm['oper1'] = $row['oper1countdoc'];
					$this->no_of_attm['oper2'] = $row['oper2countdoc'];
					$this->no_of_attm['oper3'] = $row['oper3countdoc'];
					$this->no_of_attm['oper4'] = $row['oper4countdoc'];
				}
				$temp = array();
				$temp['id'] = $row['id'];
				$temp['code'] = $row['data_field'];
				$temp['function_name'] = $row['function_name'];
				$temp['name'] = $row['name'];
				$temp['datavalue'] = is_numeric($row['data_value']) ? number_format($row['data_value'],2,".","") : $row['data_value'];
				$temp['datavalueold'] = is_numeric($row['data_value']) ? number_format($row['data_value'],2,".","") : $row['data_value'];
				$temp['updtype'] = $row['upd_type'];
				$temp['fieldtype'] = $row['field_type'];
				$temp['manualinput'] = $row['manual_input'];
				$this->record[$row['function_name']] = $temp;
			}
		}

		if ($this->wfstatus=='PA' || $this->wfstatus=='PH') {
			$wf = new WorkflowOprpt;
			$connection = $wf->openConnection();
			if ($wf->initReadOnlyProcess('OPRPT',$this->id,$this->lcd)) {
				$actionusers = $wf->getCurrentStateRespUser();
				$this->wfactionuser = empty($actionusers) ? '' : implode('/',$actionusers);
			}
		}
		
		if ($this->wfstatus=='PS') {
			$wf = new WorkflowOprpt;
			$connection = $wf->openConnection();
			if ($wf->initReadOnlyProcess('OPRPT',$this->id,$this->lcd)) {
				$reasons1 = $wf->getLastStateActionRemarks('DENY');
				$reasons2 = $wf->getLastStateActionRemarks('HDDENY');
				$reasons = array_merge($reasons1, $reasons2);
				if (!empty($reasons)) {
					foreach ($reasons as $userid=>$reason) {
						$this->reason = empty($this->reason) ? $reason : $this->reason."<br>".$reason;
					}
				}
			}
		}
		return true;
	}
	
	public function submit()
	{
		$wf = new WorkflowOprpt;
		$connection = $wf->openConnection();
		try {
			$this->saveMonthly($connection);
			$this->updateDocman($connection,'OPER1');
			$this->updateDocman($connection,'OPER2');
			$this->updateDocman($connection,'OPER3');
			$this->updateDocman($connection,'OPER4');
			if ($wf->startProcess('OPRPT',$this->id,$this->lcd)) {
				$wf->saveRequestData('CITY',$this->city);
				$wf->saveRequestData('CITYNAME',$this->city_name);
				$wf->saveRequestData('REQ_USER',Yii::app()->user->id);
				$wf->saveRequestData('YEAR',$this->year_no);
				$wf->saveRequestData('MONTH',$this->month_no);
				$wf->takeAction('SUBMIT');
			}
			$wf->transaction->commit();
		}
		catch(Exception $e) {
			$wf->transaction->rollback();
			throw new CHttpException(404,'Cannot update.'.$e->getMessage());
		}
	}

	public function resubmit()
	{
		$wf = new WorkflowOprpt;
		$connection = $wf->openConnection();
		try {
			$this->saveMonthly($connection);
			$this->updateDocman($connection,'OPER1');
			$this->updateDocman($connection,'OPER2');
			$this->updateDocman($connection,'OPER3');
			$this->updateDocman($connection,'OPER4');
			if ($wf->startProcess('OPRPT',$this->id,$this->lcd)) {
				$wf->takeAction('RESUBMIT');
			}
			$wf->transaction->commit();
		}
		catch(Exception $e) {
			$wf->transaction->rollback();
			throw new CHttpException(404,'Cannot update.'.$e->getMessage());
		}
	}

	public function accept()
	{
		$wf = new WorkflowOprpt;
		$connection = $wf->openConnection();
		try {
//			$this->saveMonthly($connection);
			if ($wf->startProcess('OPRPT',$this->id,$this->lcd)) {
				$wf->takeAction('APPROVE');
			}
			$wf->transaction->commit();
		}
		catch(Exception $e) {
			$wf->transaction->rollback();
			throw new CHttpException(404,'Cannot update.'.$e->getMessage());
		}
	}

	public function acceptm()
	{
		$wf = new WorkflowOprpt;
		$connection = $wf->openConnection();
		try {
//			$this->saveMonthly($connection);
			if ($wf->startProcess('OPRPT',$this->id,$this->lcd)) {
				$wf->takeAction('HDAPPROVE');
			}
			$wf->transaction->commit();
		}
		catch(Exception $e) {
			$wf->transaction->rollback();
			throw new CHttpException(404,'Cannot update.'.$e->getMessage());
		}
	}

	public function reject()
	{
		$wf = new WorkflowOprpt;
		$connection = $wf->openConnection();
		try {
//			$this->saveMonthly($connection);
			if ($wf->startProcess('OPRPT',$this->id,$this->lcd)) {
				$wf->takeAction('DENY',$this->reason);
			}
			$wf->transaction->commit();
		}
		catch(Exception $e) {
			$wf->transaction->rollback();
			throw new CHttpException(404,'Cannot update.'.$e->getMessage());
		}
	}

	public function rejectm()
	{
		$wf = new WorkflowOprpt;
		$connection = $wf->openConnection();
		try {
//			$this->saveMonthly($connection);
			if ($wf->startProcess('OPRPT',$this->id,$this->lcd)) {
				$wf->takeAction('HDDENY',$this->reason);
			}
			$wf->transaction->commit();
		}
		catch(Exception $e) {
			$wf->transaction->rollback();
			throw new CHttpException(404,'Cannot update.'.$e->getMessage());
		}
	}

	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveMonthly($connection);
			$this->updateDocman($connection,'OPER1');
			$this->updateDocman($connection,'OPER2');
			$this->updateDocman($connection,'OPER3');
			$this->updateDocman($connection,'OPER4');
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update. ('.$e->getMessage().')');
		}
	}

	protected function saveMonthly(&$connection) {
		$sql = '';
		switch ($this->scenario) {
			case 'edit' || 'submit' || 'resubmit':
				$sql = "update opr_monthly_dtl set
							data_value = :data_value,
							manual_input = :manual_input,
							luu = :uid 
						where id = :id
					";
				break;
		}
		if (empty($sql)) return false;

		$city = $this->city; //Yii::app()->user->city();
		$uid = Yii::app()->user->id;
		
		$select = "select function_name from opr_monthly_field 
					where status = 'Y'
					order by code
				";
		$rows = Yii::app()->db->createCommand($select)->queryAll();
		foreach ($rows as $row) {
			$code = $row['function_name'];
			if (isset($this->record[$code])) {
				$num = is_numeric($this->record[$code]['datavalue']) ? number_format($this->record[$code]['datavalue'],2,".","") : $this->record[$code]['datavalue'];
				$command=$connection->createCommand($sql);
				if (strpos($sql,':id')!==false)
					$command->bindParam(':id',$this->record[$code]['id'],PDO::PARAM_INT);
				if (strpos($sql,':data_value')!==false) {
					$command->bindParam(':data_value',$num,PDO::PARAM_STR);
				}
				if (strpos($sql,':manual_input')!==false) {
					$input = 'N';
					if ($this->record[$code]['updtype']=='M' || $this->record[$code]['datavalueold']==$num) {
						$input = $this->record[$code]['manualinput'];
					} else {
						if ($this->record[$code]['updtype']!='M') {
							$input = ($this->record[$code]['datavalue'] || $num=='0.00') ? 'N' : 'Y';
						}
					}
					$command->bindParam(':manual_input',$input,PDO::PARAM_STR);
				}
				if (strpos($sql,':uid')!==false)
				$command->bindParam(':uid',$uid,PDO::PARAM_STR);
				$command->execute();
			}
		}
		return true;
	}
	
	protected function updateDocman(&$connection, $doctype) {
		if ($this->scenario=='new') {
			$docidx = strtolower($doctype);
			if ($this->docMasterId[$docidx] > 0) {
				$docman = new DocMan($doctype,$this->id,get_class($this));
				$docman->masterId = $this->docMasterId[$docidx];
				$docman->updateDocId($connection, $this->docMasterId[$docidx]);
			}
		}
	}

//	public function exportExcel() {
//		$file = "oprpt".date("YmdHi").".xls";
//		header("Content-type: application/vnd.ms-excel");
//		header("Content-Disposition: attachment; filename=$file");
//		$export = "<table>";
//		
//		$export .= "</table>";
//		echo $export;
//	}
	
	public function validUserInCurrentAction() {
		$uid = Yii::app()->user->id;
		return (strpos('/'.$this->wfactionuser.'/', '/'.$uid.'/')!==false);
	}
	
	public function isReadOnly() {
		return ($this->scenario=='view'|| strpos('~~PS~','~'.$this->wfstatus.'~')===false);
	}
	
	public function getMonthlyRate() {
		$city = $this->city;
		$sql = "select rate from opr_monthly_rate where city='$city' or city='~ZZZZ' order by city limit 1";
		$row = Yii::app()->db->createCommand($sql)->queryRow();
		$rate = json_decode($row['rate'],true);
		return $rate;
	}
}
