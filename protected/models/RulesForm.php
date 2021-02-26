<?php

class RulesForm extends CFormModel
{
	public $id;
	public $name;
	public $multiple = 1;
	public $max = 99999;
	public $min = 1;

	public function attributeLabels()
	{
		return array(
            'name'=>Yii::t('procurement','Name'),
            'multiple'=>Yii::t('procurement','Multiple'),
            'max'=>Yii::t('procurement','Max Number'),
            'min'=>Yii::t('procurement','Min Number'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, name,multiple,max,min','safe'),
            array('name','required'),
            array('multiple','required'),
            array('max','required'),
            array('min','required'),
			array('name','validateName'),
            array('multiple','numerical','allowEmpty'=>true,'integerOnly'=>true,'min'=>1),
            array('max','numerical','allowEmpty'=>true,'integerOnly'=>true,'min'=>1),
            array('min','numerical','allowEmpty'=>true,'integerOnly'=>true,'min'=>1),
		);
	}

	public function validateName($attribute, $params){
        $city = Yii::app()->user->city();
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("opr_goods_rules")
            ->where('name=:name and id!=:id', array(':name'=>$this->name,':id'=>$id))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('procurement','the name of already exists');
            $this->addError($attribute,$message);
        }
	}

	public function retrieveData($index) {
		$rows = Yii::app()->db->createCommand()->select("*")
            ->from("opr_goods_rules")->where("id=:id",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->multiple = $row['multiple'];
                $this->max = $row['max'];
                $this->min = $row['min'];
                break;
			}
		}
		return true;
	}

    //獲取規則列表
    public function getRulesList(){
	    $arr = array(0=>"");
        $rs = Yii::app()->db->createCommand()->select()->from("opr_goods_rules")->queryAll();
        if($rs){
            foreach ($rs as $row){
                $arr[$row["id"]] = $row["name"];
            }
        }
        return $arr;
    }

    //根據規則id查規則列表
    public function getRulesToId($rules_id){
        $rs = Yii::app()->db->createCommand()->select()
            ->from("opr_goods_rules")->where('id=:id',array(':id'=>$rules_id))->queryAll();
        if($rs){
            return $rs[0];
        }
        return array();
    }

    //刪除驗證
    public function deleteValidate(){
        $rs0 = Yii::app()->db->createCommand()->select()->from("opr_goods_do")->where('rules_id=:rules_id',array(':rules_id'=>$this->id))->queryAll();
        $rs1 = Yii::app()->db->createCommand()->select()->from("opr_goods_fa")->where('rules_id=:rules_id',array(':rules_id'=>$this->id))->queryAll();
        $rs2 = Yii::app()->db->createCommand()->select()->from("opr_goods_im")->where('rules_id=:rules_id',array(':rules_id'=>$this->id))->queryAll();
        if($rs0 || $rs1 || $rs2){
            return false;
        }else{
            return true;
        }
    }

	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveGoods($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update. ('.$e->getMessage().')');
		}
	}

	protected function saveGoods(&$connection) {
		$sql = '';
        switch ($this->scenario) {
            case 'delete':
                $sql = "delete from opr_goods_rules where id = :id";
                break;
            case 'new':
                $sql = "insert into opr_goods_rules(
							name,multiple,max,min, lcu, lcd
						) values (
							:name,:multiple,:max,:min, :lcu, :lcd
						)";
                break;
            case 'edit':
                $sql = "update opr_goods_rules set
							name = :name, 
							multiple = :multiple, 
							max = :max, 
							min = :min, 
							luu = :luu,
							lud = :lud
						where id = :id
						";
                break;
        }
		if (empty($sql)) return false;

        //$city = Yii::app()->user->city();
        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;

        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        if (strpos($sql,':name')!==false)
            $command->bindParam(':name',$this->name,PDO::PARAM_STR);
        if (strpos($sql,':multiple')!==false)
            $command->bindParam(':multiple',$this->multiple,PDO::PARAM_INT);
        if (strpos($sql,':max')!==false)
            $command->bindParam(':max',$this->max,PDO::PARAM_INT);
        if (strpos($sql,':min')!==false)
            $command->bindParam(':min',$this->min,PDO::PARAM_INT);

        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcu')!==false)
            $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lud')!==false)
            $command->bindParam(':lud',date("Y-m-d H:s:i"),PDO::PARAM_STR);
        if (strpos($sql,':lcd')!==false)
            $command->bindParam(':lcd',date("Y-m-d H:s:i"),PDO::PARAM_STR);
        $command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->scenario = "edit";
        }
		return true;
	}
}
