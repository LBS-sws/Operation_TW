<?php

class DeliveryForm extends CFormModel
{
	public $id;
	public $order_user;
	//public $technician;
    public $status;
    public $remark;
	public $luu;
	public $lcu;
	public $lcd;
	public $statusList;
	public $order_code;
	public $goods_list;
	public $ject_remark;

	//單個物品退回專用
	public $confirm_num;
	public $num;
	public $black_id;
	public $goods_id;

	//批量處理的訂單
    public $orderList;
    public $checkBoxDown;

    public function attributeLabels()
	{
		return array(
            'black_id'=>Yii::t('procurement','Goods Name'),
            'num'=>Yii::t('procurement','Black Number'),
            'order_code'=>Yii::t('procurement','Order Code'),
            'goods_list'=>Yii::t('procurement','Goods List'),
            'order_user'=>Yii::t('procurement','Order User'),
            //'technician'=>Yii::t('procurement','Technician'),
            'status'=>Yii::t('procurement','Order Status'),
            'remark'=>Yii::t('procurement','Remark'),
            'lcu'=>Yii::t('procurement','Apply for user'),
            'lcd'=>Yii::t('procurement','Apply for time'),
            'ject_remark'=>Yii::t('procurement','reject remark'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id,lcd,num,black_id, order_code, order_user, order_class, activity_id, technician, status, remark, ject_remark, luu, lcu, lud, lcd, checkBoxDown','safe'),
            array('goods_list','required','on'=>array('audit','edit','reject')),
            array('goods_list','validateGoods','on'=>array('audit','edit')),
            array('id','validatePriceOverTime','on'=>array('audit','edit')),
            array('ject_remark','required','on'=>'reject'),
            array('black_id','required','on'=>'black'),
            array('num','required','on'=>'black'),
            array('num','validateNum','on'=>'black'),
            //array('order_num','numerical','allowEmpty'=>true,'integerOnly'=>true),
            //array('order_num','in','range'=>range(0,600)),
		);
	}
	public function validatePriceOverTime($attribute='', $params='')
    {
		// Percy: 解決台灣不執行此檢查的問題
		if ((isset(Yii::app()->params['checkPriceOverTime']) && Yii::app()->params['checkPriceOverTime']==false)) {
			return true;
		}
        if(date("Y-m-d")<="2020-03-20"){ //一月份以前不需要驗證
            return true;
        }
        $city = Yii::app()->user->city();
        $year = date("Y");
        $month = date("m");
        $day = date("d");
        $month = $day>20?$month-1:$month-2;
        if($month <= 0){
            $month = 12+$month;
            $year--;
        }
        $bool = Yii::app()->db->createCommand()->select("a.id")->from("opr_warehouse_price a")
            ->leftJoin("opr_warehouse b","a.warehouse_id=b.id")
            ->where('b.city = :city and a.year=:year and a.month=:month', array(':city' =>$city,':year' =>$year,':month' =>$month))->queryRow();
        if ($bool) {
            return true;
        }else{
            if(!empty($attribute)){
                $message = $year."年".$month."月价格未导入，请联系财务同事";
                $this->addError($attribute,$message);
            }
            return false;
        }
    }

	public function validateNum($attribute, $params){
        if($this->num!==""){
            $idList = Yii::app()->db->createCommand()->select("*")->from("opr_order_goods")->where('id = :id',array(':id'=>$this->black_id))->queryRow();
            if($idList){
                if(is_numeric($this->num)){
                    if(floatval($this->num)<0){
                        $message = "退回數量不能小於零";
                        $this->addError($attribute,$message);
                    }else if(floatval($idList["confirm_num"])<floatval($this->num)){
                        $message = "退回數量不能大於實際數量";
                        $this->addError($attribute,$message);
                    }
                }else{
                    $message = "退回數量只能為數字";
                    $this->addError($attribute,$message);
                }
                $this->goods_id = $idList["goods_id"];
                $this->confirm_num = $idList["confirm_num"];
            }else{
                $message = "訂單內的物品不存在";
                $this->addError($attribute,$message);
            }
        }
    }

	//驗證訂單內的物品
    public function validateGoods($attribute, $params){
	    $goods_list = $this->goods_list;
        if(count($this->goods_list)<1){
            $message = Yii::t('procurement','Fill in at least one goods');
            $this->addError($attribute,$message);
            return false;
        }
        foreach ($goods_list as $key =>$goods){
            if(empty($goods["goods_id"]) && empty($goods["confirm_num"])){
                unset($this->goods_list[$key]);
                continue;
            }else if ($goods["confirm_num"] === ""){
                $message = Yii::t('procurement','Actual Number cannot be empty');
                $this->addError($attribute,$message);
                return false;
            }else if(!is_numeric($goods["confirm_num"])){
                $message = Yii::t('procurement','Actual Number can only be numbered');
                $this->addError($attribute,$message);
                return false;
            }else if ($goods["confirm_num"] != 0){
                $list = WarehouseForm::getGoodsToGoodsId($goods["goods_id"]);
                if (empty($list)){
                    $message = Yii::t('procurement','Not Font Goods').$goods["goods_id"]."a";
                    $this->addError($attribute,$message);
                    return false;
                }elseif (intval($list["inventory"])<intval($goods["confirm_num"])){
                    $message = $list["name"]."：".Yii::t('procurement','Cannot exceed the quantity of Inventory')."（".$list["inventory"]."）";
                    $this->addError($attribute,$message);
                    return false;
                }
            }
        }
        if(count($this->goods_list)<1){
            $message = Yii::t('procurement','Fill in at least one goods');
            $this->addError($attribute,$message);
        }
    }

	public function retrieveData($index) {
		$city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
		$rows = Yii::app()->db->createCommand()->select("*")
            ->from("opr_order")->where("id=:id AND judge=0 AND city in ($city_allow)",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->order_code = $row['order_code'];
                $this->goods_list = WarehouseForm::getGoodsListToId($row['id']);
                $this->order_user = $row['order_user'];
                //$this->technician = $row['technician'];
                $this->status = $row['status'];
                $this->remark = $row['remark'];
                $this->lcu = OrderGoods::getNameToUsername($row['lcu']);
                $this->ject_remark = $row['ject_remark'];
                $this->lcd = date("Y-m-d",strtotime($row['lcd']));
                $this->statusList = OrderForm::getStatusListToId($row['id']);
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
        $oldOrderStatus = Yii::app()->db->createCommand()->select()->from("opr_order")
            ->where("id=:id",array(":id"=>$this->id))->queryAll();
		$sql = '';
        switch ($this->scenario) {
            case 'edit':
                $sql = "update opr_order set
							remark = :remark,
							luu = :luu,
							status = :status
						where id = :id AND judge=0
						";
                break;
            case 'audit':
                $sql = "update opr_order set
							remark = :remark,
							ject_remark = '',
							luu = :luu,
							audit_time = :audit_time,
							status = :status
						where id = :id AND judge=0
						";
                break;
            case 'reject':
                $sql = "update opr_order set
							ject_remark = :ject_remark,
							luu = :luu,
							status = :status
						where id = :id AND judge=0
						";
                $this->goods_list = array();
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
            if ($this->scenario == "audit"){
                $this->status = "approve";
            }elseif ($this->scenario == "edit"){
                $this->status = "read";
            }elseif ($this->scenario == "reject"){
                $this->status = "reject";
            }
            $command->bindParam(':status',$this->status,PDO::PARAM_STR);
        }

        if (strpos($sql,':remark')!==false)
            $command->bindParam(':remark',$this->remark,PDO::PARAM_STR);
        if (strpos($sql,':ject_remark')!==false)
            $command->bindParam(':ject_remark',$this->ject_remark,PDO::PARAM_STR);
        if (strpos($sql,':audit_time')!==false)
            $command->bindParam(':audit_time',date('Y-m-d H:i:s'),PDO::PARAM_STR);
        if (strpos($sql,':lud')!==false)
            $command->bindParam(':lud',date('Y-m-d H:i:s'),PDO::PARAM_STR);
        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        $command->execute();

        Yii::app()->db->createCommand()->insert('opr_order_status', array(
            'order_id'=>$this->id,
            'status'=>$this->status,
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
                if ($this->scenario == "audit"){
                    //減少庫存 inventory
                    $this->reduceInventory($goods["goods_id"],$goods["confirm_num"]);
                }
            }
        }
        $this->resetZIndex();

        //發送郵件
        OrderGoods::sendEmailTwo($oldOrderStatus,$this->status,$this->order_code);
		return true;
	}

    //減少庫存
	public function reduceInventory($goodsId,$num){
        if(empty($goodsId)||!is_numeric($goodsId) || floor($goodsId)!=$goodsId){
            return false;
        }
        if(empty($num)||!is_numeric($num)){
            return false;
        }
        Yii::app()->db->createCommand("update opr_warehouse set inventory=inventory-$num where id=$goodsId")->execute();
    }

    //退回
    function backward(){
        $rows = Yii::app()->db->createCommand()->select("id")->from("opr_order")->where('status = "approve" and id = :id',array(':id'=>$this->id))->queryAll();
        if(count($rows) > 0){
            $uid = Yii::app()->user->id;
            $this->status = "sent";
            //修改訂單狀態
            Yii::app()->db->createCommand()->update('opr_order', array(
                'status'=>$this->status,
                'remark'=>$this->remark,
                'luu'=>$uid,
                'lud'=>date('Y-m-d H:i:s'),
            ), 'id=:id', array(':id'=>$this->id));

            //補回庫存
            $goods_list = Yii::app()->db->createCommand()->select("goods_id,confirm_num")->from("opr_order_goods")
                ->where('order_id = :order_id',array(':order_id'=>$this->id))->queryAll();
            if($goods_list){
                foreach ($goods_list as $goods){
                    $num = $goods["confirm_num"];
                    $goodsId = $goods["goods_id"];
                    Yii::app()->db->createCommand("update opr_warehouse set inventory=inventory+$num where id=$goodsId")->execute();
                }
                $this->resetZIndex();
            }

            //添加狀態記錄
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


    public function getTableHeard(){
        $arr = array("物品編號","物品名称","物品單位","要求備註","總部備註","要求數量","實際數量");
        return $arr;
    }
    //整理出下載的物品列表
    public function resetGoodsList(){
        $goodsList = $this->goods_list;
        $arr = array();
        foreach ($goodsList as $goods){
            array_push($arr,array($goods["goods_code"],$goods["name"],$goods["unit"],$goods["note"],$goods["remark"],$goods["goods_num"],$goods["confirm_num"]));
        }
        return $arr;
    }
    //
    private function resetZIndex(){
        Yii::app()->db->createCommand("update opr_warehouse set z_index=1 where (inventory+0)>(min_num+0)")->execute();
        Yii::app()->db->createCommand("update opr_warehouse set z_index=2 where (inventory+0)<=(min_num+0)")->execute();
    }

    //退回單個物品
    public function blackGoods($str=""){
        $num = $this->num;
        $goodsId = $this->goods_id;
        $blackId = $this->black_id;
        Yii::app()->db->createCommand("update opr_order_goods set confirm_num=confirm_num-$num where id=$blackId")->execute();
        Yii::app()->db->createCommand("update opr_warehouse set inventory=inventory+$num where id=$goodsId")->execute();

        $this->resetZIndex();
        //記錄
        Yii::app()->db->createCommand()->insert('opr_order_status', array(
            'order_id'=>$this->id,
            'status'=>"backward",
            'r_remark'=>$str."退回數量:$num",
            'lcu'=>Yii::app()->user->user_display_name(),
            'time'=>date('Y-m-d H:i:s'),
        ));
        //記錄
        Yii::app()->db->createCommand()->insert('opr_warehouse_back', array(
            'order_id'=>$this->id,
            'warehouse_id'=>$goodsId,
            'back_num'=>$num,
            'old_num'=>$this->confirm_num,
            'lcu'=>Yii::app()->user->user_display_name(),
        ));
    }
    public function downTypeList(){
        $list = array(
            0=>Yii::t('procurement','All'),
            1=>Yii::t('procurement','Have read,Drop shipping'),
            2=>Yii::t('procurement','pending approval')
        );
        return $list;
    }

    //檢查是否有未發貨的訂單
    public function validateAll($type=0){
        $list = array("'read','sent'","'read'","'sent'");
        if(!key_exists($type,$list)){
            $type = 0;
        }
        $city = Yii::app()->user->city();
        $sql ="city='$city' AND judge=0 AND status in (".$list[$type].")";
        if($this->getScenario()=="approved"){
            if(empty($this->checkBoxDown)||!is_array($this->checkBoxDown)){
                return false;
            }
            $idList = implode(",",$this->checkBoxDown);
            $sql.=" and id in($idList)";
        }
        $rows = Yii::app()->db->createCommand()->select("*")->from("opr_order")->where($sql)->queryAll();
        if($rows){
            $this->orderList = $rows;
            return true;
        }else{
            return false;
        }
    }

    //批量下載訂單
    public function allDownload(){
        $orderList = $this->orderList;
        if(!empty($orderList)) {
            foreach ($orderList as &$order) {
                $order["lcu_name"]=OrderGoods::getNameToUsername($order['lcu']);
                $order["goodsList"] = WarehouseForm::getGoodsListToId($order['id']);
                $order["status"] = OrderList::printPurchaseStatus($order["status"]);
                $order["lcd"] = date("Y-m-d",strtotime($order["lcd"]));
            }
            return $orderList;
        }else{
            return array();
        }
    }

    //批量批准訂單
    public function allApproved(){
        $orderList = $this->orderList;
        $uid = Yii::app()->user->id;
        $city = Yii::app()->user->city();
        if(!empty($orderList)){
            foreach ($orderList as $order){
                $this->lcd = $order["lcd"];
                //記錄
                Yii::app()->db->createCommand()->insert('opr_order_status', array(
                    'order_id'=>$order["id"],
                    'status'=>"approve",
                    'r_remark'=>"",
                    'lcu'=>Yii::app()->user->user_display_name(),
                    'time'=>date('Y-m-d H:i:s'),
                ));

                //批量減少庫存
                $rows = Yii::app()->db->createCommand()->select("id,goods_id,goods_num,confirm_num")->from("opr_order_goods")
                    ->where("order_id =:order_id",array(":order_id"=>$order["id"]))->queryAll();
                if($rows){
                    foreach ($rows as $row){
                        $num = ($row["confirm_num"]===""||$row["confirm_num"]===null)?floatval($row["goods_num"]):floatval($row["confirm_num"]);
                        $goodsId = intval($row["goods_id"]);
                        //減少庫存
                        Yii::app()->db->createCommand("update opr_warehouse set inventory=inventory-$num where id=$goodsId")->execute();
                        //修改物品的實際數量
                        Yii::app()->db->createCommand()->update('opr_order_goods', array(
                            'confirm_num'=>$num,
                        ), "id=:id",array(":id"=>$row["id"]));
                    }
                }

                //修改物品的价格及狀態
                Yii::app()->db->createCommand()->update('opr_order', array(
                    'status'=>"approve",
                    'audit_time'=>date('Y-m-d H:i:s'),
                    'luu'=>$uid,
                ), "city='$city' AND judge=0 AND status in ('read','sent') and id=:id",array(":id"=>$order["id"]));
            }
        }

    }
}
