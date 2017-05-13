<?php
namespace api\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use api\models\User;
use api\models\Content;
use api\models\BlackList;
use api\models\UserSubscription;
use api\models\ContentComment;
use common\models\forms\ChangePasswordForm;
use common\models\forms\ChangeProfileForm;
use common\models\forms\ChangeProfileAlertsForm;
use common\models\forms\ImageBase64Form;
use common\models\forms\UploadForm;
use yii\web\UploadedFile;

class CommentController extends BaseAuthController {

    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'actions' => ['user', 'to-user'],
                    'allow' => true,
                ],
            ],
        ];
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'user' => ['GET'],
                'to-user' => ['GET']
            ],
        ];

        return $behaviors;
    }

    public function actionUser($id) {
        return ContentComment::find()
                ->joinWith('user', true)
                ->where(['user.username' => $id])
                ->orderBy(['created_at' => SORT_DESC])
                ->all();
    }

    public function actionToUser($id) {
        return ContentComment::find()
                ->joinWith(['content', 'content.user'], true)
                ->where(['user.username' => $id])
                ->orderBy(['created_at' => SORT_DESC])
                ->all();
    }
}