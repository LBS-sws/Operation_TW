<?php

class OrderController extends Controller
{
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
                'actions'=>array('audit','edit','delete','save','finish','orderGoodsDelete','validateAjax'),
                'expression'=>array('OrderController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('OrderController','allowReadOnly'),
            ),
            array('allow',
                'actions'=>array('activity','new','save','audit','delete','orderGoodsDelete','validateAjax'),
                'expression'=>array('OrderController','addReadWrite'),
            ),
/*            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('OrderController','addReadOnly'),
            ),*/
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('YD03');
    }
    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('YD03');
    }
    public static function addReadWrite() {
        return Yii::app()->user->validRWFunction('YD04');
    }
    public static function addReadOnly() {
        return Yii::app()->user->validFunction('YD04');
    }
	public function actionIndex($pageNum=0) 
	{
		$this->function_id = 'YD03';
		Yii::app()->session['active_func'] = $this->function_id;

		$model = new OrderList;
		if (isset($_POST['OrderList'])) {
			$model->attributes = $_POST['OrderList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['order_ya01']) && !empty($session['order_ya01'])) {
				$criteria = $session['order_ya01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}


	public function actionSave()
	{
		if (isset($_POST['OrderForm'])) {
			$model = new OrderForm($_POST['OrderForm']['scenario']);
			$model->attributes = $_POST['OrderForm'];
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('order/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $model->statusList = $model->getStatusListToId($model->id);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	//完成收貨
	public function actionFinish()
	{
		if (isset($_POST['OrderForm'])) {
			$model = new OrderForm("finish");
            $model->attributes = $_POST['OrderForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('order/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('order/edit',array('index'=>$model->id)));
            }
		}
	}

    //提交審核
	public function actionAudit()
	{
		if (isset($_POST['OrderForm'])) {
		    $scenario =$_POST['OrderForm']['scenario'];
			$model = new OrderForm();
			$model->attributes = $_POST['OrderForm'];
			$model->scenario = "audit";
			if ($model->validate()) {
				$model->saveData();
                $model->scenario = "edit";
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done and Sent Notification'));
				$this->redirect(Yii::app()->createUrl('order/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $model->scenario = $scenario;
                $model->statusList = $model->getStatusListToId($model->id);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionView($index)
	{
		$model = new OrderForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

    public function actionNew($index)
    {
        $model = new OrderForm('new');
        $model->activity_id = $index;
        if ($model->validateLogin()){
            $this->render('form',array('model'=>$model,));
        }else{
            $this->redirect(Yii::app()->createUrl('order/activity'));
        }
    }

	public function actionEdit($index)
	{
		$model = new OrderForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

    public function actionDelete()
    {
        $model = new OrderForm('delete');
        if (isset($_POST['OrderForm'])) {
            $model->attributes = $_POST['OrderForm'];
            $model->saveData();
            Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
        }
        $this->redirect(Yii::app()->createUrl('order/index'));
    }

    public function actionOrderGoodsDelete(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $id = $_POST['id'];
            $rs = OrderGoods::model()->deleteByPk($id);
            echo CJSON::encode(array('status'=>$rs));//Yii 的方法将数组处理成json数据
        }else{
            $this->redirect(Yii::app()->createUrl('order/index'));
        }
    }

    //區域訂單活動
    public function actionActivity($pageNum=0){
		$this->function_id = 'YD04';
		Yii::app()->session['active_func'] = $this->function_id;

        $model = new AddOrderList;
        if (isset($_POST['AddOrderList'])) {
            $model->attributes = $_POST['AddOrderList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['addOrder_ya01']) && !empty($session['addOrder_ya01'])) {
                $criteria = $session['addOrder_ya01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index_activity',array('model'=>$model));
    }

    //物品列表的即時驗證
    public function actionValidateAjax(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $model = new OrderForm($_POST['OrderForm']['scenario']);
            $model->attributes = $_POST['OrderForm'];
            if ($model->validate()) {
                echo CJSON::encode(array('status'=>1));//Yii 的方法将数组处理成json数据
            } else {
                $message = CHtml::errorSummary($model);
                echo CJSON::encode(array('status'=>0,'error'=>$message));//Yii 的方法将数组处理成json数据
            }
        }else{
            $this->redirect(Yii::app()->createUrl('order/index'));
        }
    }
}
