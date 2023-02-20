<?php

class MonthlyController extends Controller 
{
	public function filters()
	{
		return array(
			'enforceRegisteredStation',
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
				'actions'=>array('edit','save','submit','resubmit','fileupload','fileremove'),
				'expression'=>array('MonthlyController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('edit','filedownload','fileupload','fileremove'),
				'expression'=>array('MonthlyController','allowReadWriteC'),
			),
			array('allow', 
				'actions'=>array('index','view','filedownload','addType','filedownload'),
				'expression'=>array('MonthlyController','allowReadOnly'),
			),
			array('allow', 
				'actions'=>array('indexc','view','accept','reject','acceptm','rejectm','filedownload'),
				'expression'=>array('MonthlyController','allowReadOnlyC'),
			),
			array('allow', 
				'actions'=>array('indexa','view','accept','reject','acceptm','rejectm','filedownload','fileupload','fileremove'),
				'expression'=>array('MonthlyController','allowReadOnlyA'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

    // To override the checking in components/Controller.php
	public function beforeAction($action) {
		$index_id = $action->getId();
		if (strpos('index/indexa/indexc/edit/view/', $index_id.'/')!==false) {
			if (strpos($index_id,'index')===false) {
				$params = $this->getActionParams();
				if (isset($params['rtn'])) $index_id = $params['rtn'];
			}
			$this->function_id = $index_id=='indexa' ? 'YA03' : ($index_id=='indexc' ? 'YA02' : 'YA01');
			return parent::beforeAction($action);
		} else {
			return true;
		}
	}

    //add opr_monthly_field mysql
	public function actionAddType() {
        $sql = "select id from opr_monthly_field WHERE function_name='1001' and code='11001' and name='空气净化机租赁'";
        $typeRow = Yii::app()->db->createCommand($sql)->queryRow();
        if($typeRow){
            Yii::app()->db->createCommand()->delete('opr_monthly_field', "code='11001' and function_name='1001'");
            Yii::app()->db->createCommand()->delete('opr_monthly_dtl', "data_field='11001'");
            echo "</br>delete success;</br>";
        }
        $sql = "select id from opr_monthly_field WHERE function_name='13' and code='100055'";
        $typeRow = Yii::app()->db->createCommand($sql)->queryRow();
        if(!$typeRow){
            echo "</br>error not find code:100055;</br>";
            return false;
        }
        $suffix = Yii::app()->params['envSuffix'];
        $sql = "select a.code
				from security$suffix.sec_city a left outer join security$suffix.sec_city b on a.code=b.region 
				where b.code is null 
				order by a.code
			";
        $rows = Yii::app()->db->createCommand($sql)->queryAll();
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $city = $row['code'];
                echo "CITY: $city<br/>";
                $sql = "select id from opr_monthly_hdr 
						where city='$city' and year_no=2023 and month_no in (1,2)";
                $rcs = Yii::app()->db->createCommand($sql)->queryAll();
                if($rcs){
                    foreach ($rcs as $rc){
                        $sql = "select id from opr_monthly_dtl WHERE hdr_id={$rc['id']} and data_field='100055'";
                        $bool = Yii::app()->db->createCommand($sql)->queryScalar();
                        if(!$bool){
                            Yii::app()->db->createCommand()->insert('opr_monthly_dtl', array(
                                'data_field'=>"100055",
                                'hdr_id'=>$rc['id'],
                                'data_value'=>"0.00",
                                'manual_input'=>"Y",
                                'lcu'=>"shenchao"
                            ));
                        }
                    }
                }
            }
        }
        echo "</br>end;</br>";
        Yii::app()->end();
	}

	public function actionIndex($pageNum=0) 
	{
		$this->function_id = 'YA01';
		Yii::app()->session['active_func'] = $this->function_id;
		
		$model = new MonthlyList;
		if (isset($_POST['MonthlyList'])) {
			$model->attributes = $_POST['MonthlyList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['criteria_ya01']) && !empty($session['criteria_ya01'])) {
				$criteria = $session['criteria_ya01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

	public function actionIndexc($pageNum=0) 
	{
		$this->function_id = 'YA02';
		Yii::app()->session['active_func'] = $this->function_id;
		
		$model = new MonthlyConfList;
		if (isset($_POST['MonthlyConfList'])) {
			$model->attributes = $_POST['MonthlyConfList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['criteria_ya02']) && !empty($session['criteria_ya02'])) {
				$criteria = $session['criteria_ya02'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('indexc',array('model'=>$model));
	}

	public function actionIndexa($pageNum=0) 
	{
		$this->function_id = 'YA03';
		Yii::app()->session['active_func'] = $this->function_id;
		
		$model = new MonthlyApprList;
		if (isset($_POST['MonthlyApprList'])) {
			$model->attributes = $_POST['MonthlyApprList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['criteria_ya03']) && !empty($session['criteria_ya03'])) {
				$criteria = $session['criteria_ya03'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('indexa',array('model'=>$model));
	}

	public function actionAccept()
	{
		if (isset($_POST['MonthlyForm'])) {
			$model = new MonthlyForm($_POST['MonthlyForm']['scenario']);
			$model->attributes = $_POST['MonthlyForm'];
			$model->scenario = 'accept';
			if ($model->validate()) {
				$model->accept();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Acceptance Done'));
				$this->redirect(Yii::app()->createUrl('monthly/view',array('index'=>$model->id,'rtn'=>$model->listform)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,'rtn'=>$model->listform));
			}
		}
	}

	public function actionAcceptm()
	{
		if (isset($_POST['MonthlyForm'])) {
			$model = new MonthlyForm($_POST['MonthlyForm']['scenario']);
			$model->attributes = $_POST['MonthlyForm'];
			if ($model->validate()) {
				$model->acceptm();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Acceptance Done'));
				$this->redirect(Yii::app()->createUrl('monthly/view',array('index'=>$model->id,'rtn'=>$model->listform)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,'rtn'=>$model->listform));
			}
		}
	}

	public function actionReject()
	{
		if (isset($_POST['MonthlyForm'])) {
			$model = new MonthlyForm($_POST['MonthlyForm']['scenario']);
			$model->attributes = $_POST['MonthlyForm'];
			if ($model->validate()) {
				$model->reject();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Rejection Done'));
				$this->redirect(Yii::app()->createUrl('monthly/view',array('index'=>$model->id,'rtn'=>$model->listform)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionRejectm()
	{
		if (isset($_POST['MonthlyForm'])) {
			$model = new MonthlyForm($_POST['MonthlyForm']['scenario']);
			$model->attributes = $_POST['MonthlyForm'];
			if ($model->validate()) {
				$model->rejectm();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Rejection Done'));
				$this->redirect(Yii::app()->createUrl('monthly/view',array('index'=>$model->id,'rtn'=>$model->listform)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionResubmit()
	{
		if (isset($_POST['MonthlyForm'])) {
			$model = new MonthlyForm($_POST['MonthlyForm']['scenario']);
			$model->attributes = $_POST['MonthlyForm'];
			$model->scenario = 'resubmit';
			if ($model->validate()) {
				$model->resubmit();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Submission Done'));
				$this->redirect(Yii::app()->createUrl('monthly/edit',array('index'=>$model->id,'rtn'=>$model->listform)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionSubmit()
	{
		if (isset($_POST['MonthlyForm'])) {
			$model = new MonthlyForm($_POST['MonthlyForm']['scenario']);
			$model->attributes = $_POST['MonthlyForm'];
			$model->scenario = 'submit';
			if ($model->validate()) {
				$model->submit();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Submission Done'));
				$this->redirect(Yii::app()->createUrl('monthly/edit',array('index'=>$model->id,'rtn'=>$model->listform)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionSave()
	{
		if (isset($_POST['MonthlyForm'])) {
			$model = new MonthlyForm($_POST['MonthlyForm']['scenario']);
			$model->attributes = $_POST['MonthlyForm'];
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('monthly/edit',array('index'=>$model->id,'rtn'=>$model->listform)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionView($index, $rtn='index')
	{
		$this->function_id = $rtn=='indexa' ? 'YA03' : ($rtn=='indexc' ? 'YA02' : 'YA01') ;
		Yii::app()->session['active_func'] = $this->function_id;

		$model = new MonthlyForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$model->listform = $rtn;
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function actionEdit($index, $rtn='index')
	{
		$this->function_id = $rtn=='indexa' ? 'YA03' : ($rtn=='indexc' ? 'YA02' : 'YA01') ;
		Yii::app()->session['active_func'] = $this->function_id;

		$model = new MonthlyForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$model->listform = $rtn;
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function actionFileupload($doctype) {
		$model = new MonthlyForm();
		if (isset($_POST['MonthlyForm'])) {
			$model->attributes = $_POST['MonthlyForm'];
			
			$id = $model->id;
			$docman = new DocMan($doctype,$id,get_class($model));
			$docman->masterId = $model->docMasterId[strtolower($doctype)];
			if (isset($_FILES[$docman->inputName])) $docman->files = $_FILES[$docman->inputName];
			$docman->fileUpload();
			echo $docman->genTableFileList(false);
		} else {
			echo "NIL";
		}
	}
	
	public function actionFileRemove($doctype) {
		$model = new MonthlyForm();
		if (isset($_POST['MonthlyForm'])) {
			$model->attributes = $_POST['MonthlyForm'];
			$docman = new DocMan($doctype,$model->id,get_class($model));
			$docman->masterId = $model->docMasterId[strtolower($doctype)];
			$docman->fileRemove($model->removeFileId[strtolower($doctype)]);
			echo $docman->genTableFileList(false);
		} else {
			echo "NIL";
		}
	}
	
	public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
		$sql = "select city from opr_monthly_hdr where id = $docId";
		$row = Yii::app()->db->createCommand($sql)->queryRow();
		if ($row!==false) {
			$citylist = Yii::app()->user->city_allow();
			if (strpos($citylist, $row['city']) !== false) {
				$docman = new DocMan($doctype,$docId,'MonthlyForm');
				$docman->masterId = $mastId;
				$docman->fileDownload($fileId);
			} else {
				throw new CHttpException(404,'Access right not match.');
			}
		} else {
				throw new CHttpException(404,'Record not found.');
		}
	}

//	public function actionExportexcel() {
//		if (isset($_POST['MonthlyForm'])) {
//			$model = new MonthlyForm($_POST['MonthlyForm']['scenario']);
//			$model->attributes = $_POST['MonthlyForm'];
//			$model->exportExcel();
//		}
//	}
	
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='monthly-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('YA01');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('YA01');
	}

	public static function allowReadWriteC() {
		return Yii::app()->user->validRWFunction('YA02');
	}
	
	public static function allowReadOnlyC() {
		return Yii::app()->user->validFunction('YA02');
	}

	public static function allowReadWriteA() {
		return Yii::app()->user->validRWFunction('YA03');
	}
	
	public static function allowReadOnlyA() {
		return Yii::app()->user->validFunction('YA03');
	}

}
