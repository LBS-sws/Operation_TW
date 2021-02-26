<?php

class ClassifyForm extends CFormModel
{
	public $id;
	public $name;
	public $class_type;
	public $level;

	public function attributeLabels()
	{
		return array(
            'name'=>Yii::t('procurement','Name'),
            'class_type'=>Yii::t('procurement','Class Type'),
            'level'=>Yii::t('procurement','Level'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, name,class_type,level','safe'),
            array('name','required'),
            array('class_type','required'),
			array('name','validateName'),
            array('level','numerical','allowEmpty'=>true,'integerOnly'=>true,'min'=>0),
		);
	}

	public function validateName($attribute, $params){
        $city = Yii::app()->user->city();
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("opr_classify")
            ->where('name=:name and id!=:id and class_type=:class_type',
                array(':name'=>$this->name,':id'=>$id,':class_type'=>$this->class_type))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('procurement','the name of already exists');
            $this->addError($attribute,$message);
        }
	}

	public function retrieveData($index) {
		$rows = Yii::app()->db->createCommand()->select("*")
            ->from("opr_classify")->where("id=:id",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->class_type = $row['class_type'];
                $this->level = $row['level'];
                break;
			}
		}
		return true;
	}

    //獲取分類列表(排除沒有物品的分類)
    public function getClassifyList($str="Import"){
	    switch ($str){
            case "Import":
                $from = "opr_goods_im";
                break;
            case "Domestic":
                $from = "opr_goods_do";
                break;
            case "Fast":
                $from = "opr_goods_fa";
                break;
            case "Warehouse":
                $from = "opr_warehouse";
                break;
            default:
                $from = "opr_warehouse";
        }
        $city = Yii::app()->user->city();
	    $arr = array(""=>"");
        $rs = Yii::app()->db->createCommand()->select()->from("opr_classify")->where("class_type=:class_type",array(":class_type"=>$str))->order('level desc')->queryAll();
        if($rs){
            foreach ($rs as $row){
                if($from == "opr_warehouse"){
                    $bool = Yii::app()->db->createCommand()->select("count(id)")
                        ->from($from)->where("classify_id=:classify_id and city=:city",array(":classify_id"=>$row["id"],":city"=>$city))->queryScalar();
                }else{
                    $bool = Yii::app()->db->createCommand()->select("count(id)")->from($from)->where("classify_id=:classify_id",array(":classify_id"=>$row["id"]))->queryScalar();
                }
                if($bool>0){
                    $arr[$row["id"]] = $row["name"];
                }
            }
        }
        return $arr;
    }


    //獲取分類列表
    public function getAllClassifyList($str="Import"){
        $arr = array(""=>"");
        $rs = Yii::app()->db->createCommand()->select()->from("opr_classify")->where("class_type=:class_type",array(":class_type"=>$str))->order('level desc')->queryAll();
        if($rs){
            foreach ($rs as $row){
                $arr[$row["id"]] = $row["name"];
            }
        }
        return $arr;
    }

    //獲取物品列表(按分類生成二維數組）
    public function getGoodsListToClassify($str="Import"){
        switch ($str){
            case "Import":
                $from = "opr_goods_im";
                break;
            case "Domestic":
                $from = "opr_goods_do";
                break;
            case "Fast":
                $from = "opr_goods_fa";
                break;
            case "Warehouse":
                $from = "opr_warehouse";
                break;
            default:
                $from = "opr_warehouse";
        }
        $city = Yii::app()->user->city();
        $arr = array(array("id"=>0,"name"=>Yii::t("procurement","All Goods Class"),"list"=>array()));
        $rs = Yii::app()->db->createCommand()->select()->from("opr_classify")->where("class_type=:class_type",array(":class_type"=>$str))->order('level desc')->queryAll();
        if($rs){
            foreach ($rs as $row){
                if($from == "opr_warehouse"){
                    $goodList = Yii::app()->db->createCommand()->select("*")
                        ->from($from)->where("classify_id=:classify_id and city=:city and display=1",array(":classify_id"=>$row["id"],":city"=>$city))->queryAll();
                }else{
                    $goodList = Yii::app()->db->createCommand()->select("*")
                        ->from($from)->where("classify_id=:classify_id",array(":classify_id"=>$row["id"]))->queryAll();
                }
                if($goodList){
                    array_push($arr,array("id"=>$row["id"],"name"=>$row["name"],"list"=>$goodList));
                }
            }
        }
        return $arr;
    }

    //根據訂單id查分類名字
    public function getClassifyToId($classify_id){
        $rs = Yii::app()->db->createCommand()->select("name")
            ->from("opr_classify")->where('id=:id',array(':id'=>$classify_id))->queryAll();
        if($rs){
            return $rs[0]["name"];
        }
        return "";
    }
    //獲取小分類的類型
    public function getArrTypeClass(){
        $arr = OrderGoods::getArrGoodsClass();
        $arr["Warehouse"] = Yii::t("procurement","Warehouse");
        return $arr;
    }

    //刪除驗證
    public function deleteValidate(){
        $rs0 = Yii::app()->db->createCommand()->select()->from("opr_goods_do")->where('classify_id=:classify_id',array(':classify_id'=>$this->id))->queryAll();
        $rs1 = Yii::app()->db->createCommand()->select()->from("opr_goods_fa")->where('classify_id=:classify_id',array(':classify_id'=>$this->id))->queryAll();
        $rs2 = Yii::app()->db->createCommand()->select()->from("opr_goods_im")->where('classify_id=:classify_id',array(':classify_id'=>$this->id))->queryAll();
        $rs3 = Yii::app()->db->createCommand()->select()->from("opr_warehouse")->where('classify_id=:classify_id',array(':classify_id'=>$this->id))->queryAll();
        if($rs0 || $rs1 || $rs2 || $rs3){
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
                $sql = "delete from opr_classify where id = :id";
                break;
            case 'new':
                $sql = "insert into opr_classify(
							name,level,class_type, lcu, lcd
						) values (
							:name,:level,:class_type, :lcu, :lcd
						)";
                break;
            case 'edit':
                $sql = "update opr_classify set
							name = :name, 
							level = :level, 
							class_type = :class_type, 
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
        if (strpos($sql,':level')!==false)
            $command->bindParam(':level',$this->level,PDO::PARAM_INT);
        if (strpos($sql,':class_type')!==false)
            $command->bindParam(':class_type',$this->class_type,PDO::PARAM_STR);

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
