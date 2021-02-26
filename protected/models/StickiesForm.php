<?php

class StickiesForm extends CFormModel
{
	public $id;
	public $name;
	public $content;

	public function attributeLabels()
	{
		return array(
            'name'=>Yii::t('procurement','Name'),
            'content'=>Yii::t('procurement','Content'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, name, content','safe'),
            array('name','required'),
            array('content','required'),
			array('name','validateName'),
		);
	}

	public function validateName($attribute, $params){
        $city = Yii::app()->user->city();
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("opr_stickies")
            ->where('name=:name and id!=:id', array(':name'=>$this->name,':id'=>$id))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('procurement','the name of already exists');
            $this->addError($attribute,$message);
        }
	}

	public function retrieveData($index) {
		$rows = Yii::app()->db->createCommand()->select("*")
            ->from("opr_stickies")->where("id=:id",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->content = $row['content'];
                break;
			}
		}
		return true;
	}

    //獲取標籤列表
    public function getStickiesList(){
	    $arr = array(""=>"");
        $rs = Yii::app()->db->createCommand()->select()->from("opr_stickies")->order('index desc')->queryAll();
        if($rs){
            foreach ($rs as $row){
                $arr[$row["id"]] = $row["name"];
            }
        }
        return $arr;
    }

    //獲取標籤列表
    public function getStickiesContentList(){
	    $arr = array(""=>"");
        $rs = Yii::app()->db->createCommand()->select()->from("opr_stickies")->order('index desc')->queryAll();
        if($rs){
            foreach ($rs as $row){
                $arr[$row["id"]] = $row["content"];
            }
        }
        return $arr;
    }

    //根據訂單id查標籤內容
    public function getStickiesToId($stickies_id){
        $rs = Yii::app()->db->createCommand()->select("name,content")
            ->from("opr_stickies")->where('id=:id',array(':id'=>$stickies_id))->queryAll();
        if($rs){
            return $rs[0];
        }
        return array(
            "name"=>"",
            "content"=>""
        );
    }

    //刪除驗證
    public function deleteValidate(){
        $rs = Yii::app()->db->createCommand()->select()->from("opr_goods_do")->where('stickies_id=:stickies_id',array(':stickies_id'=>$this->id))->queryAll();
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
                $sql = "delete from opr_stickies where id = :id";
                break;
            case 'new':
                $sql = "insert into opr_stickies(
							name, content, lcu, lcd
						) values (
							:name, :content, :lcu, :lcd
						)";
                break;
            case 'edit':
                $sql = "update opr_stickies set
							name = :name, 
							content = :content, 
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
        if (strpos($sql,':content')!==false)
            $command->bindParam(':content',$this->content,PDO::PARAM_STR);

        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$city,PDO::PARAM_STR);
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
