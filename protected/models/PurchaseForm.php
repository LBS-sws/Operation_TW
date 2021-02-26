<?php

class PurchaseForm extends CFormModel
{
	public $id;
	public $order_user;
	//public $technician;
    public $status;
    public $remark;
	public $luu;
	public $lcu;
	public $statusList;
	public $order_code;
	public $order_class;
	public $activity_id;
	public $goods_list;
    public $ject_remark;
    public $notice;

	public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub

        $this->status = "pending";
        $this->goods_list = array(
            array(
                "id"=>"",
                "goods_id"=>"",
                "classify_id"=>"",
                "stickies_id"=>"",
                "note"=>"",
                "name"=>"",
                "type"=>"",
                "unit"=>"",
                "price"=>"",
                "goods_num"=>"",
                "confirm_num"=>"",
                "remark"=>"",
            )
        );
    }

    public function attributeLabels()
	{
		return array(
            'order_code'=>Yii::t('procurement','Order Code'),
            'order_class'=>Yii::t('procurement','Order Class'),
            'activity_id'=>Yii::t('procurement','Order of Activity'),
            'goods_list'=>Yii::t('procurement','Goods List'),
            'order_user'=>Yii::t('procurement','Order User'),
            //'technician'=>Yii::t('procurement','Technician'),
            'status'=>Yii::t('procurement','Order Status'),
            'remark'=>Yii::t('procurement','Remark'),
            'ject_remark'=>Yii::t('procurement','reject remark'),
            'notice'=>Yii::t('procurement','Notice text'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, order_code, order_user, order_class, activity_id, technician, status, remark, ject_remark, luu, lcu, lud, lcd, goods_list, notice','safe'),
            array('goods_list','required','on'=>array('audit','edit','reject')),
            array('goods_list','validateGoods','on'=>array('audit','edit')),
            array('ject_remark','required','on'=>'reject'),
            array('notice','required','on'=>'notice'),
            //array('order_num','numerical','allowEmpty'=>true,'integerOnly'=>true),
            //array('order_num','in','range'=>range(0,600)),
		);
	}

	//驗證訂單內的物品
    public function validateGoods($attribute, $params){
	    $goods_list = $this->goods_list;
        if(count($this->goods_list)<1){
            $message = Yii::t('procurement','Fill in at least one goods');
            $this->addError($attribute,$message);
            return false;
        }
        $validateList = array();
        foreach ($goods_list as $key =>$goods){
            if(empty($goods["goods_id"]) && empty($goods["confirm_num"])){
                unset($this->goods_list[$key]);
            }else if ($goods["confirm_num"] === ""){
                $message = Yii::t('procurement','Actual Number cannot be empty');
                $this->addError($attribute,$message);
                return false;
            }else if(!is_numeric($goods["confirm_num"]) || floor($goods["confirm_num"])!=$goods["confirm_num"]){
                $message = Yii::t('procurement','Actual Number can only be numbered');
                $this->addError($attribute,$message);
                return false;
            }else if ($goods["confirm_num"] != 0){
                $list = OrderForm::getOneGoodsToId($goods["goods_id"],$this->order_class);
                if (empty($list)){
                    $message = Yii::t('procurement','Not Font Goods').$goods["name"];
                    $this->addError($attribute,$message);
                    return false;
                }else{
                    if(empty($list["rules_id"])){
                        //常規驗證
                        if (intval($goods["confirm_num"])%intval($list["multiple"]) != 0){
                            $message = $list["name"]." ".Yii::t('procurement','Multiple is').$list["multiple"];
                            $this->addError($attribute,$message);
                            return false;
                        }elseif (intval($list["big_num"])<intval($goods["confirm_num"])){
                            $message = $list["name"]." ".Yii::t('procurement','Max Number is').$list["big_num"];
                            $this->addError($attribute,$message);
                            return false;
                        }elseif (intval($list["small_num"])>intval($goods["confirm_num"])){
                            $message = $list["name"]." ".Yii::t('procurement','Min Number is').$list["small_num"];
                            $this->addError($attribute,$message);
                            return false;
                        }
                    }else{
                        //混合驗證
                        if(empty($validateList[$list["rules_id"]])){
                            $rules = RulesForm::getRulesToId($list["rules_id"]);
                            $validateList[$list["rules_id"]] = array(
                                "rulesName"=>$rules["name"],
                                "rulesMultiple"=>$rules["multiple"],
                                "rulesMin"=>$rules["min"],
                                "rulesMax"=>$rules["max"],
                                "goodsNum"=>0,
                            );
                        }
                        $validateList[$list["rules_id"]]["goodsNum"]+=intval($goods["confirm_num"]);
                    }
                }
            }
        }
        foreach ($validateList as $hybrid){
            if (intval($hybrid["goodsNum"])%intval($hybrid["rulesMultiple"]) != 0){
                $message = $hybrid["rulesName"]." ".Yii::t('procurement','Multiple is').$hybrid["rulesMultiple"];
                $this->addError($attribute,$message);
                return false;
            }elseif (intval($hybrid["rulesMax"])<intval($hybrid["goodsNum"])){
                $message = $hybrid["rulesName"]." ".Yii::t('procurement','Max Number is').$hybrid["rulesMax"];
                $this->addError($attribute,$message);
                return false;
            }elseif (intval($hybrid["rulesMin"])>intval($hybrid["goodsNum"])){
                $message = $hybrid["rulesName"]." ".Yii::t('procurement','Min Number is').$hybrid["rulesMin"];
                $this->addError($attribute,$message);
                return false;
            }
        }
        if(count($this->goods_list)<1){
            $message = Yii::t('procurement','Fill in at least one goods');
            $this->addError($attribute,$message);
        }
    }


	public function retrieveData($index) {
		$city = Yii::app()->user->city();
		$rows = Yii::app()->db->createCommand()->select("*")
            ->from("opr_order")->where("id=:id AND status_type=1 AND judge=1",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->order_code = $row['order_code'];
                $this->order_class = $row['order_class'];
                $this->activity_id = $row['activity_id'];
                $this->lcu = $row['lcu'];
                $this->goods_list = OrderForm::getGoodsListToId($row['id']);
                $this->order_user = $row['order_user'];
                //$this->technician = $row['technician'];
                $this->status = $row['status'];
                $this->ject_remark = $row['ject_remark'];
                $this->remark = "";
                $this->statusList = OrderForm::getStatusListToId($row['id']);
                break;
			}
		}
		return true;
	}

	public function validateOrderId($index) {
		$rows = Yii::app()->db->createCommand()->select("*")
            ->from("opr_order")->where("id=:id AND judge=1",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->order_code = $row['order_code'];
                $this->order_class = $row['order_class'];
                $this->activity_id = $row['activity_id'];
                $this->lcu = $row['lcu'];
                $this->goods_list = OrderForm::getGoodsListToId($row['id']);
                $this->order_user = $row['order_user'];
                //$this->technician = $row['technician'];
                $this->status = $row['status'];
                $this->ject_remark = $row['ject_remark'];
                $this->remark = "";
                $this->statusList = OrderForm::getStatusListToId($row['id']);
                return false;
			}
		}
		return true;
	}
    //整理出下載的物品列表
    public function resetGoodsList(){
        $goodsList = $this->goods_list;
        foreach ($goodsList as $key=>$goods){
            $multiple = $goodsList[$key]["multiple"];
            $multiple = intval($multiple);
            unset($goodsList[$key]["id"]);
            unset($goodsList[$key]["goods_id"]);
            unset($goodsList[$key]["classify_id"]);
            switch ($this->order_class){
                case "Domestic":
                    $goodsList[$key]["stickies_id"] = $this->getStickiesToId($goodsList[$key]["stickies_id"]);
                    $goodsList[$key]["total"] = sprintf("%.2f",floatval($goodsList[$key]["price"])*intval($goodsList[$key]["confirm_num"]));
                    break;
                case "Import":
                    unset($goodsList[$key]["stickies_id"]);
                    $goodsList[$key]["total"] = sprintf("%.2f",floatval($goodsList[$key]["price"])*intval($goodsList[$key]["confirm_num"]));
                    $sum = floatval($goodsList[$key]["net_weight"])*intval($goodsList[$key]["confirm_num"])/$multiple;
                    $goodsList[$key]["total2"] = round($sum,2);
                    $sum = floatval($goodsList[$key]["gross_weight"])*intval($goodsList[$key]["confirm_num"])/$multiple;
                    $goodsList[$key]["total3"] = round($sum,2);
                    $volume = explode('×',$goodsList[$key]["LWH"]);
                    $volume = floatval($volume[0])*floatval($volume[1])*floatval($volume[2])/1000000;
                    $sum = $volume*intval($goodsList[$key]["confirm_num"])/$multiple;
                    $goodsList[$key]["total4"] = round($sum,2);
                    break;
                case "Fast":
                    unset($goodsList[$key]["stickies_id"]);
                    $goodsList[$key]["total"] = sprintf("%.2f",floatval($goodsList[$key]["price"])*intval($goodsList[$key]["confirm_num"]));
                    break;
            }
            unset($goodsList[$key]["multiple"]);
        }
        return $goodsList;
    }
    //獲取標籤內容
    public function getStickiesToId($index) {
        $rows = Yii::app()->db->createCommand()->select("content")
            ->from("opr_stickies")->where("id=:id",array(":id"=>$index))->queryAll();
        if ($rows) {
            return $rows[0]['content'];
        }
        return "";
    }

    //獲取表格的头
    public function getTableHeard(){
        $arr = array("物品編號","物品名称","物品規格","物品單位");
        switch ($this->order_class){
            case "Import":
                array_push($arr,'物品單價（US$）');
                array_push($arr,"净重（kg）");
                array_push($arr,"毛重（kg）");
                array_push($arr,"長×寬×高（cm）");
                array_push($arr,"要求數量");
                array_push($arr,"實際數量");
                array_push($arr,"要求備註");
                array_push($arr,"總部備註");
                array_push($arr,'總價（US$）');
                array_push($arr,'總淨重（kg）');
                array_push($arr,'總毛重（kg）');
                array_push($arr,'總體積（m³）');
                break;
            case "Domestic":
                array_push($arr,"物品單價（RMB）");
                array_push($arr,"物品標籤");
                array_push($arr,"要求數量");
                array_push($arr,"實際數量");
                array_push($arr,"要求備註");
                array_push($arr,"總部備註");
                array_push($arr,"總價（RMB）");
                break;
            case "Fast":
                array_push($arr,'物品單價（US$）');
                array_push($arr,"要求數量");
                array_push($arr,"實際數量");
                array_push($arr,"要求備註");
                array_push($arr,"總部備註");
                array_push($arr,'總價（US$）');
                break;

        }
        return $arr;
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
        $oldOrderStatus = Yii::app()->db->createCommand()->select()->from("opr_order")
            ->where("id=:id",array(":id"=>$this->id))->queryAll();
		$sql = '';
        switch ($this->scenario) {
            case 'edit':
                $sql = "update opr_order set
							remark = :remark,
							luu = :luu,
							lud = :lud,
							status = :status
						where id = :id AND judge=1 AND status_type=1
						";
                $this->status = "read";
                break;
            case 'audit':
                $sql = "update opr_order set
							remark = :remark,
							luu = :luu,
							lud = :lud,
							status = :status
						where id = :id AND judge=1 AND status_type=1
						";
                $this->status = "approve";
                break;
            case 'reject':
                $sql = "update opr_order set
							ject_remark = :ject_remark,
							luu = :luu,
							lud = :lud,
							status = :status
						where id = :id AND judge=1 AND status_type=1
						";
                $this->goods_list = array();
                $this->status = "reject";
                $this->remark = $this->ject_remark;
                break;
        }
		if (empty($sql)) return false;

        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;
        $order_username = Yii::app()->user->name;
        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        if (strpos($sql,':status')!==false){
            $command->bindParam(':status',$this->status,PDO::PARAM_STR);
        }

        if (strpos($sql,':remark')!==false)
            $command->bindParam(':remark',$this->remark,PDO::PARAM_STR);
        if (strpos($sql,':ject_remark')!==false)
            $command->bindParam(':ject_remark',$this->ject_remark,PDO::PARAM_STR);
        if (strpos($sql,':lud')!==false)
            $command->bindParam(':lud',date('Y-m-d H:i:s'),PDO::PARAM_STR);
        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        $command->execute();

        Yii::app()->db->createCommand()->insert('opr_order_status', array(
            'order_id'=>$this->id,
            'status'=>$this->status == "approve"?"head approve":$this->status,
            'r_remark'=>$this->remark,
            'lcu'=>Yii::app()->user->user_display_name(),
            'time'=>date('Y-m-d H:i:s'),
        ));

        //物品的添加、修改
        foreach ($this->goods_list as $goods){
            if(!empty($goods["id"])){
                //修改
                Yii::app()->db->createCommand()->update('opr_order_goods', array(
                    'confirm_num'=>$goods["confirm_num"],
                    'remark'=>$goods["remark"],
                    'luu'=>$uid,
                    'lud'=>date('Y-m-d H:i:s'),
                ), 'id=:id', array(':id'=>$goods["id"]));
            }
        }


        //發送郵件
        OrderGoods::sendEmail($oldOrderStatus,$this->status,$this->order_code,$this->activity_id);
		return true;
	}
	//退回
	public function backward(){
        $rows = Yii::app()->db->createCommand()->select("id")->from("opr_order")->where('status = "approve" and id = :id',array(':id'=>$this->id))->queryAll();
        if(count($rows) > 0){
            $uid = Yii::app()->user->id;
            $this->status = "sent";
            Yii::app()->db->createCommand()->update('opr_order', array(
                'status'=>$this->status,
                'remark'=>$this->remark,
                'luu'=>$uid,
                'lud'=>date('Y-m-d H:i:s'),
            ), 'id=:id', array(':id'=>$this->id));

            Yii::app()->db->createCommand()->insert('opr_order_status', array(
                'order_id'=>$this->id,
                'status'=>"backward",
                'r_remark'=>$this->remark,
                'lcu'=>Yii::app()->user->user_display_name(),
                'time'=>date('Y-m-d H:i:s'),
            ));
            return true;
        }
        return false;
    }

    //獲取規則條款
    public function getRulesText(){
	    $arr = array();
        switch ($this->order_class){
            case "Import":
                array_push($arr,"注意事項：");
                array_push($arr,"甲：	特許經營商知悉因特許經營商並無責任必須定期購貨，故此供應商不可能每天備存大量存貨等待不一定出現的訂單，因而需要時間備貨。亦知悉自己須保持合理庫存及定期盤點存貨的重要性，以便及早發現存貨不足，提早訂貨");
                array_push($arr,"乙：	備貨貨期：	由嘉富貨倉提取之貨物	 7天（由收到訂貨申請表後計）。只能以自提或速遞送貨，運費昂貴，亦沒有發票	需報關上貨之貨物 	 25天到達深圳（收到訂貨申請表後計，含報關及運貨時間），未包括由深圳到目的地之運貨時間。國內交貨之貨物	 14天（收到貨款後計），未包運貨時間。");
                array_push($arr,"丙：	貨品來源自美國或/台灣或/馬來西亞，單價含來源地至香港運費。");
                array_push($arr,"丁：	不論來源地，單價為嘉富貨倉提取價（不包括嘉富到目的地運費）、或國內出廠價（不包括國內運費）。");
                break;
            case "Domestic":
                array_push($arr,"注意事項：");
                array_push($arr,"訂貨週期：每月一次");
                array_push($arr,"訂貨時間：每月20號 (請於20號前向總公司提供訂貨單，如遇假日將順延一個工作天)");
                break;
            case "Fast":
                array_push($arr,"注意事項：");
                array_push($arr,"甲：	特許經營商知悉因特許經營商並無責任必須定期購貨，故此供應商不可能每天備存大量存貨等待不一定出現的訂單，因而需要時間備貨。亦知悉自己須保持合理庫存及定期盤點存貨的重要性，以便及早發現存貨不足，提早訂貨");
                array_push($arr,"乙：	備貨貨期：	由嘉富貨倉提取之貨物	 7天（由收到訂貨申請表後計）。只能以自提或速遞送貨，運費昂貴，亦沒有發票							
		需報關上貨之貨物 	 20天到達深圳（收到訂貨申請表後計，含報關及運貨時間），未包括由深圳到目的地之運貨時間。							
		國內交貨之貨物	 14天（收到貨款後計），未包運貨時間。");
                array_push($arr,"丙：	貨品來源自美國或/台灣或/馬來西亞，單價含來源地至香港運費。");
                array_push($arr,"丁：	不論來源地，單價為嘉富貨倉提取價（不包括嘉富到目的地運費）、或國內出廠價（不包括國內運費）。");
                break;
        }
        return $arr;
    }



    //管理員操作通知
    function notice(){
        $uid = Yii::app()->user->id;
        $suffix = Yii::app()->params['envSuffix'];
        $systemId = Yii::app()->params['systemId'];
        $from_addr = Yii::app()->params['adminEmail'];
        $to_addr = array();//
		$to_user = array(); //因通知記錄需要
        $rs = Yii::app()->db->createCommand()->select("b.email,b.city,b.username")->from("opr_order a")
            ->leftJoin("security$suffix.sec_user b","b.username=a.lcu")
            ->where("a.id=".$this->id)
            ->queryRow();;
        if($rs){
            $city = $rs["city"];
            if(!in_array($rs["email"],$to_addr)){
                array_push($to_addr,$rs["email"]);
            }
            if(!in_array($rs["username"],$to_user)){	//因通知記錄需要
                array_push($to_user,$rs["username"]);
            }
        }else{
            return false;
        }
        $rs = Yii::app()->db->createCommand()->select("b.email,b.username")->from("security$suffix.sec_user_access a")
            ->leftJoin("security$suffix.sec_user b","b.username=a.username")
            ->where("a.system_id='$systemId' and a.a_read_write like '%YD06%' and b.status ='A' and b.city='$city'")
            ->queryAll();
        if($rs){
            foreach ($rs as $row){
                array_push($to_addr,$row["email"]);
				if(!in_array($row["username"],$to_user)){	//因通知記錄需要
					array_push($to_user,$rs["username"]);
				}
            }
        }else{
            return false;
        }
        $to_addr = json_encode($to_addr);
        $message = "<p>订单编号：".$this->order_code."</p>";
        $message.="<p>通知原因：".$this->notice."</p>";
        Yii::app()->db->createCommand()->insert("swoper$suffix.swo_email_queue", array(
            'request_dt'=>date('Y-m-d H:i:s'),
            'from_addr'=>$from_addr,
            'to_addr'=>$to_addr,
            'subject'=>"订单通知，订单编号：".$this->order_code,//郵件主題
            'description'=>"订单通知，订单编号：".$this->order_code,//郵件副題
            'message'=>$message,//郵件內容（html）
            'status'=>"P",
            'lcu'=>$uid,
            'lcd'=>date('Y-m-d H:i:s'),
        ));
		
		//新增通知記錄
		$connection = Yii::app()->db;
		SystemNotice::addNotice($connection, array(
				'note_type'=>'notice',
				'subject'=>"订单通知，订单编号：".$this->order_code,//郵件主題
				'description'=>"订单通知，订单编号：".$this->order_code,//郵件副題
				'message'=>$message,
				'username'=>json_encode($to_user),
				'system_id'=>Yii::app()->user->system(),
				'form_id'=>'PurchaseForm',
				'rec_id'=>$this->id,
			)
		);

    }
}
