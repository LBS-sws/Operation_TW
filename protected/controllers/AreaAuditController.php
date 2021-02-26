<?php

class AreaAuditController extends Controller
{
	public $function_id='YD06';

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
                'actions'=>array('edit','audit','reject'),
                'expression'=>array('AreaAuditController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('AreaAuditController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('YD06');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('YD06');
    }
	public function actionIndex($pageNum=0) 
	{
		$model = new AreaAuditList;
		if (isset($_POST['AreaAuditList'])) {
			$model->attributes = $_POST['AreaAuditList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['areaAudit_ya01']) && !empty($session['areaAudit_ya01'])) {
				$criteria = $session['areaAudit_ya01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

	public function actionAudit()
	{
		if (isset($_POST['AreaAuditForm'])) {
			$model = new AreaAuditForm("audit");
			$model->attributes = $_POST['AreaAuditForm'];
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('areaAudit/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('areaAudit/edit',array('index'=>$model->id)));
			}
		}
	}

	public function actionReject()
	{
		if (isset($_POST['AreaAuditForm'])) {
			$model = new AreaAuditForm("reject");
			$model->attributes = $_POST['AreaAuditForm'];
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('areaAudit/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->redirect(Yii::app()->createUrl('areaAudit/edit',array('index'=>$model->id)));
			}
		}
	}

	public function actionView($index)
	{
		$model = new AreaAuditForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

	public function actionEdit($index)
	{
		$model = new AreaAuditForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

}
