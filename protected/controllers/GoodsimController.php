<?php

class GoodsimController extends Controller
{
	public $function_id='YG01';

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
                'expression'=>array('GoodsimController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('GoodsimController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('YG01');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('YG01');
    }
	public function actionIndex($pageNum=0)
	{
		$model = new GoodsImList;
		if (isset($_POST['GoodsImList'])) {
			$model->attributes = $_POST['GoodsImList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['goodsim_ya01']) && !empty($session['goodsim_ya01'])) {
				$criteria = $session['goodsim_ya01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}


	public function actionSave()
	{
		if (isset($_POST['GoodsImForm'])) {
			$model = new GoodsImForm($_POST['GoodsImForm']['scenario']);
			$model->attributes = $_POST['GoodsImForm'];
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('goodsim/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionView($index)
	{
		$model = new GoodsImForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

    public function actionNew()
    {
        $model = new GoodsImForm('new');
        $this->render('form',array('model'=>$model,));
    }

	public function actionEdit($index)
	{
		$model = new GoodsImForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

    public function actionDelete()
    {
        $model = new GoodsImForm('delete');
        if (isset($_POST['GoodsImForm'])) {
            $model->attributes = $_POST['GoodsImForm'];
            if($model->deleteValidate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('goodsim/index'));
            }else{
                $model->scenario = "edit";
                Dialog::message(Yii::t('dialog','Validation Message'), Yii::t("procurement","This goods has been used and cannot be deleted"));
                $this->render('form',array('model'=>$model,));
            }
        }else{
            $this->redirect(Yii::app()->createUrl('goodsim/index'));
        }
    }

}
