<?php

class RulesController extends Controller
{
	public $function_id='YG06';

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
                'actions'=>array('new','edit','delete','save'),
                'expression'=>array('RulesController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('RulesController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('YG06');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('YG06');
    }
	public function actionIndex($pageNum=0) 
	{
		$model = new RulesList;
		if (isset($_POST['RulesList'])) {
			$model->attributes = $_POST['RulesList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['rules_ya01']) && !empty($session['rules_ya01'])) {
				$criteria = $session['rules_ya01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}


	public function actionSave()
	{
		if (isset($_POST['RulesForm'])) {
			$model = new RulesForm($_POST['RulesForm']['scenario']);
			$model->attributes = $_POST['RulesForm'];
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('rules/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionView($index)
	{
		$model = new RulesForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

    public function actionNew()
    {
        $model = new RulesForm('new');
        $this->render('form',array('model'=>$model,));
    }

	public function actionEdit($index)
	{
		$model = new RulesForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

    public function actionDelete()
    {
        $model = new RulesForm('delete');
        if (isset($_POST['RulesForm'])) {
            $model->attributes = $_POST['RulesForm'];
            if($model->deleteValidate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('rules/index'));
            }else{
                $model->scenario = "edit";
                Dialog::message(Yii::t('dialog','Validation Message'), Yii::t("procurement","This category has been used and cannot be deleted"));
                $this->render('form',array('model'=>$model,));
            }
        }else{
            $this->redirect(Yii::app()->createUrl('rules/index'));
        }
    }

}
