<?php

class OrderAccForm extends CFormModel
{
	public $id;
	public $acc_do;
	public $acc_im;
	public $city;

    public function attributeLabels()
    {
        return array(
            'acc_do'=>Yii::t('procurement','Domestic').Yii::t('procurement','Order Access'),
            'acc_im'=>Yii::t('procurement','Import').Yii::t('procurement','Order Access')
        );
    }

    public function init(){
        $city = Yii::app()->user->city();
        $rows = Yii::app()->db->createCommand()->select("*")
            ->from("opr_order_acc")->where("city=:city",array(":city"=>$city))->queryRow();
        if($rows){
            $this->id = $rows["id"];
            $this->acc_do = $rows["acc_do"];
            $this->acc_im = $rows["acc_im"];
            $this->city = $rows["city"];
            $this->scenario = "edit";
        }else{
            $this->acc_do = 0;
            $this->acc_im = 0;
            $this->city = $city;
            $this->scenario = "new";
        }
    }

    public function getOpenSelectList(){
        return array(Yii::t("procurement","No Allow"),Yii::t("procurement","Allow"));
    }

    public function getNowOrderAcc(){
        $city = Yii::app()->user->city();
        $rows = Yii::app()->db->createCommand()->select("*")
            ->from("opr_order_acc")->where("city=:city",array(":city"=>$city))->queryRow();
        if($rows){
            return array(
                "Domestic"=>$rows["acc_do"],
                "Import"=>$rows["acc_im"]
            );
        }else{
            return array(
                "Domestic"=>0,
                "Import"=>0
            );
        }
    }
	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, acc_do, acc_im, city','safe'),
            array('acc_do','required'),
            array('acc_im','required'),
		);
	}

	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveActivity($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update. ('.$e->getMessage().')');
		}
	}

	protected function saveActivity(&$connection) {
		$sql = '';
        switch ($this->scenario) {
            case 'new':
                $sql = "insert into opr_order_acc(
							city, acc_do, acc_im,lcu
						) values (
							:city, :acc_do, :acc_im, :lcu
						)";
                break;
            case 'edit':
                $sql = "update opr_order_acc set
							acc_do = :acc_do,
							acc_im = :acc_im,
							luu = :luu
						where id = :id
						";
                break;
        }
		if (empty($sql)) return false;

        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;

        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);

        if (strpos($sql,':acc_do')!==false)
            $command->bindParam(':acc_do',$this->acc_do,PDO::PARAM_INT);
        if (strpos($sql,':acc_im')!==false)
            $command->bindParam(':acc_im',$this->acc_im,PDO::PARAM_INT);

        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$city,PDO::PARAM_STR);
        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcu')!==false)
            $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
        $command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
        }
		return true;
	}
}
