<?php
namespace api\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use filsh\yii2\oauth2server\filters\ErrorToExceptionFilter;
use filsh\yii2\oauth2server\filters\auth\CompositeAuth;
use common\models\forms\ResetPasswordForm;
use common\models\forms\SignupForm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use api\models\BlackList;
use api\models\UserSubscription;


class SubscriptionController extends BaseAuthController {
    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'actions' => ['block', 'remove-block', 'follow', 'remove-follow'],
                    'allow' => true,
                    'roles' => ['@']
                ]
            ],
        ];
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'block' => ['post'],
                'remove-block' => ['post'],
                'follow' => ['post'],
                'remove-follow' => ['post'],
            ],
        ];

        return $behaviors;
    }

    public function actionBlock() {
        $bodyParams = Yii::$app->getRequest()->getBodyParams();
        if(isset($bodyParams['user_id'])) {
            $existed = $this->findBlock($bodyParams['user_id']);
            if(!$existed) {
                $model = new BlackList(['block_id' => $bodyParams['user_id']]);
                if($model->validate()) {
                    $model->save();
                }
            }
        }
        return BlackList::find()->where(['user_id' => Yii::$app->user->id])->select(['id', 'block_id', 'created_at', 'updated_at'])->all(); 
    }
    public function actionRemoveBlock() {
        $bodyParams = Yii::$app->getRequest()->getBodyParams();
        if(isset($bodyParams['user_id'])) {
            $existed = $this->findBlock($bodyParams['user_id']);
            if($existed) {
                $existed->delete();
            }
        }
        return BlackList::find()->where(['user_id' => Yii::$app->user->id])->select(['id', 'block_id', 'created_at', 'updated_at'])->all();
    }
    private function findBlock($user_id){
        return BlackList::find()->where(['user_id' => Yii::$app->user->id, 'block_id' => $user_id])->one();
    }

    public function actionFollow() {
        $bodyParams = Yii::$app->getRequest()->getBodyParams();
        if(isset($bodyParams['user_id'])) {
            $existed = $this->findFollow($bodyParams['user_id']);
            if(!$existed) {
                $model = new UserSubscription(['follower_id' => $bodyParams['user_id']]);
                if($model->validate()) {
                    $model->save();
                }
            }
        }
        return UserSubscription::find()->where(['user_id' => Yii::$app->user->id])->select(['id', 'follower_id', 'created_at', 'updated_at'])->all();
    }
    public function actionRemoveFollow() {
        $bodyParams = Yii::$app->getRequest()->getBodyParams();
        if(isset($bodyParams['user_id'])) {
            $existed = $this->findFollow($bodyParams['user_id']);
            if($existed) {
                $existed->delete();
            }
        }
        return UserSubscription::find()->where(['user_id' => Yii::$app->user->id])->select(['id', 'follower_id', 'created_at', 'updated_at'])->all();
    }
    private function findFollow($user_id){
        return UserSubscription::find()->where(['user_id' => Yii::$app->user->id, 'follower_id' => $user_id])->one();
    }
}