<?php

class MonthlyrateController extends Controller 
{
	public $function_id = 'YA04';

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
				'actions'=>array('new','edit','save','delete'),
				'expression'=>array('MonthlyrateController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view'),
				'expression'=>array('MonthlyrateController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($pageNum=0) 
	{
		$model = new MonthlyRateList;
		if (isset($_POST['MonthlyRateList'])) {
			$model->attributes = $_POST['MonthlyRateList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['criteria_ya04']) && !empty($session['criteria_ya04'])) {
				$criteria = $session['criteria_ya04'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

	public function actionSave()
	{
		if (isset($_POST['MonthlyRateForm'])) {
			$model = new MonthlyRateForm($_POST['MonthlyRateForm']['scenario']);
			$model->attributes = $_POST['MonthlyRateForm'];
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('monthlyrate/edit',array('index'=>$model->city)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

    public function actionNew()
    {
        $model = new MonthlyRateForm('new');
        $this->render('form',array('model'=>$model,));
    }

	public function actionView($index)
	{
		$model = new MonthlyRateForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function actionEdit($index)
	{
		$model = new MonthlyRateForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function actionDelete()
	{
		$model = new MonthlyRateForm('delete');
		if (isset($_POST['MonthlyRateForm'])) {
			$model->attributes = $_POST['MonthlyRateForm'];
			$model->saveData();
			Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
			$this->redirect(Yii::app()->createUrl('monthlyrate/index'));
		}
	}

	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('YA04');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('YA04');
	}
}
