<?php

class GoodsdoController extends Controller
{
	public $function_id='YG04';

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
                'expression'=>array('GoodsdoController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('GoodsdoController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('YG04');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('YG04');
    }
	public function actionIndex($pageNum=0)
	{
		$model = new GoodsDoList;
		if (isset($_POST['GoodsDoList'])) {
			$model->attributes = $_POST['GoodsDoList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['goodsdo_ya01']) && !empty($session['goodsdo_ya01'])) {
				$criteria = $session['goodsdo_ya01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}


	public function actionSave()
	{
		if (isset($_POST['GoodsDoForm'])) {
			$model = new GoodsDoForm($_POST['GoodsDoForm']['scenario']);
			$model->attributes = $_POST['GoodsDoForm'];
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('goodsdo/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionView($index)
	{
		$model = new GoodsDoForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

    public function actionNew()
    {
        $model = new GoodsDoForm('new');
        $this->render('form',array('model'=>$model,));
    }

	public function actionEdit($index)
	{
		$model = new GoodsDoForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

    public function actionDelete()
    {
        $model = new GoodsDoForm('delete');
        if (isset($_POST['GoodsDoForm'])) {
            $model->attributes = $_POST['GoodsDoForm'];
            if($model->deleteValidate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('goodsdo/index'));
            }else{
                $model->scenario = "edit";
                Dialog::message(Yii::t('dialog','Validation Message'), Yii::t("procurement","This goods has been used and cannot be deleted"));
                $this->render('form',array('model'=>$model,));
            }
        }else{
            $this->redirect(Yii::app()->createUrl('goodsdo/index'));
        }
    }

}
