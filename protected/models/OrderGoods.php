<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/16 0016
 * Time: 上午 11:26
 */
class OrderGoods extends CActiveRecord{

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    public function tableName()
    {
        return 'opr_order_goods';
    }
    public function primaryKey()
    {
        return 'id';
    }

    public function getArrGoodsClass(){
        return array(
            ""=>"",
            "Import"=>Yii::t("procurement","Import"),      //進口貨
            "Domestic"=>Yii::t("procurement","Domestic"), //國內貨
            "Fast"=>Yii::t("procurement","Fast")           //快速貨
        );
    }
    //訂單狀態發生改變時發送郵件
    public function formEmail($subject,$message,$to_addr = 0,$description=""){
        $uid = Yii::app()->user->id;
        $from_addr = Yii::app()->params['adminEmail'];
		$to_user = array();	//因通知記錄需要
        if(empty($to_addr)){
            //發給地區總管
            $to_addr = EmailForm::getCityEmailList();
            $to_user = EmailForm::getCityUserList();	//因通知記錄需要
        }elseif($to_addr == 1){
            //發給總部
            $to_addr = EmailForm::getEmailList();
            $to_user = OrderGoods::getUsernameToEmail($to_addr);	//因通知記錄需要
        }elseif($to_addr === "aaa"){
            //用戶郵箱為空不發送郵件
            return false;
        }else{
            //發給地區
            if(!is_array($to_addr)){
                $to_addr = array($to_addr);
            }
            $to_user = OrderGoods::getUsernameToEmail($to_addr);	//因通知記錄需要
        }
        $to_addr = empty($to_addr)?json_encode(array("it@lbsgroup.com.hk")):json_encode($to_addr);
        $description = empty($description)?"訂單通知":$description;
		$suffix = Yii::app()->params['envSuffix'];
        $aaa = Yii::app()->db->createCommand()->insert("swoper$suffix.swo_email_queue", array(
            'request_dt'=>date('Y-m-d H:i:s'),
            'from_addr'=>$from_addr,
            'to_addr'=>$to_addr,
            'subject'=>$subject,//郵件主題
            'description'=>$description,//郵件副題
            'message'=>$message,//郵件內容（html）
            'status'=>"P",
            'lcu'=>$uid,
            'lcd'=>date('Y-m-d H:i:s'),
        ));
		
		//新增通知記錄
		$connection = Yii::app()->db;
		SystemNotice::addNotice($connection, array(
				'note_type'=>'notice',
				'subject'=>$subject,//郵件主題
				'description'=>$description,//郵件副題
				'message'=>$message,
				'username'=>json_encode($to_user),
				'system_id'=>Yii::app()->user->system(),
				'form_id'=>'OrderGoods',
				'rec_id'=>0,
			)
		);
    }

    //獲取用戶暱稱
    public function getNameToUsername($username){
        $suffix = Yii::app()->params['envSuffix'];
        $rs = Yii::app()->db->createCommand()->select("disp_name")->from("security$suffix.sec_user")
            ->where("username=:username",array(":username"=>$username))->queryRow();
        if($rs){
            return $rs["disp_name"];
        }
        return $username;
    }

    public function getUsernameToEmail($emaillist){
		$rtn = array();
        $suffix = Yii::app()->params['envSuffix'];
		foreach ($emaillist as $email){
			if (!empty($email)) {
				$rs = Yii::app()->db->createCommand()->select("username")->from("security$suffix.sec_user")
					->where("email=:email",array(":email"=>$email))->queryAll();
				if($rs){
					foreach ($rs as $row) {
						$rtn[] = $row['username'];
					}
				}
			}
        }
        return $rtn;
    }

