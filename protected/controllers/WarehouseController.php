<?php

class WarehouseController extends Controller
{
	public $function_id='YD01';

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
                'actions'=>array('new','edit','delete','save','copy','importGoods','downExcel','test'),
                'expression'=>array('WarehouseController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('importPrice','ajaxPriceHistory'),
                'expression'=>array('WarehouseController','allowImportPrice'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('WarehouseController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowImportPrice() {
        return Yii::app()->user->validFunction('YN02');
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('YD01');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('YD01');
    }
	public function actionIndex($pageNum=0) 
	{
		$model = new WarehouseList;
		if (isset($_POST['WarehouseList'])) {
			$model->attributes = $_POST['WarehouseList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['warehouse_ya01']) && !empty($session['warehouse_ya01'])) {
				$criteria = $session['warehouse_ya01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}


	public function actionSave()
	{
		if (isset($_POST['WarehouseForm'])) {
			$model = new WarehouseForm($_POST['WarehouseForm']['scenario']);
			$model->attributes = $_POST['WarehouseForm'];
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('warehouse/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionView($index)
	{
		$model = new WarehouseForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

    public function actionNew()
    {
        $model = new WarehouseForm('new');
        $this->render('form',array('model'=>$model,));
    }

    public function actionCopy()
    {
        if (isset($_POST['WarehouseForm'])) {
            $model = new WarehouseForm($_POST['WarehouseForm']['scenario']);
            $model->attributes = $_POST['WarehouseForm'];
            $model->id = 0;
            $model->setScenario("new");
            $this->render('form',array('model'=>$model));
        }
    }

	public function actionEdit($index)
	{
		$model = new WarehouseForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

    public function actionDelete()
    {
        $model = new WarehouseForm('delete');
        if (isset($_POST['WarehouseForm'])) {
            $model->attributes = $_POST['WarehouseForm'];
            if($model->deleteValidate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('warehouse/index'));
            }else{
                $model->scenario = "edit";
                Dialog::message(Yii::t('dialog','Validation Message'), Yii::t("procurement","This goods has been used and cannot be deleted"));
                $this->render('form',array('model'=>$model,));
            }
        }else{
            $this->redirect(Yii::app()->createUrl('warehouse/index'));
        }
    }

    public function actionDownExcel()
    {
        //$model = new WarehouseForm();
        $warehouseList = WarehouseForm::downExcel();
        $myExcel = new MyExcelTwo();
        $myExcel->setDataHeard($warehouseList["head"]);
        $myExcel->setDataBody($warehouseList["body"]);
        $myExcel->outDownExcel("仓库物品.xls");
    }

    public function actionImportGoods(){
        $model = new UploadExcelForm();
        $model->attributes = $_POST['UploadExcelForm'];
        $img = CUploadedFile::getInstance($model,'file');
        $city = Yii::app()->user->city();
        $path =Yii::app()->basePath."/../upload/";
        if (!file_exists($path)){
            mkdir($path);
        }
        $path =Yii::app()->basePath."/../upload/excel/";
        if (!file_exists($path)){
            mkdir($path);
        }
        $path.=$city."/";
        if (!file_exists($path)){
            mkdir($path);
        }
        $url = "upload/excel/".$city."/".date("YmdHis").".".$img->getExtensionName();
        $model->file = $img->getName();
        if ($model->file && $model->validate()) {
            $img->saveAs($url);
            $loadExcel = new LoadExcel($url);
            $list = $loadExcel->getExcelList();
            $model->loadGoods($list);
            $this->redirect(Yii::app()->createUrl($_POST['prevUrl']));
        }else{
            $message = CHtml::errorSummary($model);
            Dialog::message(Yii::t('dialog','Validation Message'), $message);
            $this->redirect(Yii::app()->createUrl($_POST['prevUrl']));
        }
    }

    public function actionImportPrice(){
        $model = new UploadExcelForm();
        $img = CUploadedFile::getInstance($model,'file');
        if(empty($img)){
            Dialog::message(Yii::t('dialog','Validation Message'), "文件不能为空");
            $this->redirect(Yii::app()->createUrl('warehouse/index'));
        }
        $city = Yii::app()->user->city();
        $path =Yii::app()->basePath."/../upload/";
        if (!file_exists($path)){
            mkdir($path);
        }
        $path =Yii::app()->basePath."/../upload/excel/";
        if (!file_exists($path)){
            mkdir($path);
        }
        $path.=$city."/";
        if (!file_exists($path)){
            mkdir($path);
        }
        $url = "upload/excel/".$city."/".date("YmdHis").".".$img->getExtensionName();
        $model->file = $img->getName();
        if ($model->file && $model->validate()) {
            $img->saveAs($url);
            $loadExcel = new LoadExcel($url);
            $list = $loadExcel->getExcelList();
            if($model->loadPrice($list)){
                if(empty($model->error_list)){
                    Dialog::message(Yii::t('dialog','Validation Message'), "导入成功！");
                }
            }
            $this->redirect(Yii::app()->createUrl('warehouse/index'));
        }else{
            $message = CHtml::errorSummary($model);
            Dialog::message(Yii::t('dialog','Validation Message'), $message);
            $this->redirect(Yii::app()->createUrl('warehouse/index'));
        }
    }

    //價格歷史的異步請求
    public function actionAjaxPriceHistory(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $id = $_GET['id'];
            $model = new WarehouseForm();
            $rs =$model->getPriceHistory($id);
            echo CJSON::encode($rs);//Yii 的方法将数组处理成json数据
        }else{
            $this->redirect(Yii::app()->createUrl('warehouse/index'));
        }
    }

    //訂單新增審核時間字段，系統自動完成數據輸入（一次性方法），將舊數據更新
    public function actionTest(){
        $connection = Yii::app()->db;
        $sql = "SELECT * FROM opr_order WHERE status IN ('finished','approve') AND (audit_time='' OR audit_time IS NULL )";
        $records = $connection->createCommand($sql)->queryAll();
        foreach ($records as $record){
            $order_id=$record["id"];
            $audit_time = $connection->createCommand("SELECT lcd FROM opr_order_status WHERE order_id=$order_id AND status='approve' order by id desc")->queryRow();
            if($audit_time){
                $audit_time = $audit_time["lcd"];
                $connection->createCommand()->update('opr_order', array("audit_time"=>$audit_time),"id=$order_id");
            }
        }
    }
}
