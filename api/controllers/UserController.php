<?php
namespace api\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class UserController extends BaseAuthController {

    public $modelClass = 'api\models\User';

    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'actions' => ['profile'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
                [
                    'actions' => ['index'],
                    'allow' => true,
                    'roles' => ['?']
                ]
            ],
        ];
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'profile' => ['get'],
            ],
        ];

        return $behaviors;
    }

    public function actionProfile() {
        return User::findOne(Yii::$app->user->id);
        /*$bodyParams = Yii::$app->getRequest()->getBodyParams();
        $model = User::findOne(Yii::$app->user->id);
        /*$model->load($bodyParams, '');
        $model->username = Yii::$app->user->identity->username;
        $model->save();*/
        //return $model;
    }

}