<?php

class WarehouseForm extends CFormModel
{
	public $id;
	public $goods_code;
	public $name;
	public $unit;
	public $orderClass = "Warehouse";
	public $inventory;
	public $classify_id;
	public $price=0.00;
	public $costing;
	public $decimal_num;
	public $luu;
	public $lcu;
	public $min_num;
	public $matters;
	public $matching;
	public $z_index=1;
    public $display = 1;
    private $foreach_num = 0;

	public function attributeLabels()
	{
		return array(
            'goods_code'=>Yii::t('procurement','Goods Code'),
            'classify_id'=>Yii::t('procurement','Classify'),
            'name'=>Yii::t('procurement','Name'),
            'unit'=>Yii::t('procurement','Unit'),
            'inventory'=>Yii::t('procurement','Inventory'),
            'price'=>Yii::t('procurement','Price（RMB）'),
            'costing'=>Yii::t('procurement','Costing（RMB）'),
            'decimal_num'=>Yii::t('procurement','Decimal'),
            'min_num'=>Yii::t('procurement','min inventory'),
            'matching'=>Yii::t('procurement','matching'),
            'matters'=>Yii::t('procurement','matters'),
            'display'=>Yii::t('procurement','judge for visible'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, goods_code,display, min_num, name, unit, inventory, classify_id, price, costing, decimal_num, lcu, luu, matching, matters','safe'),
            array('name','required'),
            array('classify_id','required'),
            array('unit','required'),
            array('inventory','required'),
            array('inventory','numerical','allowEmpty'=>false,'integerOnly'=>false),
            array('min_num','required'),
            array('min_num','numerical','allowEmpty'=>false,'integerOnly'=>false),
			array('name','validateId'),
			array('name','validateName'),
			array('goods_code','validateCode'),
			array('price','validatePrice'),
		);
	}
//
	public function validateId($attribute, $params){
	    //Yii::app()->user->validFunction('YN04');
        $city = Yii::app()->user->city();
        $matching = "";
        $matters = "";
        if($this->getScenario() == "edit"){
            $row = Yii::app()->db->createCommand()->select("matching,matters")->from("opr_warehouse")
                ->where('id=:id and city=:city', array(':id'=>$this->id,':city'=>$city))->queryRow();
            if($row){
                if(Yii::app()->user->validFunction('YN04')){
                    $matching = $this->matching;
                    $matters = $this->matters;
                }else{
                    $matching =$row["matching"];
                    $matters =$row["matters"];
                }
            }else{
                $message = "數據異常，請於管理員聯繫";
                $this->addError($attribute,$message);
            }
        }
        $this->matching = $matching;
        $this->matters = $matters;
	}
	public function validateName($attribute, $params){
        $city = Yii::app()->user->city();
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("opr_warehouse")
            ->where('name=:name and id!=:id and city = :city', array(':name'=>$this->name,':id'=>$id,':city'=>$city))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('procurement','the name of already exists');
            $this->addError($attribute,$message);
        }
	}
	public function validateCode($attribute, $params){
        $city = Yii::app()->user->city();
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("opr_warehouse")
            ->where('goods_code=:goods_code and id!=:id and city = :city', array(':goods_code'=>$this->goods_code,':id'=>$id,':city'=>$city))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('procurement','the Goods Code of already exists');
            $this->addError($attribute,$message);
        }
	}
	public function validatePrice($attribute, $params){
	    if(floatval($this->inventory)<=floatval($this->min_num)){
	        $this->z_index = 2;
        }else{
	        $this->z_index = 1;
        }
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

	public function retrieveData($index) {
		$city = Yii::app()->user->city();
		$rows = Yii::app()->db->createCommand()->select("*,costPrice(id,now()) as cost_price")
            ->from("opr_warehouse")->where("id=:id and city=:city",array(":id"=>$index,':city'=>$city))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->unit = $row['unit'];
                $this->classify_id = $row['classify_id'];
                $this->goods_code = $row['goods_code'];
                $this->inventory = $row['inventory'];
                $this->costing = sprintf("%.2f",$row['costing']);
                $this->decimal_num = empty($row['decimal_num'])?"否":$row['decimal_num'];
                $this->price = $row['cost_price'];
                $this->min_num = $row['min_num'];
                $this->matters = $row['matters'];
                $this->matching = $row['matching'];
                $this->display = $row['display'];
                break;
			}
		}
		return true;
	}

    //獲取物品列表
    public function getGoodsList(){
        $city = Yii::app()->user->city();
        $rs = Yii::app()->db->createCommand()->select()->from("opr_warehouse")->where("city=:city",array(":city"=>$city))->queryAll();
        return $rs;
    }

    //根據物品id獲取物品信息
    public function getGoodsToGoodsId($goods_id){
        $city = Yii::app()->user->city();
        $rows = Yii::app()->db->createCommand()->select("*")
            ->from("opr_warehouse")
            ->where('id = :id and city=:city',array(':id'=>$goods_id,':city'=>$city))
            ->queryAll();
        if($rows){
            return $rows[0];
        }else{
            return array();
        }
    }

    //根據訂單id查訂單所有物品
    public function getGoodsListToId($order_id){
        $rs = Yii::app()->db->createCommand()->select("b.id as warehouse_id,a.lcd,b.matching,b.matters,b.name,b.inventory,b.goods_code,b.classify_id,b.unit,a.goods_num,a.confirm_num,a.id,a.goods_id,a.remark,a.note")
            ->from("opr_order_goods a,opr_warehouse b")->where('a.order_id=:order_id and a.goods_id = b.id',array(':order_id'=>$order_id))->queryAll();
        return $rs;
    }

    //
    public function getPriceHistory($id){
        $html = '';
        $rs = Yii::app()->db->createCommand()->select("a.year,a.month,a.price,b.name,b.goods_code")
            ->from("opr_warehouse_price a")->leftJoin('opr_warehouse b',"a.warehouse_id=b.id")
            ->where('b.id =:id',array(':id'=>$id))->order("a.year desc,a.month desc")->queryAll();
        if($rs){
            foreach ($rs as $row){
                $html.="<tr>";
                $html.="<td>".$row["goods_code"]."</td>";
                $html.="<td>".$row["name"]."</td>";
                $html.="<td>".$row["year"]."/".$row["month"]."</td>";
                $html.="<td>".$row["price"]."</td>";
                $html.="</tr>";
            }
        }
        if(empty($html)){
            $html.="<span>该物品没有单价历史</span>";
        }
        return array('status'=>1,'html'=>$html);
    }

    //
    public function getPriceToIdAndDate($id,$date=''){
        if(empty($date)){
            $date = date("Y-m");
        }
        $year = intval(date("Y",strtotime($date)));
        $month = intval(date("m",strtotime($date)));
        $rs = Yii::app()->db->createCommand()->select("price")->from("opr_warehouse_price")
            ->where("warehouse_id =:id and (year < $year or (year = $year and month<=$month))",array(':id'=>$id))->order("year desc,month desc")->queryRow();
        if($rs){
            return $rs["price"];
        }
        return 0;
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
                $sql = "delete from opr_warehouse where id = :id AND city=:city";
                break;
            case 'new':
                $sql = "insert into opr_warehouse(
							name, unit, display, inventory, classify_id, lcu, goods_code,city,costing,decimal_num,min_num,z_index,matching,matters
						) values (
							:name, :unit, :display, :inventory, :classify_id, :lcu, :goods_code,:city,:costing,:decimal_num,:min_num,:z_index,:matching,:matters
						)";
                break;
            case 'edit':
                $sql = "update opr_warehouse set
							name = :name, 
							goods_code = :goods_code, 
							display = :display, 
							classify_id = :classify_id, 
							unit = :unit,
							costing = :costing,
							decimal_num = :decimal_num,
							z_index = :z_index,
							min_num = :min_num,
							matching = :matching,
							matters = :matters,
							luu = :luu,
							inventory = :inventory
						where id = :id AND city=:city
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
        if (strpos($sql,':display')!==false)
            $command->bindParam(':display',$this->display,PDO::PARAM_INT);
        if (strpos($sql,':name')!==false)
            $command->bindParam(':name',$this->name,PDO::PARAM_STR);
        if (strpos($sql,':goods_code')!==false){
            $goodsCode = empty($this->goods_code)?1:$this->goods_code;
            $command->bindParam(':goods_code',$goodsCode,PDO::PARAM_STR);
        }
        if (strpos($sql,':unit')!==false)
            $command->bindParam(':unit',$this->unit,PDO::PARAM_STR);
        if (strpos($sql,':price')!==false)
            $command->bindParam(':price',$this->price,PDO::PARAM_STR);
        if (strpos($sql,':costing')!==false)
            $command->bindParam(':costing',$this->costing,PDO::PARAM_STR);
        if (strpos($sql,':decimal_num')!==false)
            $command->bindParam(':decimal_num',$this->decimal_num,PDO::PARAM_STR);
        if (strpos($sql,':min_num')!==false)
            $command->bindParam(':min_num',$this->min_num,PDO::PARAM_STR);
        if (strpos($sql,':z_index')!==false)
            $command->bindParam(':z_index',$this->z_index,PDO::PARAM_INT);
        if (strpos($sql,':inventory')!==false)
            $command->bindParam(':inventory',$this->inventory,PDO::PARAM_STR);
        if (strpos($sql,':matching')!==false)
            $command->bindParam(':matching',$this->matching,PDO::PARAM_STR);
        if (strpos($sql,':matters')!==false)
            $command->bindParam(':matters',$this->matters,PDO::PARAM_STR);
        if (strpos($sql,':classify_id')!==false)
            $command->bindParam(':classify_id',$this->classify_id,PDO::PARAM_INT);

        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$city,PDO::PARAM_STR);
        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcu')!==false)
            $command->bindParam(':lcu',$uid,PDO::PARAM_STR);

        $command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->scenario = "edit";
        }
        $this->setGoodsCode($this->id);
		return true;
	}

    private function setGoodsCode($id){
        if(empty($this->goods_code)){
            $this->foreach_num++;
            $city = Yii::app()->user->city();
            $code = strval($id);
            $goodsCode = "W";
            for($i = 0;$i < 5-strlen($code);$i++){
                $goodsCode.="0";
            }
            $goodsCode .= $code;
            $row = Yii::app()->db->createCommand()->select("id")->from("opr_warehouse")
                ->where('goods_code=:goods_code and id!=:id and city = :city',
                    array(':goods_code'=>$goodsCode,':id'=>$this->id,':city'=>$city))->queryRow();
            if($row&&$this->foreach_num<10){
                $this->setGoodsCode($row['id']);
            }else{
                Yii::app()->db->createCommand()->update('opr_warehouse', array(
                    'goods_code'=>$goodsCode
                ), 'id=:id', array(':id'=>$this->id));
            }
        }
    }

    public function downExcel(){
        $city = Yii::app()->user->city();
        $list["head"] = array("存货编码","存货名称","主计量单位","所属分类码","参考售价(成本)","是否允许小数","现有库存","产品配比","使用注意事项");
        if(Yii::app()->user->validFunction('YN02')){
            $list["head"][]="单价年月";
            $list["head"][]="单价";
        }
        $rs = Yii::app()->db->createCommand()->select("a.*,b.name as classify_name")->from("opr_warehouse a")
            ->leftJoin("opr_classify b","a.classify_id=b.id")
            ->where('a.city=:city',array(':city'=>$city))->queryAll();
        $list["body"] = array();
        if($rs){
            foreach ($rs as $row){
                $arr = array(
                    "goods_code"=>$row["goods_code"],
                    "name"=>$row["name"],
                    "unit"=>$row["unit"],
                    "classify_name"=>$row["classify_name"],
                    "costing"=>$row["costing"],
                    "decimal_num"=>$row["decimal_num"],
                    "inventory"=>$row["inventory"],
                    "matching"=>$row["matching"],
                    "matters"=>$row["matters"]
                );
                if(Yii::app()->user->validFunction('YN02')){
                    $priceList = Yii::app()->db->createCommand()->select("price as cost_price,year,month")->from("opr_warehouse_price")
                        ->where("(year<date_format(:date_time,'%Y') or (year=date_format(:date_time,'%Y') and month<=date_format(:date_time,'%m'))) AND warehouse_id = :id",
                            array(':id'=>$row['id'],':date_time'=>date("Y-m-d")))->order("year DESC,month DESC")->queryRow();
                    if(!$priceList){
                        $priceList=array('cost_price'=>'无','year'=>'无','month'=>'无');
                    }
                    $arr["cost_year_month"] = $priceList["cost_price"]==="无"?"无":$priceList["year"]."/".$priceList["month"];
                    $arr["cost_price"] = $priceList["cost_price"];
                }
                $list["body"][]=$arr;
            }
        }
        return $list;
    }
}
