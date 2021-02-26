<?php

class MonthlyRateForm extends CFormModel
{
	public $city;
	public $city_name;
	public $rate;

	public $rate_cat = array(
					'SP'=>'Service Patent Fee',
					'PP'=>'Paper Patent Fee',
				);

	public function attributeLabels()
	{
		return array(
			'city'=>Yii::t('misc','City'),
		);
	}

	public function rules()
	{
		return array(
			array('city','required'), 
			array('city_name','safe'), 
			array('rate','validateRecord'), 
		);
	}

	public function validateRecord($attribute, $params){
		$message = '';
		foreach ($this->rate_cat as $key=>$value) {
			if (!isset($this->rate[$key]) || empty($this->rate[$key])) {
				$message = Yii::t('monthly',$this->rate_cat[$key]).Yii::t('monthly',' cannnot be empty');
				$this->addError($attribute,$message);
			} elseif (!is_numeric($this->rate[$key])) {
				$message = Yii::t('monthly',$this->rate_cat[$key]).Yii::t('monthly',' is invalid');
				$this->addError($attribute,$message);
			}
		}
	}

	public function retrieveData($index)
	{
		$uid = Yii::app()->user->id;
		$sysid = Yii::app()->params['systemId'];
		$suffix = Yii::app()->params['envSuffix'];

		$sql = "select a.*, b.name as city_name
				from opr_monthly_rate a
				left outer join security$suffix.sec_city b on a.city=b.code
				where a.city='$index'
			";
		$row = Yii::app()->db->createCommand($sql)->queryRow();
		if ($row!==false) {
			$this->city = $row['city'];
			$this->city_name = $row['city']=='~ZZZZ' ? Yii::t('monthly','Default') : $row['city_name'];
			$items = json_decode($row['rate'], true);
			foreach ($this->rate_cat as $key=>$value) {
				$this->rate[$key] = isset($items[$key]) ? $items[$key] : 0;
			}
			return true;
		}
		return false;
	}

	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveMonthlyRate($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update. ('.$e->getMessage().')');
		}
	}
	
	public function saveMonthlyRate(&$connection) {
		$uid = Yii::app()->user->id;

		$sql = "";
		switch ($this->scenario) {
			case 'new':
				$sql = "insert into opr_monthly_rate(city, rate, lcu, luu) values(:city, :rate, :uid, :uid)";
				break;
			case 'edit':
				$sql = "update opr_monthly_rate set rate=:rate, luu=:uid where city=:city";
				break;
			case 'delete':
				$sql = "delete from opr_monthly_rate where city=:city";
				break;
		}
		if ($sql!="") {
			$command=$connection->createCommand($sql);
			if (strpos($sql,':city')!==false)
				$command->bindParam(':city',$this->city,PDO::PARAM_STR);
			if (strpos($sql,':rate')!==false) {
				$rate = json_encode($this->rate);
				$command->bindParam(':rate',$rate,PDO::PARAM_STR);
			}
			if (strpos($sql,':uid')!==false)
				$command->bindParam(':uid',$uid,PDO::PARAM_STR);
			$command->execute();
		}
	}
	
	public function getCityList() {
		$list = General::getCityListWithNoDescendant();
		$sql = "select city from opr_monthly_rate where city<>'~ZZZZ'";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		foreach ($rows as $row) {
			if (isset($list[$row['city']])) unset($list[$row['city']]);
		}
		return $list;
	}
}