    //訂單發郵件(總部)
    public function sendEmail($oldOrderStatus,$stauts,$order_code=0,$activity_id = 0){
        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->user_display_name();
        $activityList = new ActivityForm();
        if(empty($oldOrderStatus[0]["activity_id"])&&empty($activity_id)){
            $activityList->activity_code = "快速訂單";
            $activityList->activity_title = "快速訂單";
        }else{
            $activity_id = empty($activity_id)?$oldOrderStatus[0]["activity_id"]:$activity_id;
            if(!$activityList->retrieveData($activity_id)){
                return false;
            }
        }
        $html = "<p>採購編號：".$activityList->activity_code."</p>";
        $html .= "<p>採購標題：".$activityList->activity_title."</p>";
        //發送郵件
        if($oldOrderStatus){
            if(empty($oldOrderStatus[0]["lcu_email"])){
                $oldOrderStatus[0]["lcu_email"] = "aaa";//後期bug修改，不想重構
            }
            if($oldOrderStatus[0]["status"] != $stauts){
                $html .= "<p>下單城市：".$oldOrderStatus[0]["city"]."</p>";
                $html .= "<p>下單用戶：".OrderGoods::getNameToUsername($oldOrderStatus[0]["lcu"])."</p>";
                $html .= "<p>下單時間：".$oldOrderStatus[0]["lcd"]."</p>";
                $html .= "<p>訂單編號：".$oldOrderStatus[0]["order_code"]."</p>";
                if($stauts == "sent"){
                    OrderGoods::formEmail("營運系統：要求審核訂單（訂單編號：".$oldOrderStatus[0]["order_code"]."）",$html,$oldOrderStatus[0]["status_type"]);
                }elseif ($stauts == "finished"){ //收貨
                    OrderGoods::formEmail("營運系統：訂單已完成，地區已收貨（訂單編號：".$oldOrderStatus[0]["order_code"]."）",$html,$oldOrderStatus[0]["status_type"]);
                }elseif ($stauts == "approve"){ //批准
                    OrderGoods::formEmail("營運系統：總部已發貨，等待收貨（訂單編號：".$oldOrderStatus[0]["order_code"]."）",$html,$oldOrderStatus[0]["lcu_email"]);
                }elseif ($stauts == "reject"){  //拒絕
                    OrderGoods::formEmail("營運系統：訂單已拒絕，請查看詳情（訂單編號：".$oldOrderStatus[0]["order_code"]."）",$html,$oldOrderStatus[0]["lcu_email"]);
                }elseif ($stauts == "read"){  //查看
                    OrderGoods::formEmail("營運系統：訂單已查看，等待總部發貨（訂單編號：".$oldOrderStatus[0]["order_code"]."）",$html,$oldOrderStatus[0]["lcu_email"]);
                }
            }
        }else{
            if($stauts == "sent"){
                $html .= "<p>下單城市：".$city."</p>";
                $html .= "<p>下單用戶：".$uid."</p>";
                $html .= "<p>下單時間：".date('Y-m-d H:i:s')."</p>";
                $html .= "<p>訂單編號：".$order_code."</p>";
                OrderGoods::formEmail("營運系統：要求審核訂單（訂單編號：".$order_code."）",$html,0);
            }
        }
    }
    //訂單發郵件(倉庫)
    public function sendEmailTwo($oldOrderStatus,$stauts,$order_code=0){
        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->user_display_name();
        $email = ActivityForm::getEmailToCity($city);
        if(empty($email)){
            return false;
        }
        $html = "";
        //發送郵件
        if($oldOrderStatus){
            if(empty($oldOrderStatus[0]["lcu_email"])){
                $oldOrderStatus[0]["lcu_email"] = "aaa";//後期bug修改，不想重構
            }
            if($oldOrderStatus[0]["status"] != $stauts){
                $html .= "<p>下單城市：".$oldOrderStatus[0]["city"]."</p>";
                $html .= "<p>下單用戶：".OrderGoods::getNameToUsername($oldOrderStatus[0]["lcu"])."</p>";
                $html .= "<p>下單時間：".$oldOrderStatus[0]["lcd"]."</p>";
                $html .= "<p>訂單編號：".$oldOrderStatus[0]["order_code"]."</p>";
                if($stauts == "sent"){
                    OrderGoods::formEmail("營運系統：要求倉庫發貨（訂單編號：".$oldOrderStatus[0]["order_code"]."）",$html,$email);
                }elseif ($stauts == "finished"){ //收貨
                    OrderGoods::formEmail("營運系統：倉庫已發貨，領料員已收貨（訂單編號：".$oldOrderStatus[0]["order_code"]."）",$html,$email);
                }elseif ($stauts == "approve"){ //批准
                    OrderGoods::formEmail("營運系統：倉庫已發貨，等待領料員收貨（訂單編號：".$oldOrderStatus[0]["order_code"]."）",$html,$oldOrderStatus[0]["lcu_email"]);
                }elseif ($stauts == "reject"){  //拒絕
                    OrderGoods::formEmail("營運系統：訂單已拒絕，請查看詳情（訂單編號：".$oldOrderStatus[0]["order_code"]."）",$html,$oldOrderStatus[0]["lcu_email"]);
                }elseif ($stauts == "read"){  //查看
                    OrderGoods::formEmail("營運系統：訂單已查看，等待倉庫發貨（訂單編號：".$oldOrderStatus[0]["order_code"]."）",$html,$oldOrderStatus[0]["lcu_email"]);
                }
            }
        }else{
            if($stauts == "sent"){
                $html .= "<p>下單城市：".$city."</p>";
                $html .= "<p>下單用戶：".$uid."</p>";
                $html .= "<p>下單時間：".date('Y-m-d H:i:s')."</p>";
                $html .= "<p>訂單編號：".$order_code."</p>";
                OrderGoods::formEmail("營運系統：要求倉庫發貨（訂單編號：".$order_code."）",$html,$email);
            }
        }
    }
}