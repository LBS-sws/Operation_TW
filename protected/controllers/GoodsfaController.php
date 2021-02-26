<?php

class GoodsfaController extends Controller
{
	public $function_id='YG05';

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
                'expression'=>array('GoodsfaController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('GoodsfaController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('YG05');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('YG05');
    }
    public function actionIndex($pageNum=0)
    {
        $model = new GoodsFaList;
        if (isset($_POST['GoodsFaList'])) {
            $model->attributes = $_POST['GoodsFaList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['goodsfa_ya01']) && !empty($session['goodsfa_ya01'])) {
                $criteria = $session['goodsfa_ya01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionSave()
    {
        if (isset($_POST['GoodsFaForm'])) {
            $model = new GoodsFaForm($_POST['GoodsFaForm']['scenario']);
            $model->attributes = $_POST['GoodsFaForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('goodsfa/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionView($index)
    {
        $model = new GoodsFaForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionNew()
    {
        $model = new GoodsFaForm('new');
        $this->render('form',array('model'=>$model,));
    }

    public function actionEdit($index)
    {
        $model = new GoodsFaForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionDelete()
    {
        $model = new GoodsFaForm('delete');
        if (isset($_POST['GoodsFaForm'])) {
            $model->attributes = $_POST['GoodsFaForm'];
            if($model->deleteValidate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('goodsfa/index'));
            }else{
                $model->scenario = "edit";
                Dialog::message(Yii::t('dialog','Validation Message'), Yii::t("procurement","This goods has been used and cannot be deleted"));
                $this->render('form',array('model'=>$model,));
            }
        }else{
            $this->redirect(Yii::app()->createUrl('goodsfa/index'));
        }
    }

}
