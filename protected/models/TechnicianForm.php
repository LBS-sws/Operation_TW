<?php

class TechnicianForm extends CFormModel
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
    public $goods_list;
    public $ject_remark;

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub

        $this->status = "pending";
/*        $this->goods_list = array(
            array(
                "id"=>"",
                "goods_id"=>"",
                "goods_code"=>"",
                "name"=>"",
                "type"=>"",
                "unit"=>"",
                "price"=>"",
                "goods_num"=>"",
                "note"=>"",
                "remark"=>"",
                "classify_id"=>"",
            )
        );*/
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
        );
    }

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('id, order_code, order_user, order_class, technician, status, remark, luu, lcu, lud, lcd, goods_list','safe'),
            array('goods_list','required'),
            array('goods_list','validateGoods'),
            array('remark','validateActivity','on'=>'audit'),
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
        foreach ($goods_list as $key =>$goods){
            if(empty($goods["goods_id"]) && empty($goods["goods_num"])){
                unset($this->goods_list[$key]);
                continue;
            }else if (empty($goods["goods_id"]) || empty($goods["goods_num"])){
                $message = Yii::t('procurement','The goods or quantity cannot be empty');
                $this->addError($attribute,$message);
                return false;
            }else if(!is_numeric($goods["goods_id"])|| floor($goods["goods_id"])!=$goods["goods_id"]){
                $message = Yii::t('procurement','goods does not exist');
                $this->addError($attribute,$message);
                return false;
            }else if(!is_numeric($goods["goods_num"])){
                $message = Yii::t('procurement','Goods Number can only be numbered');
                $this->addError($attribute,$message);
                return false;
            }else{
                $list = WarehouseForm::getGoodsToGoodsId($goods["goods_id"]);
                if (empty($list)){
                    $message = Yii::t('procurement','Not Font Goods').$goods["goods_id"]."a";
                    $this->addError($attribute,$message);
                    return false;
                }elseif ($list["decimal_num"] != "是"&& floor($goods["goods_num"])!=$goods["goods_num"]){
                    $message = $list["name"]."：".Yii::t('procurement','Goods can only be positive integers');
                    $this->addError($attribute,$message);
                    return false;
                }elseif (floatval($list["inventory"])<floatval($goods["goods_num"])){
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

    public function validateActivity($attribute, $params){
        $city = Yii::app()->user->city();
        $userName = Yii::app()->user->name;
        if ($this->scenario == "audit") {
            $rows = Yii::app()->db->createCommand()->select("count(id)")
                ->from("opr_order")->where("judge=0 and city=:city and status = 'approve' and lcu=:lcu",
                    array( ":city" => $city, ":lcu" => $userName))->queryScalar();
            if ($rows > 0) {
                $message = "您有订单没有收货，请收货后继续操作。";
                $this->addError($attribute, $message);
                return false;
            }
        }
    }


    public function retrieveData($index) {
        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;
        $rows = Yii::app()->db->createCommand()->select("*")
            ->from("opr_order")->where("id=:id and city=:city and judge=0 and lcu=:lcu",
                array(":id"=>$index,":city"=>$city,":lcu"=>$uid))->queryAll();
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->order_code = $row['order_code'];
                $this->goods_list = WarehouseForm::getGoodsListToId($row['id']);
                $this->order_user = $row['order_user'];
                //$this->technician = $row['technician'];
                $this->status = $row['status'];
                $this->remark = $row['remark'];
                $this->ject_remark = $row['ject_remark'];
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
        $goodsBool = true;
        $insetBool = false;
        switch ($this->scenario) {
            case 'delete':
                $sql = "delete from opr_order where id = :id and judge=0 and lcu=:lcu";
                $goodsBool = false;
                break;
            case 'new':
                $insetBool = true;
                $sql = "insert into opr_order(
							order_user, remark, status, lcu, lcd
						) values (
							:order_user,:remark, :status, :lcu, :lcd
						)";
                break;
            case 'edit':
                $sql = "update opr_order set
							remark = :remark,
							luu = :luu,
							lud = :lud
						where id = :id and judge=0
						";
                break;
            case 'audit':
                if(empty($this->id)){
                    $insetBool = true;
                    $sql = "insert into opr_order(
							order_user, remark, status, lcu, lcd
						) values (
							:order_user,:remark, :status, :lcu, :lcd
						)";
                }else{
                    $sql = "update opr_order set
							remark = :remark,
							luu = :luu,
							lcd = :lcd,
							lud = :lud,
							status = :status
						where id = :id and judge=0
						";
                }
                break;
            case 'finish':
                $sql = "update opr_order set
							remark = :remark,
							luu = :luu,
							lud = :lud,
							status = :status
						where id = :id and judge=0
						";
                $goodsBool = false;
                break;
            default:
                $goodsBool = false;
        }
        if (empty($sql)) return false;

        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;
        $order_username = Yii::app()->user->name;
        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        if (strpos($sql,':order_user')!==false)
            $command->bindParam(':order_user',$order_username,PDO::PARAM_STR);
        if (strpos($sql,':status')!==false){
            if($this->scenario == "new"){
                $this->status = "pending";
            }elseif ($this->scenario == "audit"){
                $this->status = "sent";
            }elseif ($this->scenario == "finish"){
                $this->status = "finished";
            }
            $command->bindParam(':status',$this->status,PDO::PARAM_STR);
        }

        if (strpos($sql,':remark')!==false)
            $command->bindParam(':remark',$this->remark,PDO::PARAM_STR);
        if (strpos($sql,':lud')!==false)
            $command->bindParam(':lud',date('Y-m-d H:i:s'),PDO::PARAM_STR);
        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcu')!==false)
            $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcd')!==false)
            $command->bindParam(':lcd',date('Y-m-d H:i:s'),PDO::PARAM_STR);
        $command->execute();

        if ($insetBool){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->scenario = "edit";
            $code = strval($this->id);
            $this->order_code = "";
            for($i = 0;$i < 5-strlen($code);$i++){
                $this->order_code.="0";
            }
            $this->order_code .= $code;
            Yii::app()->db->createCommand()->update('opr_order', array(
                'order_code'=>$this->order_code,
                'judge'=>0,
                'city'=>$city,
                'lcu_email'=>Yii::app()->user->email(),
            ), 'id=:id', array(':id'=>$this->id));
        }
        if ($this->scenario=='delete'){
            Yii::app()->db->createCommand()->delete('opr_order_status', 'order_id=:order_id', array(':order_id'=>$this->id));
            Yii::app()->db->createCommand()->delete('opr_order_goods', 'order_id=:order_id', array(':order_id'=>$this->id));
        }else{
            Yii::app()->db->createCommand()->insert('opr_order_status', array(
                'order_id'=>$this->id,
                'status'=>$this->status,
                'r_remark'=>$this->remark,
                'lcu'=>Yii::app()->user->user_display_name(),
                'time'=>date('Y-m-d H:i:s'),
            ));
        }


        if ($goodsBool){
            //先刪除訂單里的所有物品
            Yii::app()->db->createCommand()->delete('opr_order_goods', 'order_id=:order_id', array(':order_id'=>$this->id));
            //物品的添加
            foreach ($this->goods_list as $goods){
                //添加
                Yii::app()->db->createCommand()->insert('opr_order_goods', array(
                    'goods_id'=>$goods["goods_id"],
                    'order_id'=>$this->id,
                    'goods_num'=>$goods["goods_num"],
                    'note'=>$goods["note"],
                    'lcu'=>Yii::app()->user->user_display_name(),
                ));
            }
        }

        $this->updateGoodsStatus();
        //發送郵件
        OrderGoods::sendEmailTwo($oldOrderStatus,$this->status,$this->order_code);
        return true;
    }

    //修改訂單內物品的狀態
    protected function updateGoodsStatus(){
        Yii::app()->db->createCommand()->update('opr_order_goods', array(
            'order_status'=>$this->status,
        ), 'order_id=:order_id', array(':order_id'=>$this->id));
    }

    //判斷輸入框能否修改
    public function getInputBool(){
        if($this->scenario=='view'){
            return true;
        }
        if($this->status == "pending"||$this->status == "reject"){
            return false;
        }else{
            return true;
        }
    }
}
