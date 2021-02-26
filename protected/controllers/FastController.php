<?php


class FastController extends Controller
{
	public $function_id='YS04';
	
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
                'actions'=>array('audit','edit','reject','save','backward','notice'),
                'expression'=>array('FastController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('FastController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('YS04');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('YS04');
    }
	public function actionIndex($pageNum=0) 
	{
		$model = new FastList;
		if (isset($_POST['FastList'])) {
			$model->attributes = $_POST['FastList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['fast_ya01']) && !empty($session['fast_ya01'])) {
				$criteria = $session['fast_ya01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}



    public function actionSave()
    {
        if (isset($_POST['FastForm'])) {
            $model = new FastForm($_POST['FastForm']['scenario']);
            $model->attributes = $_POST['FastForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('fast/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

//通知
    public function actionNotice()
    {
        if (isset($_POST['FastForm'])) {
            $model = new FastForm("notice");
            $model->attributes = $_POST['FastForm'];
            if ($model->validate()) {
                $model->scenario = "edit";
                $model->saveData();
                $model->notice();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done and Sent Notification'));
                $this->redirect(Yii::app()->createUrl('fast/edit',array('index'=>$model->id)));
            } else {
                $model->scenario = "edit";
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }
//接受
    public function actionAudit()
    {
        if (isset($_POST['FastForm'])) {
            $model = new FastForm("audit");
            $model->attributes = $_POST['FastForm'];
            if ($model->validate()) {
                $model->saveData();
                $model->scenario = "edit";
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('fast/edit',array('index'=>$model->id)));
            } else {
                $model->scenario = "edit";
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }
//拒絕
    public function actionReject()
    {
        if (isset($_POST['FastForm'])) {
            $model = new FastForm("reject");
            $model->attributes = $_POST['FastForm'];
            if ($model->validate()) {
                $model->saveData();
                $model->scenario = "edit";
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('fast/edit',array('index'=>$model->id)));
            } else {
                $model->scenario = "edit";
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionEdit($index)
    {
        $model = new FastForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }
    public function actionView($index)
    {
        $model = new FastForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionBackward()
    {
        if (isset($_POST['FastForm'])) {
            $model = new FastForm("backward");
            $model->attributes = $_POST['FastForm'];
            if($model->backward()){
                Dialog::message(Yii::t('dialog','Information'), Yii::t('procurement','Backward Done'));
            }else{
                Dialog::message(Yii::t('dialog','Validation Message'), Yii::t('procurement','Backward Error'));
            }
            $model->scenario = "edit";
            $this->redirect(Yii::app()->createUrl('fast/edit',array('index'=>$model->id)));
        }
    }
}
