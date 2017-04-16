<?php
namespace api\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use api\models\User;

class UserController extends BaseAuthController {

    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'actions' => ['profile', 'avatar', 'delete-avatar'],
                    'allow' => true,
                    'roles' => ['@'],
                ]
            ],
        ];

        return $behaviors;
    }

    public function actionProfile()
    {
        if (Yii::$app->getRequest()->isGet) {
            return User::findOne(Yii::$app->user->id);
        }
        $bodyParams = Yii::$app->getRequest()->getBodyParams();
        $model = User::findOne(Yii::$app->user->id);
        $model->load($bodyParams, '');
        $model->save();
        return $model;
    }

    public function actionAvatar($fileparam)
    {
        /*$file = \yii\web\UploadedFile::getInstanceByName($fileparam);
        if (!$file || !$file->size || $file->error) {
            return 'Error: Upload error';
        }
        if (!in_array($file->type, array('image/jpeg', 'image/png'))) {
            return 'Error: Invalid file type';
        }
        Yii::$app->user->identity->userpic = Yii::$app->fileStorage->save($file);
        Yii::$app->user->identity->save(false);
        return User::findOne(Yii::$app->user->identity->id);*/
    }

    public function actionDeleteAvatar($userpic)
    {
        /*if (!Yii::$app->fileStorage->delete($userpic)) {
            throw new HttpException(400);
        }
        Yii::$app->user->identity->userpic = null;
        Yii::$app->user->identity->save(false);
        return User::findOne(Yii::$app->user->identity->id);*/
    }
}