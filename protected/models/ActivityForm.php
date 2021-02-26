<?php

class ActivityForm extends CFormModel
{
	public $id;
	public $activity_code;
	public $activity_title;
	public $start_time;
	public $end_time;
	public $order_class;
	public $num=3;
    public $city_auth;
    public $city_name="全部";
    public $luu;
    public $lcu;
    public $lud;
    public $lcd;

    public function init(){
        $this->start_time = date("Y/m/d");
    }

	public function attributeLabels()
	{
		return array(
            'activity_code'=>Yii::t('procurement','Activity Code'),
            'activity_title'=>Yii::t('procurement','Activity Title'),
            'start_time'=>Yii::t('procurement','Start Time'),
            'end_time'=>Yii::t('procurement','End Time'),
            'num'=>Yii::t('procurement','Max Number Restrictions'),
            'order_class'=>Yii::t('procurement','Order Class'),
            'city_auth'=>Yii::t('user','City')
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, start_time, end_time, order_class, num, city_name, city_auth','safe'),
            array('start_time','required'),
            array('end_time','required'),
            array('end_time','validateDate'),
            array('city_auth','validateCity'),
            array('order_class','required',"on"=>"new"),
            array('num','required'),
            array('num','numerical','allowEmpty'=>false,'integerOnly'=>true,'min'=>1),
		);
	}

    public function validateCity($attribute, $params){
	    if(!empty($this->city_auth)){
            $this->city_auth ="~".$this->city_auth."~";
        }
    }
    public function validateDate($attribute, $params){
        if(strtotime($this->start_time)>strtotime($this->end_time)){
            $message = Yii::t('procurement','The end time cannot be less than the start time');
            $this->addError($attribute,$message);
        }
    }

	//刪除快速訂單
    public function getOrderClassNotFast(){
	    $arr = OrderGoods::getArrGoodsClass();
	    unset($arr["Fast"]);
	    return $arr;
    }

	public function retrieveData($index) {
		$city = Yii::app()->user->city();
        $row = Yii::app()->db->createCommand()->select("*")
            ->from("opr_order_activity")->where("id=:id",array(":id"=>$index))->queryRow();
		if ($row) {
            $this->id = $row['id'];
            $this->activity_code = $row['activity_code'];
            $this->activity_title = $row['activity_title'];
            $this->start_time = $row['start_time'];
            $this->end_time = $row['end_time'];
            $this->order_class = $row['order_class'];
            $this->num = $row['num'];
            $this->city_auth = empty($row['city_auth'])?"":substr($row['city_auth'],1,-1);
            $this->city_name =empty($this->city_auth)?"全部":"";
            $cityList = explode("~",$this->city_auth);
            foreach ($cityList as $code){
                $this->city_name.=CGeneral::getCityName($code)." ";
            }
		}
		return true;
	}

    //刪除驗證
    public function deleteValidate(){
        $rs = Yii::app()->db->createCommand()->select()->from("opr_order")->where('activity_id=:activity_id',array(':activity_id'=>$this->id))->queryAll();
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
			$this->saveActivity($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update. ('.$e->getMessage().')');
		}
	}

	//刪除活動下面的所有訂單及訂單內的物品
    public function deleteAllOrderToActivityId($activity_id){
        $rs = Yii::app()->db->createCommand()->select("id")->from("opr_order")->where("activity_id=:activity_id and judge=1",array(":activity_id"=>$activity_id))->queryAll();
        if($rs){
            foreach ($rs as $row){
                Yii::app()->db->createCommand()->delete('opr_order', 'id=:id', array(':id'=>$row['id']));
                Yii::app()->db->createCommand()->delete('opr_order_status', 'order_id=:order_id', array(':order_id'=>$row['id']));
                Yii::app()->db->createCommand()->delete('opr_order_goods', 'order_id=:order_id', array(':order_id'=>$row['id']));
            }
        }
    }

	//查看活動下面的所有訂單
    public function seeAllOrder(){
        //read
        $rows = Yii::app()->db->createCommand()->select("id")->from("opr_order")->where("activity_id=:activity_id and status='sent' AND status_type=1 AND judge=1",
            array(":activity_id"=>$this->id))->queryAll();
        if($rows){
            foreach ($rows as $row){
                //修改物品的實際數量
                $sql = "UPDATE opr_order_goods SET confirm_num = goods_num WHERE order_id=".$row["id"];
                Yii::app()->db->createCommand($sql)->execute();

                //記錄操作
                Yii::app()->db->createCommand()->insert('opr_order_status', array(
                    'order_id'=>$row["id"],
                    'status'=>"read",
                    'lcu'=>Yii::app()->user->user_display_name(),
                    'time'=>date('Y-m-d H:i:s'),
                ));
            }

            //修改訂單狀態
            Yii::app()->db->createCommand()->update("opr_order", array(
                'status'=>"read",
            ),"activity_id=:id and status='sent' AND status_type=1 AND judge=1",array(":id"=>$this->id));
        }
    }

