<?php

class GoodsImForm extends CFormModel
{
	public $id;
	public $goods_code;
	public $name;
	public $classify_id;
	public $type;
	public $unit;
	public $price;
    public $big_num = 99999;
    public $small_num = 1;
    public $multiple = 1;
    public $rules_id = 0;
	public $origin;
	public $len;
	public $width;
	public $height;
	public $net_weight;
	public $gross_weight;
    public $orderClass = "Import";

	public function attributeLabels()
	{
		return array(
            'goods_code'=>Yii::t('procurement','Goods Code'),
            'name'=>Yii::t('procurement','Name'),
            'multiple'=>Yii::t('procurement','Multiple'),
            'rules_id'=>Yii::t('procurement','Hybrid Rules'),
            'classify_id'=>Yii::t('procurement','Classify'),
            'type'=>Yii::t('procurement','Type'),
            'unit'=>Yii::t('procurement','Unit'),
            'price'=>Yii::t('procurement','Price（US$）'),
            'big_num'=>Yii::t('procurement','Max Number'),
            'small_num'=>Yii::t('procurement','Min Number'),
            'origin'=>Yii::t('procurement','Origin'),
            'net_weight'=>Yii::t('procurement','Net Weight（kg）'),
            'gross_weight'=>Yii::t('procurement','Gross Weight（kg）'),
            'volume'=>Yii::t('procurement','Length×Width×Height（cm）'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, goods_code, name, classify_id, type, unit, price, rules_id, multiple, big_num, small_num, net_weight, gross_weight, origin, len, width, height','safe'),
            array('goods_code','required'),
            array('name','required'),
            array('type','required'),
            array('unit','required'),
            array('origin','required'),
            array('price','required'),
            array('classify_id','required'),
            array('price','numerical','allowEmpty'=>false,'integerOnly'=>false),
            array('classify_id','numerical','allowEmpty'=>true,'integerOnly'=>true),
            array('big_num','numerical','allowEmpty'=>false,'integerOnly'=>true,'min'=>1),
            array('small_num','numerical','allowEmpty'=>false,'integerOnly'=>true,'min'=>1),
            array('multiple','numerical','allowEmpty'=>true,'integerOnly'=>true,'min'=>1),
            array('rules_id','numerical','allowEmpty'=>true,'integerOnly'=>true,'min'=>0),
			array('name','validateName'),
			array('goods_code','validateCode'),
		);
	}

	public function validateName($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("opr_goods_im")->where('name=:name and id!=:id', array(':name'=>$this->name,':id'=>$id))->queryAll();
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
        $rows = Yii::app()->db->createCommand()->select("id")->from("opr_goods_im")->where('goods_code=:goods_code and id!=:id', array(':goods_code'=>$this->goods_code,':id'=>$id))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('procurement','the Goods Code of already exists');
            $this->addError($attribute,$message);
        }
	}

//id, goods_code, name, classify_id, type, unit, price, big_num, small_num, net_weight, gross_weight, origin, len, width, height
	public function retrieveData($index) {
		$city = Yii::app()->user->city();
		$rows = Yii::app()->db->createCommand()->select()
            ->from("opr_goods_im")->where("id=:id",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->type = $row['type'];
                $this->unit = $row['unit'];
                $this->price = sprintf("%.2f", $row['price']);
                $this->goods_code = $row['goods_code'];
                $this->classify_id = $row['classify_id'];
                $this->net_weight = $row['net_weight'];
                $this->gross_weight = $row['gross_weight'];
                $this->origin = $row['origin'];
                $this->len = $row['len'];
                $this->width = $row['width'];
                $this->height = $row['height'];
                $this->big_num = $row['big_num'];
                $this->small_num = $row['small_num'];
                $this->multiple = $row['multiple'];
                $this->rules_id = $row['rules_id'];
                break;
			}
		}
		return true;
	}

    //刪除驗證
    public function deleteValidate(){
        $rs = Yii::app()->db->createCommand()->select()->from("opr_order_goods")->where('goods_id=:goods_id',array(':goods_id'=>$this->id))->queryAll();
        if($rs){
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
                $sql = "delete from opr_goods_im where id = :id";
                break;
            case 'new':
                $sql = "insert into opr_goods_im(
							name, type, unit, price, goods_code, classify_id, net_weight, gross_weight, origin, len, width, height, rules_id, multiple, big_num, small_num,lcu,lcd
						) values (
							:name, :type, :unit, :price, :goods_code, :classify_id, :net_weight, :gross_weight, :origin, :len, :width, :height, :rules_id, :multiple, :big_num, :small_num,:lcu,:lcd
						)";
                break;
            case 'edit':
                $sql = "update opr_goods_im set
							name = :name, 
							type = :type, 
							unit = :unit,
							classify_id = :classify_id,
							net_weight = :net_weight,
							gross_weight = :gross_weight,
							origin = :origin,
							len = :len,
							width = :width,
							height = :height,
							goods_code = :goods_code,
							multiple = :multiple,
							rules_id = :rules_id,
							big_num = :big_num,
							small_num = :small_num,
							luu = :luu,
							lud = :lud,
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
        if (strpos($sql,':classify_id')!==false)
            $command->bindParam(':classify_id',$this->classify_id,PDO::PARAM_INT);
        if (strpos($sql,':gross_weight')!==false)
            $command->bindParam(':gross_weight',$this->gross_weight,PDO::PARAM_STR);
        if (strpos($sql,':net_weight')!==false)
            $command->bindParam(':net_weight',$this->net_weight,PDO::PARAM_STR);
        if (strpos($sql,':big_num')!==false)
            $command->bindParam(':big_num',$this->big_num,PDO::PARAM_INT);
        if (strpos($sql,':small_num')!==false)
            $command->bindParam(':small_num',$this->small_num,PDO::PARAM_INT);
        if (strpos($sql,':multiple')!==false)
            $command->bindParam(':multiple',$this->multiple,PDO::PARAM_INT);
        if (strpos($sql,':rules_id')!==false)
            $command->bindParam(':rules_id',$this->rules_id,PDO::PARAM_INT);
        if (strpos($sql,':len')!==false)
            $command->bindParam(':len',$this->len,PDO::PARAM_INT);
        if (strpos($sql,':width')!==false)
            $command->bindParam(':width',$this->width,PDO::PARAM_INT);
        if (strpos($sql,':height')!==false)
            $command->bindParam(':height',$this->height,PDO::PARAM_INT);
        if (strpos($sql,':name')!==false)
            $command->bindParam(':name',$this->name,PDO::PARAM_STR);
        if (strpos($sql,':goods_code')!==false)
            $command->bindParam(':goods_code',$this->goods_code,PDO::PARAM_STR);
        if (strpos($sql,':origin')!==false)
            $command->bindParam(':origin',$this->origin,PDO::PARAM_STR);
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
