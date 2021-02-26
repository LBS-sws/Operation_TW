<?php

class CargoCostUserController extends Controller
{
	public $function_id='YD07';

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
                'actions'=>array('index','view'),
                'expression'=>array('CargoCostUserController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('YD07');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('YD07');
    }
	public function actionIndex($pageNum=0,$username='',$month='null',$year='')
	{
		$model = new CargoCostUserList;
		if (isset($_POST['CargoCostUserList'])) {
			$model->attributes = $_POST['CargoCostUserList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['cargoCostUser_ya01']) && !empty($session['cargoCostUser_ya01'])) {
				$criteria = $session['cargoCostUser_ya01'];
				$model->setCriteria($criteria);
			}
		}
        $model->setProSession($username,$year,$month);
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

    public function actionView($index)
    {
        $model = new CargoCostUserForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }
}