    //自動生成標題和編號
    public function selfTitleAndCode(){
        $day = date("Ymd",strtotime($this->start_time));
        $codeStr = $this->order_class == "Import"?"PHJK":"PHGN";
        $titleStr = $this->order_class == "Import"?"进口货":"国内货";
        $count = Yii::app()->db->createCommand()->select("count(id)")->from("opr_order_activity")->where(array('like', 'activity_code', "%$codeStr$day%"))->queryScalar();
        $count = empty($count)?"":$count+1;
        $this->activity_code = $codeStr.$day.$count;
        $this->activity_title = $day.$titleStr."采购订单".$count;
    }

	protected function saveActivity(&$connection) {
        $this->selfTitleAndCode();
		$sql = '';
        switch ($this->scenario) {
            case 'delete':
                $sql = "delete from opr_order_activity where id = :id";
                $this->deleteAllOrderToActivityId($this->id);
                break;
            case 'new':
                $sql = "insert into opr_order_activity(
							activity_code, activity_title, start_time, end_time, order_class, num, city_auth, lcu, lcd
						) values (
							:activity_code, :activity_title, :start_time, :end_time, :order_class, :num, :city_auth, :lcu, :lcd
						)";
                break;
            case 'edit':
                $sql = "update opr_order_activity set
							start_time = :start_time,
							end_time = :end_time,
							num = :num,
							luu = :luu,
							city_auth = :city_auth,
							lud = :lud
						where id = :id
						";
                break;
        }
		if (empty($sql)) return false;

