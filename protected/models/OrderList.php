<?php

class OrderList extends CListPageModel
{
    public $activity_id;
    public $activity_title;
	public function attributeLabels()
	{
		return array(	
			'order_code'=>Yii::t('procurement','Order Code'),
			'order_class'=>Yii::t('procurement','Order Class'),
			'activity'=>Yii::t('procurement','Activity Code'),
			'goods_list'=>Yii::t('procurement','Goods List'),
			'order_user'=>Yii::t('procurement','Order User'),
			'technician'=>Yii::t('procurement','Technician'),
			'activity_id'=>Yii::t('procurement','Order of Activity'),
			'status'=>Yii::t('procurement','Order Status'),
			'city'=>Yii::t('procurement','Order For City'),
			'lcd'=>Yii::t('procurement','Apply for time'),
		);
	}

    //根據活動id查活動編號
    public function getActivityTitleToId($activity_id){
        $rs = Yii::app()->db->createCommand()->select("activity_title")
            ->from("opr_order_activity")->where('id=:id',array(':id'=>$activity_id))->queryAll();
        if($rs){
            return $rs[0]["activity_title"];
        }
        return "";
    }

	public function retrieveDataByPage($pageNum=1,$activity_id="yes")
	{
	    //order_user = '$userName' OR technician = '$userName'
		$city = Yii::app()->user->city();
		$userName = Yii::app()->user->name;
		if($activity_id === "yes"){
            $sql1 = "select *
				from opr_order
				where (city = '$city' AND judge=1) 
			";
            $sql2 = "select count(id)
				from opr_order
				where (city = '$city' AND judge=1) 
			";
        }else{
            $this->activity_id = $activity_id;
		    $this->activity_title = $this->getActivityTitleToId($activity_id);
            $sql1 = "select *
				from opr_order
				where (activity_id = '$activity_id' AND status_type=1 AND judge=1 AND status != 'pending' AND status != 'cancelled') 
			";
            $sql2 = "select count(id)
				from opr_order
				where (activity_id = '$activity_id' AND status_type=1 AND judge=1 AND status != 'pending' AND status != 'cancelled') 
			";
        }
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'lcd':
					$clause .= General::getSqlConditionClause('lcd', $svalue);
				case 'order_code':
					$clause .= General::getSqlConditionClause('order_code', $svalue);
					break;
				case 'activity_id':
                    $activityIdSql = $this->getActivityIdSql($svalue);
                    $clause .= " and activity_id in ($activityIdSql)";
					break;
				case 'city':
				    $citySql = $this->getCitySql($svalue);
					$clause .= " and city in ($citySql)";
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		} else
			$order = " order by id desc";

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();
		
		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
					$this->attr[] = array(
						'id'=>$record['id'],
                        'order_code'=>$record['order_code'],
                        'order_class'=>Yii::t("procurement",$record['order_class']),
                        'activity_id'=>$this->getActivityTitleToId($record['activity_id']),
						'goods_list'=>OrderForm::getGoodsListToId($record['id']),
						'order_user'=>$record['order_user'],
						'technician'=>$record['technician'],
						'status'=>$record['status'],
						'status_type'=>$record['status_type'],
						'city'=>$record['city'],
						'lcd'=>date("Y-m-d",strtotime($record['lcd'])),
					);
			}
		}
		$session = Yii::app()->session;
		$session['order_ya01'] = $this->getCriteria();
		return true;
	}

	public function printOrderStatus($status,$status_type = 1){
        if($status_type == 1){
            switch ($status){
                case "sent":
                    //已发送，待审核
                    return Yii::t("procurement","Waiting for central audit");
                case "read":
                    return Yii::t("procurement","Central checked");
                case "approve":
                    //已发货，待收货
                    return Yii::t("procurement","Shipped out, Wait for receiving");
                case "reject":
                    return Yii::t("procurement","Central refused order");
                case "finished":
                    return Yii::t("procurement","finished");
                case "expired":
                    return Yii::t("procurement","expired");
                default:
                    return Yii::t("procurement","Error Status");
            }
        }else{
            switch ($status){
                case "pending":
                    //草稿，未发送
                    return Yii::t("procurement","Draft, not sent");
                case "sent":
                    //已发送，待审核
                    return Yii::t("procurement","Waiting area audit");
                case "reject":
                    return Yii::t("procurement","Area rejected");
                case "finished":
                    return Yii::t("procurement","finished");
                case "expired":
                    return Yii::t("procurement","expired");
                default:
                    return Yii::t("procurement","Error Status");
            }
        }
    }

	public function printTechnicianStatus($status){
        switch ($status){
            case "pending":
                //已发送，待审核
                return Yii::t("procurement","Draft, not sent");
            case "sent":
                //已发送，待审核
                return Yii::t("procurement","Sent, pending approval");
            case "read":
                return Yii::t("procurement","Have read,Drop shipping");
            case "approve":
                //已审核，已发货
                return Yii::t("procurement","Shipped out, Wait for receiving");
            case "reject":
                return Yii::t("procurement","Reject");
            case "finished":
                return Yii::t("procurement","finished");
            default:
                return Yii::t("procurement","Error Status");
        }
    }

	public function printPurchaseStatus($status){
        switch ($status){
            case "sent":
                //已发送，待审核
                return Yii::t("procurement","pending approval");
            case "read":
                return Yii::t("procurement","Have read,Drop shipping");
            case "approve":
                //已审核，已发货
                return Yii::t("procurement","Has been approved, Shipped out");
            case "reject":
                return Yii::t("procurement","Reject");
            case "finished":
                return Yii::t("procurement","finished");
            default:
                return Yii::t("procurement","Error Status");
        }
    }

    public function getCitySql($str){
        $citySql = "";
        $suffix = Yii::app()->params['envSuffix'];
        $suffix = "security".$suffix.".sec_city";
        $rs = Yii::app()->db->createCommand()->select("code")->from($suffix)->where(array('like', 'name', "%$str%"))->queryAll();

        if($rs){
            foreach ($rs as $key => $row){
                if($key != 0){
                    $citySql.=",";
                }
                $citySql.="'".$row["code"]."'";
            }
        }else{
            $citySql.="''";
        }
	    return $citySql;
    }

    public function getCityNameToCode($code){
        $suffix = Yii::app()->params['envSuffix'];
        $suffix = "security".$suffix.".sec_city";
        $rs = Yii::app()->db->createCommand()->select("name")->from($suffix)->where("code=:code",array(":code"=>$code))->queryAll();

        if($rs){
            return $rs[0]["name"];
        }else{
            return "";
        }
    }

    public function getActivityIdSql($str){
        $activityIdSql = "";
        $rs = Yii::app()->db->createCommand()->select("id")->from("opr_order_activity")->where(array('like', 'activity_title', "%$str%"))->queryAll();

        if($rs){
            foreach ($rs as $key => $row){
                if($key != 0){
                    $activityIdSql.=",";
                }
                $activityIdSql.="'".$row["id"]."'";
            }
        }else{
            $activityIdSql.="''";
        }
	    return $activityIdSql;
    }

    //獲取訂單需要處理的數據
    public function waitingMessage(){
        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;
        $fast_num = Yii::app()->db->createCommand()->select("count(id)")
            ->from("opr_order")->where('status="sent" and status_type=1 and order_class="Fast" and judge=1')->queryScalar();
        $imDo_num = Yii::app()->db->createCommand()->select("count(id)")
            ->from("opr_order")->where('status="sent" and status_type=1  and order_class!="Fast" and judge=1')->queryScalar();
        $area_num = Yii::app()->db->createCommand()->select("count(id)")
            ->from("opr_order")->where('status="sent" and status_type=0 and city=:city and judge=1',array(":city"=>$city))->queryScalar();
        $take_num = Yii::app()->db->createCommand()->select("count(id)")
            ->from("opr_order")->where('status="approve" and judge=1 and city=:city',array(":city"=>$city))->queryScalar();
        $deli_num = Yii::app()->db->createCommand()->select("count(id)")
            ->from("opr_order")->where('status="sent" and judge=0 and city=:city',array(":city"=>$city))->queryScalar();
        $goods_num = Yii::app()->db->createCommand()->select("count(id)")
            ->from("opr_order")->where('status="approve" and judge=0 and city=:city and lcu=:lcu',array(":city"=>$city,":lcu"=>$uid))->queryScalar();

		// 营业报告审核的數量
		$suffix = Yii::app()->params['envSuffix'];
		$type = Yii::app()->user->validFunction('YN01') ? 'PA' : 'PH';
		$wf = new WorkflowOprpt;
		$wf->connection = Yii::app()->db;
		$list = $wf->getPendingRequestIdList('OPRPT', $type, $uid);
		if (empty($list)) $list = '0';
		$cityallow = Yii::app()->user->city_allow();
		$sql = "select count(a.id)
				from opr_monthly_hdr a, security$suffix.sec_city b 
				where a.city in ($cityallow) and a.city=b.code 
				and a.id in ($list)
			";
		$rep_num = Yii::app()->db->createCommand($sql)->queryScalar();
        //$rep_num = 0;
		// 营业报告审核的數量 -- END
		
		return array(
            "YS04"=>$fast_num,//快速訂單的數量
            "YS01"=>$imDo_num,//採購活動的數量
            "YD03"=>$take_num,//地區待收貨的數量
            "YD02"=>$deli_num,//地區發貨的數量
            "YD06"=>$area_num,//地區審核數量
            "YC02"=>$goods_num,//技術員收貨的數量
			"YA03"=>$rep_num,//营业报告审核的數量
        );
    }
}
