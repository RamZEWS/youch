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
use common\models\forms\ImageBase64Form;

class UserController extends BaseAuthController {

    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'actions' => ['profile', 'avatar', 'delete-avatar', 'change-password', 'change-profile', 'change-alerts', 'black-list', 'followers', 'followings', 'delete'],
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
                'followers' => ['GET'],
                'followings' => ['GET'],
                'avatar' => ['POST'],
                'delete-avatar' => ['POST'],
                'change-password' => ['POST'],
                'change-profile' => ['POST'],
                'change-alerts' => ['POST'],
                'delete' => ['DELETE']
            ],
        ];

        return $behaviors;
    }

    public function actionProfile() {
        return User::findOne(Yii::$app->user->id);
    }

    public function actionAvatar()
    {
        $bodyParams = Yii::$app->getRequest()->getBodyParams();
        $model = new ImageBase64Form([
            'base64string' => $bodyParams['image'],
            'field' => 'image'
        ]);
        $image = $model->saveImage('/upload/users/');
        if(!$image) {
            return $model;
        } else {
            $this->deleteAvatar();
            $identity = Yii::$app->user->identity;
            $identity->avatar_base_url = $image['base_url'];
            $identity->avatar_url = $image['file_name'];
            if($identity->save()) {
                return User::findOne(Yii::$app->user->id);
            }
        }
    }

    public function actionDeleteAvatar($userpic)
    {
        $this->deleteAvatar();
        Yii::$app->user->identity->avatar_base_url = null;
        Yii::$app->user->identity->avatar_url = null;
        Yii::$app->user->identity->save();
        return User::findOne(Yii::$app->user->id);
    }

    private function deleteAvatar(){
        $identity = Yii::$app->user->identity;
        $filepath = implode('', [$_SERVER['DOCUMENT_ROOT'], $identity->avatar_base_url, $identity->avatar_url]);
        if (file_exists($filepath)) {
            unlink($filepath);
        }
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

    public function actionFollowers(){
        return UserSubscription::find()->where(['follower_id' => Yii::$app->user->id])->select(['id', 'user_id', 'created_at', 'updated_at'])->all();
    }

    public function actionFollowing(){
        return UserSubscription::find()->where(['user_id' => Yii::$app->user->id])->select(['id', 'follower_id', 'created_at', 'updated_at'])->all();
    }

    public function actionDelete(){
        $user = User::findOne(Yii::$app->user->id);
        return $user->delete();
    }

}