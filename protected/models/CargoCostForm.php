<?php

class CargoCostForm extends CFormModel
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
    public $total_price=0;


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
            'total_price'=>Yii::t('procurement','Cargo Cost'),
		);
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
                $this->order_user = $row['order_user'];
                //$this->technician = $row['technician'];
                $this->status = $row['status'];
                $this->remark = $row['remark'];
                $this->total_price = $row['total_price'];
                $this->lcu = OrderGoods::getNameToUsername($row['lcu']);
                $this->ject_remark = $row['ject_remark'];
                $this->lcd = date("Y-m-d",strtotime($row['lcd']));
                $this->statusList = OrderForm::getStatusListToId($row['id']);
                break;
			}
		}
		return true;
	}

	public function printTable(){
        $html = '';
        $rows = $rs = Yii::app()->db->createCommand()->select("costPrice(b.id,a.lcd) as cost_price,b.id as warehouse_id,a.lcd,b.name,b.inventory,b.goods_code,b.classify_id,b.unit,a.goods_num,a.confirm_num,a.id,a.goods_id,a.remark,a.note")
            ->from("opr_order_goods a")
            ->leftJoin("opr_warehouse b","a.goods_id = b.id")
            ->where('a.order_id=:order_id',array(':order_id'=>$this->id))->queryAll();

        if($rows){
            foreach ($rows as $row){//warehouse_id
                $num = ($row["confirm_num"]===""||$row["confirm_num"]===null)?floatval($row["goods_num"]):floatval($row["confirm_num"]);
                $price = empty($row["confirm_num"])?0:$row["confirm_num"];
                $html.="<tr>";
                $html.="<td>".$row["goods_code"]."</td>";
                $html.="<td>".$row["name"]."</td>";
                $html.="<td>".$row["unit"]."</td>";
                $html.="<td><span style='margin-right: 10px;'>".sprintf("%.2f",$price)."</span>";
                $html.=TbHtml::button(Yii::t('procurement','price history'), array(
                    'onclick'=>"printPriceTable(".$row["warehouse_id"].")",'data-toggle'=>'modal','data-target'=>'#priceFlow'));
                $html.="</td>";
                $html.="<td>".$row["confirm_num"]."</td>";
                $html.="<td>".sprintf("%.2f",$price*$num)."</td>";
                $html.="</tr>";
            }
        }
        return $html;
    }
}