        //$city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;

        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);

        if (strpos($sql,':activity_code')!==false)
            $command->bindParam(':activity_code',$this->activity_code,PDO::PARAM_STR);
        if (strpos($sql,':activity_title')!==false)
            $command->bindParam(':activity_title',$this->activity_title,PDO::PARAM_STR);
        if (strpos($sql,':start_time')!==false)
            $command->bindParam(':start_time',$this->start_time,PDO::PARAM_STR);
        if (strpos($sql,':end_time')!==false)
            $command->bindParam(':end_time',$this->end_time,PDO::PARAM_STR);
        if (strpos($sql,':order_class')!==false)
            $command->bindParam(':order_class',$this->order_class,PDO::PARAM_STR);
        if (strpos($sql,':city_auth')!==false)
            $command->bindParam(':city_auth',$this->city_auth,PDO::PARAM_STR);
        if (strpos($sql,':num')!==false)
            $command->bindParam(':num',$this->num,PDO::PARAM_INT);

        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcu')!==false)
            $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lud')!==false)
            $command->bindParam(':lud',date('Y-m-d H:i:s'),PDO::PARAM_STR);
        if (strpos($sql,':lcd')!==false)
            $command->bindParam(':lcd',date('Y-m-d H:i:s'),PDO::PARAM_STR);
        $command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->scenario = "edit";
            $this->setEmail();
        }elseif ($this->scenario=='edit'){
            if(strtotime(date("Y-m-d"))<strtotime($this->start_time)){
                $message = "<p>總部採購編號：".$this->activity_code."</p>";
                $message .= "<p>總部採購標題：".$this->activity_title."</p>";
                $message .= "<p>總部採購類型：".Yii::t("procurement",$this->order_class)."</p>";
                $message .= "<p>總部採購開始時間：".$this->start_time."</p>";
                $message .= "<p>總部採購結束時間：".$this->end_time."</p>";
				$suffix = Yii::app()->params['envSuffix'];
                Yii::app()->db->createCommand()->update("swoper$suffix.swo_email_queue", array(
                    'request_dt'=>$this->start_time,
                    'message'=>$message,
                ),"lcu=:lcu",array(":lcu"=>$this->id));
             }
        }
		return true;
	}

	//發送郵件
	private function setEmail(){
        $authCity = explode("~",$this->city_auth);//採購單允許的城市
        $cityList = General::getCityListWithNoDescendant();//城市列表
        $userList = $this->getUserListToAddOrder();//有添加權限的用戶
		$to_user = array(); //因通知記錄需要
        foreach ($cityList as $city=>$cityName){
            if(!empty($this->city_auth)&&!in_array($city,$authCity)){
                continue;
            }
            $email = $this->getEmailToCity($city);
			$username = $this->getUserToCity($city);	//因通知記錄需要
            if(!empty($email)){
                //發送郵件
                $this->sendEmail($email);
				$to_user = $username;	//因通知記錄需要
            }
        }
        if(!empty($userList)){
            foreach ($userList as $user){
                $email = $user["email"];
				$username = $user["username"]; 	//因通知記錄需要
                if(!empty($email)){
                    //發送郵件
                    $this->sendEmail($email);
					if(!in_array($username,$to_user)){ 	//因通知記錄需要
						$to_user[] = $username;
					}
                }
            }
        }
		//新增通知記錄
		$this->setNotification($to_user);
    }

    private function sendEmail($to_addr){
        $from_addr = Yii::app()->params['adminEmail'];
        if(!empty($to_addr)){
            //發送郵件
            $message = "<p>總部採購編號：".$this->activity_code."</p>";
            $message .= "<p>總部採購標題：".$this->activity_title."</p>";
            $message .= "<p>總部採購類型：".Yii::t("procurement",$this->order_class)."</p>";
            $message .= "<p>總部採購開始時間：".$this->start_time."</p>";
            $message .= "<p>總部採購結束時間：".$this->end_time."</p>";
            $suffix = Yii::app()->params['envSuffix'];
            Yii::app()->db->createCommand()->insert("swoper$suffix.swo_email_queue", array(
                'request_dt'=>$this->start_time,
                'from_addr'=>$from_addr,
                'to_addr'=>json_encode($to_addr),
                'subject'=>"總部受理訂單:".$this->activity_title,//郵件主題
                'description'=>"總部受理訂單",//郵件副題
                'message'=>$message,//郵件內容（html）
                'status'=>"P",
                'lcu'=>$this->id,
                'lcd'=>date('Y-m-d H:i:s'),
            ));
        }
    }

	private function setNotification($to_user) {
		if (!empty($to_user)) {
            $message = "<p>總部採購編號：".$this->activity_code."</p>";
            $message .= "<p>總部採購標題：".$this->activity_title."</p>";
            $message .= "<p>總部採購類型：".Yii::t("procurement",$this->order_class)."</p>";
            $message .= "<p>總部採購開始時間：".$this->start_time."</p>";
            $message .= "<p>總部採購結束時間：".$this->end_time."</p>";
			$connection = Yii::app()->db;
			SystemNotice::addNotice($connection, array(
					'note_type'=>'notice',
					'subject'=>"總部受理訂單:".$this->activity_title,//郵件主題
					'description'=>"總部受理訂單",//郵件副題
					'message'=>$message,
					'username'=>json_encode($to_user),
					'system_id'=>Yii::app()->user->system(),
					'form_id'=>'FastForm',
					'rec_id'=>$this->id,
				)
			);
		}
	}

    //根據城市獲取地區管理員郵件
    public function getEmailToCity($city){
        $systemId = Yii::app()->params['systemId'];
        $suffix = Yii::app()->params['envSuffix'];
        $suffix = "security".$suffix;
        $arr = array();
        if (!empty($city)){
            $userList = Yii::app()->db->createCommand()->select("username")->from($suffix.".sec_user_access")
                ->where("system_id=:system_id and a_read_write like '%YD02%'",array(":system_id"=>$systemId))->queryAll();
            if($userList){
                foreach ($userList as $user){
                    $email = Yii::app()->db->createCommand()->select("email")->from($suffix.".sec_user")
                    ->where("username=:username and city='$city' and status='A'",array(":username"=>$user["username"]))->queryRow();
                    if($email){
                        array_push($arr,$email["email"]);
                    }
                }
            }
        }

        return $arr;
    }

    public function getUserToCity($city){
        $systemId = Yii::app()->params['systemId'];
        $suffix = Yii::app()->params['envSuffix'];
        $suffix = "security".$suffix;
        $arr = array();
        if (!empty($city)){
            $userList = Yii::app()->db->createCommand()->select("username")->from($suffix.".sec_user_access")
                ->where("system_id=:system_id and a_read_write like '%YD02%'",array(":system_id"=>$systemId))->queryAll();
            if($userList){
                foreach ($userList as $user){
					$arr[] = $user["username"];
                }
            }
        }

        return $arr;
    }

    //根據用戶username獲取郵箱
    public function getEmailToUsername($username){
        if(empty($username)){
            return "";
        }
        $suffix = Yii::app()->params['envSuffix'];
        $suffix = "security".$suffix.".sec_user";
        $email = Yii::app()->db->createCommand()->select("email")->from($suffix)->where("username=:username",array(":username"=>$username))->queryRow();
        if($email){
            return $email["email"];
        }else{
            return "";
        }
    }

    //獲取有添加訂單權限的用戶
    public function getUserListToAddOrder(){
        $systemId = Yii::app()->params['systemId'];
        $suffix = Yii::app()->params['envSuffix'];
        $suffix = "security".$suffix;
        if(empty($this->city_auth)){
            $sql = "";
        }else{
            $city = substr($this->city_auth,1,-1);
            $city = explode("~",$city);
            $sql = " and b.city in ('".implode("','",$city)."')";
        }
        $userList = Yii::app()->db->createCommand()->select("b.email, b.username")->from($suffix.".sec_user_access a")
            ->leftJoin($suffix.".sec_user b","a.username=b.username")
            ->where("a.system_id=:system_id and a.a_read_write like '%YD04%'$sql",array(":system_id"=>$systemId))->queryAll();
        return $userList;
    }
}
