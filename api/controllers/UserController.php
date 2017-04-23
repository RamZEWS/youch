<?php
namespace api\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use api\models\User;
use api\models\BlackList;
use common\models\forms\ChangePasswordForm;
use common\models\forms\ChangeProfileForm;
use common\models\forms\ChangeProfileAlertsForm;

class UserController extends BaseAuthController {

    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'actions' => ['profile', 'avatar', 'delete-avatar', 'change-password', 'change-profile', 'change-alerts', 'black-list'],
                    'allow' => true,
                    'roles' => ['@'],
                ]
            ],
        ];
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'profile' => ['GET'],
                'black-list' => ['GET'],
                'avatar' => ['POST'],
                'delete-avatar' => ['POST'],
                'change-password' => ['POST'],
                'change-profile' => ['POST'],
                'change-alerts' => ['POST'],
            ],
        ];

        return $behaviors;
    }

    public function actionProfile() {
        return User::findOne(Yii::$app->user->id);
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

    public function actionChangePassword(){
        $bodyParams = Yii::$app->getRequest()->getBodyParams();
        $model = new ChangePasswordForm();
        $model->load($bodyParams, '');
        if ($model->validate()) {
            return $model->changePassword();
        } else {
            return $model;
        }
    }

    public function actionChangeProfile(){
        $bodyParams = Yii::$app->getRequest()->getBodyParams();
        $model = new ChangeProfileForm();
        $model->load($bodyParams, '');
        if ($model->validate()) {
            return $model->changeProfile();
        } else {
            return $model;
        }
    }

    public function actionChangeAlerts(){
        $bodyParams = Yii::$app->getRequest()->getBodyParams();
        $model = new ChangeProfileAlertsForm();
        $model->load($bodyParams, '');
        if ($model->validate()) {
            return $model->changeAlerts();
        } else {
            return $model;
        }
    }

    public function actionBlackList(){
        return BlackList::find()->where(['user_id' => Yii::$app->user->id])->select(['id', 'block_id', 'created_at', 'updated_at'])->all();
    }
}