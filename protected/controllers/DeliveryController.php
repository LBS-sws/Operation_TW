<?php

//技術員模塊的採購控制器
class DeliveryController extends Controller
{
	public $function_id='YD02';
	
    public function filters()
    {
        return array(
            'enforceSessionExpiration',
            'enforceNoConcurrentLogin',
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow',
                'actions'=>array('save','audit','reject','edit','backward','black'),
                'expression'=>array('DeliveryController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view','allApproved','allDownload'),
                'expression'=>array('DeliveryController','allowReadOnly'),
            ),
            array('allow',
                'actions'=>array('downorder'),
                'expression'=>array('DeliveryController','allowRead'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('YD02');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('YD02');
    }
    public static function allowRead() {
        return true;
    }
	public function actionIndex($pageNum=0) 
	{
		$model = new DeliveryList;
		if (isset($_POST['DeliveryList'])) {
			$model->attributes = $_POST['DeliveryList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['delivery_ya01']) && !empty($session['delivery_ya01'])) {
				$criteria = $session['delivery_ya01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}


	public function actionSave()
	{
		if (isset($_POST['DeliveryForm'])) {
			$model = new DeliveryForm($_POST['DeliveryForm']['scenario']);
			$model->attributes = $_POST['DeliveryForm'];
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('delivery/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionAudit()
	{
		if (isset($_POST['DeliveryForm'])) {
			$model = new DeliveryForm("audit");
			$model->attributes = $_POST['DeliveryForm'];
			if ($model->validate()) {
				$model->saveData();
				$model->scenario = "edit";
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('delivery/edit',array('index'=>$model->id)));
			} else {
                $model->scenario = "edit";
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionReject()
	{
		if (isset($_POST['DeliveryForm'])) {
			$model = new DeliveryForm("reject");
			$model->attributes = $_POST['DeliveryForm'];
			if ($model->validate()) {
				$model->saveData();
				$model->scenario = "edit";
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('delivery/edit',array('index'=>$model->id)));
			} else {
                $model->scenario = "edit";
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

    public function actionView($index)
    {
        $model = new DeliveryForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

	public function actionEdit($index)
	{
		$model = new DeliveryForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

    public function actionBackward()
    {
        if (isset($_POST['DeliveryForm'])) {
            $model = new DeliveryForm("backward");
            $model->attributes = $_POST['DeliveryForm'];
            if($model->backward()){
                Dialog::message(Yii::t('dialog','Information'), Yii::t('procurement','Backward Done'));
            }else{
                Dialog::message(Yii::t('dialog','Validation Message'), Yii::t('procurement','Backward Error'));
            }
            $model->scenario = "edit";
            $this->redirect(Yii::app()->createUrl('delivery/edit',array('index'=>$model->id)));
        }
    }


    //下載領料訂單
    public function actionDownorder($index){
        $model = new DeliveryForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $myExcel = new MyExcelTwo();
            $myExcel->setStartRow(5);
            $myExcel->setRowContent("A1","订单编号：".$model->order_code,"F1");
            $myExcel->setRowContent("A2","下单用户：".$model->lcu,"F2");
            $myExcel->setDataHeard($model->getTableHeard());
            $myExcel->setDataBody($model->resetGoodsList());
            $myExcel->outDownExcel($model->lcu."的订单.xls");
        }
    }

    //單個物品退回
    public function actionBlack(){
        $arr = $_POST;
        $model = new DeliveryForm("black");
        $model->attributes = $arr;
        if($model->validate()){
            $model->blackGoods($arr["name"]);
            Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
        }else{
            $message = CHtml::errorSummary($model);
            Dialog::message(Yii::t('dialog','Validation Message'), $message);
        }
        $this->redirect(Yii::app()->createUrl('delivery/edit',array('index'=>$model->id)));
    }

    //全部批准發貨
    public function actionAllApproved(){
        $model = new DeliveryForm("approved");
        $model->setAttributes($_POST['DeliveryList']);
        if($model->validateAll()){
            if($model->validatePriceOverTime('id')){
                $model->allApproved();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('delivery/index'));
            }else{
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('delivery/index'));
            }
        }else{
            Dialog::message(Yii::t('dialog','Validation Message'), "沒有待发货的订单");
            $this->redirect(Yii::app()->createUrl('delivery/index'));
        }
    }

    //下載全部未發貨訂單
    public function actionAllDownload($downType=0){
        $model = new DeliveryForm("down");
        if($model->validateAll($downType)){
            $orderList = $model->allDownload();
            $myExcel = new MyExcelTwo();
            $goodsList = $myExcel->setDeliveryExcel($orderList);
            $myExcel->setDeliveryExcelTwo($goodsList);
            $myExcel->outDownExcel("外勤领料审批订单.xls");
        }else{
            Dialog::message(Yii::t('dialog','Validation Message'), "沒有待发货的订单");
            $this->redirect(Yii::app()->createUrl('delivery/index'));
        }
    }
}
