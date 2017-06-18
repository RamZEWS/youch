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
use api\models\CommentRating;
use common\models\forms\ChangePasswordForm;
use common\models\forms\ChangeProfileForm;
use common\models\forms\ChangeProfileAlertsForm;
use common\models\forms\ImageBase64Form;
use common\models\forms\UploadForm;
use yii\web\UploadedFile;
use yii\data\ActiveDataProvider;

class CommentController extends BaseAuthController {

    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'actions' => ['user', 'to-user', 'index'],
                    'allow' => true,
                ],
                [
                    'actions' => ['add-mark', 'image'],
                    'allow' => true,
                    'roles' => ['@']
                ],
            ],
        ];
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'index' => ['GET'],
                'user' => ['GET'],
                'to-user' => ['GET'],
                'add-mark' => ['POST'],
                'image' => ['POST']
            ],
        ];

        return $behaviors;
    }

    public function actionUser($id) {
        $activeData = new ActiveDataProvider([
            'query' => ContentComment::find(),
            'pagination' => [
                'defaultPageSize' => $perpage,
                'validatePage' => false
            ],
        ]);
        $activeData->query->joinWith(['user'], true)
                          ->where(['user.username' => $id])
                          ->orderBy(['created_at' => SORT_DESC]);

        $pagination = $this->getPagination($activeData->getTotalCount(), $page, $perpage);
        return ['pagination' => $pagination, 'models' => $activeData->getModels()];
    }

    public function actionToUser($id) {
        $activeData = new ActiveDataProvider([
            'query' => ContentComment::find(),
            'pagination' => [
                'defaultPageSize' => $perpage,
                'validatePage' => false
            ],
        ]);
        $activeData->query->joinWith(['content', 'content.user'], true)
                          ->where(['user.username' => $id])
                          ->orderBy(['created_at' => SORT_DESC]);

        $pagination = $this->getPagination($activeData->getTotalCount(), $page, $perpage);
        return ['pagination' => $pagination, 'models' => $activeData->getModels()];
    }

    public function actionIndex($event_id, $page = 1, $perpage = 10) {
        $activeData = new ActiveDataProvider([
            'query' => ContentComment::find(),
            'pagination' => [
                'defaultPageSize' => $perpage,
                'validatePage' => false
            ],
        ]);
        $activeData->query->where(['content_id' => $event_id, 'comment_id' => null])->orderBy(['created_at' => SORT_DESC]);

        $pagination = $this->getPagination($activeData->getTotalCount(), $page, $perpage);
        return ['pagination' => $pagination, 'models' => $activeData->getModels()];
    }

    public function actionAddMark(){
        $bodyParams = Yii::$app->getRequest()->getBodyParams();
        $model = new CommentRating();
        $model->load($bodyParams, '');
        $existed = CommentRating::find()->where(['user_id' => Yii::$app->user->id, 'comment_id' => $model->comment_id])->one();
        if(!$existed) {
            if ($model->validate() && $model->save()) {
                return ContentComment::findOne($model->comment_id);
            }
        } else {
            $model->addError('comment_id', 'You have already added the mark');
        }
        return $model;
    }

    public function actionImage($id){
        $comment = ContentComment::findOne($id);
        if($comment) {
            $image = null;
            $model = new UploadForm();
            $model->file = UploadedFile::getInstanceByName('file');
            if ($model->file && $model->validate()) {
                $image = $model->saveImage('/upload/comments/');
            }
            if(!$image) {
                return $model;
            } else {
                if($comment->file_url) {
                    $file = implode('', [$_SERVER['DOCUMENT_ROOT'], $comment->file_base_url, $comment->file_url]);
                    if(file_exists($file)) {
                        unlink($file);
                    }
                }
                $comment->file_base_url = $image['base_url'];
                $comment->file_url = $image['file_name'];
                $comment->save();
                return $comment;
            }
        }
    }
}