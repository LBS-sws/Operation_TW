<?php

class EmailForm extends CFormModel
{
	public $id;
	public $name;
	public $email;

	public function attributeLabels()
	{
		return array(
            'name'=>Yii::t('procurement','Name'),
            'email'=>Yii::t('procurement','Email'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, name,email','safe'),
            array('name','required'),
            array('email','required'),
            array('email', 'email'),
			array('name','validateName'),
			array('email','validateEmail'),
            //, 'message'=>'必须为电子邮箱', 'pattern'=>'/[a-z]/i'
		);
	}

	public function validateName($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("opr_email")
            ->where('name=:name and id!=:id', array(':name'=>$this->name,':id'=>$id))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('procurement','the name of already exists');
            $this->addError($attribute,$message);
        }
	}

	public function validateEmail($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("opr_email")
            ->where('email=:email and id!=:id', array(':email'=>$this->email,':id'=>$id))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('procurement','the email of already exists');
            $this->addError($attribute,$message);
        }
	}

	public function retrieveData($index) {
		$rows = Yii::app()->db->createCommand()->select("*")
            ->from("opr_email")->where("id=:id",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->email = $row['email'];
                break;
			}
		}
		return true;
	}

    //獲取郵箱列表
    public function getEmailList(){
	    $arr = array();
        $rs = Yii::app()->db->createCommand()->select()->from("opr_email")->queryAll();
        if($rs){
            foreach ($rs as $row){
                array_push($arr,$row["email"]);
            }
        }
        return $arr;
    }

    public function getUserList(){
	    $arr = array();
        $rs = Yii::app()->db->createCommand()->select()->from("opr_email")->queryAll();
        if($rs){
            foreach ($rs as $row){
                array_push($arr,$row["name"]);
            }
        }
        return $arr;
    }

    //獲取郵箱列表(總管)
    public function getCityEmailList(){
        $suffix = Yii::app()->params['envSuffix'];
        $systemId = Yii::app()->params['systemId'];
        $city = Yii::app()->user->city();
	    $arr = array();
        $rs = Yii::app()->db->createCommand()->select("username")->from("security$suffix.sec_user_access")
            ->where("system_id='$systemId' and a_read_write like '%YD06%'")
            ->queryAll();
        if($rs){
            foreach ($rs as $row){
                $email = Yii::app()->db->createCommand()->select("email")->from("security$suffix.sec_user")
                    ->where("username=:username and city=:city and status='A'",array(":username"=>$row["username"],":city"=>$city))
                    ->queryRow();
                if($email){
                    array_push($arr,$email["email"]);
                }
            }
        }
        return $arr;
    }

    public function getCityUserList(){
        $suffix = Yii::app()->params['envSuffix'];
        $systemId = Yii::app()->params['systemId'];
        $city = Yii::app()->user->city();
	    $arr = array();
        $rs = Yii::app()->db->createCommand()->select("username")->from("security$suffix.sec_user_access")
            ->where("system_id='$systemId' and a_read_write like '%YD06%'")
            ->queryAll();
        if($rs){
            foreach ($rs as $row){
				$arr[] = $row['username'];
            }
        }
        return $arr;
    }

    //刪除驗證
    public function deleteValidate(){
        $rs0 = Yii::app()->db->createCommand()->select()->from("opr_email")->queryAll();
        if(count($rs0)<2){
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
                $sql = "delete from opr_email where id = :id";
                break;
            case 'new':
                $sql = "insert into opr_email(
							name,email, lcu, lcd
						) values (
							:name,:email, :lcu, :lcd
						)";
                break;
            case 'edit':
                $sql = "update opr_email set
							name = :name, 
							email = :email, 
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
        if (strpos($sql,':email')!==false)
            $command->bindParam(':email',$this->email,PDO::PARAM_INT);

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
