<?php

class GoodsForm extends CFormModel
{
	public $id;
	public $goods_code;
	public $name;
	public $goods_class;
	public $type;
	public $unit;
	public $price;
	public $big_num;
	public $small_num;
	public $luu;
	public $lcu;

	public function attributeLabels()
	{
		return array(
            'goods_code'=>Yii::t('procurement','Goods Code'),
            'name'=>Yii::t('procurement','Name'),
            'goods_class'=>Yii::t('procurement','Goods Class'),
            'type'=>Yii::t('procurement','Type'),
            'unit'=>Yii::t('procurement','Unit'),
            'price'=>Yii::t('procurement','Price（RMB）'),
            'big_num'=>Yii::t('procurement','Headquarters Number'),
            'small_num'=>Yii::t('procurement','Area Number'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, goods_code, name, goods_class, type, unit, price, big_num, small_num, lcu, luu','safe'),
            array('goods_code','required'),
            array('goods_code','numerical','allowEmpty'=>false,'integerOnly'=>true),
            array('name','required'),
            array('goods_class','required'),
            array('big_num','numerical','allowEmpty'=>true,'integerOnly'=>true,'min'=>0),
            array('small_num','numerical','allowEmpty'=>true,'integerOnly'=>true,'min'=>0),
            array('type','required'),
            array('unit','required'),
            array('price','required'),
            array('price','numerical','allowEmpty'=>false,'integerOnly'=>false),
			array('name','validateName'),
			array('goods_code','validateCode'),
		);
	}

	public function validateName($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("opr_goods")->where('name=:name and id!=:id', array(':name'=>$this->name,':id'=>$id))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('procurement','the name of already exists');
            $this->addError($attribute,$message);
        }
	}
	public function validateCode($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("opr_goods")->where('goods_code=:goods_code and id!=:id', array(':goods_code'=>$this->goods_code,':id'=>$id))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('procurement','the Goods Code of already exists');
            $this->addError($attribute,$message);
        }
	}

	public function retrieveData($index) {
		$city = Yii::app()->user->city();
		$rows = Yii::app()->db->createCommand()->select("id,name,type,unit,price,goods_code,goods_class,big_num,small_num")
            ->from("opr_goods")->where("id=:id",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->type = $row['type'];
                $this->unit = $row['unit'];
                $this->price = $row['price'];
                $this->goods_code = $row['goods_code'];
                $this->goods_class = $row['goods_class'];
                $this->big_num = $row['big_num'];
                $this->small_num = $row['small_num'];
                break;
			}
		}
		return true;
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
                $sql = "delete from opr_goods where id = :id";
                break;
            case 'new':
                $sql = "insert into opr_goods(
							name, type, unit, price, goods_code, goods_class, big_num, small_num
						) values (
							:name, :type, :unit, :price, :goods_code, :goods_class, :big_num, :small_num
						)";
                break;
            case 'edit':
                $sql = "update opr_goods set
							name = :name, 
							type = :type, 
							unit = :unit,
							goods_class = :goods_class,
							goods_code = :goods_code,
							big_num = :big_num,
							small_num = :small_num,
							price = :price
						where id = :id
						";
                break;
        }
		if (empty($sql)) return false;

        //$city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;

        $command=$connection->createCommand($sql);
        if(empty($this->big_num)){
            $this->big_num = 0;
        }
        if(empty($this->small_num)){
            $this->small_num = 0;
        }
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        if (strpos($sql,':big_num')!==false)
            $command->bindParam(':big_num',$this->big_num,PDO::PARAM_INT);
        if (strpos($sql,':small_num')!==false)
            $command->bindParam(':small_num',$this->small_num,PDO::PARAM_INT);
        if (strpos($sql,':name')!==false)
            $command->bindParam(':name',$this->name,PDO::PARAM_STR);
        if (strpos($sql,':goods_code')!==false)
            $command->bindParam(':goods_code',$this->goods_code,PDO::PARAM_STR);
        if (strpos($sql,':goods_class')!==false)
            $command->bindParam(':goods_class',$this->goods_class,PDO::PARAM_STR);
        if (strpos($sql,':type')!==false)
            $command->bindParam(':type',$this->type,PDO::PARAM_STR);
        if (strpos($sql,':unit')!==false)
            $command->bindParam(':unit',$this->unit,PDO::PARAM_STR);
        if (strpos($sql,':price')!==false)
            $command->bindParam(':price',$this->price,PDO::PARAM_STR);
        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcu')!==false)
            $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
        $command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->scenario = "edit";
        }
		return true;
	}
}
