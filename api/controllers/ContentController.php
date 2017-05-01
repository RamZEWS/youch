<?php

namespace api\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use common\models\forms\ContentForm;
use api\models\Content;
use api\models\ContentRating;
use api\models\ContentComment;

class ContentController extends BaseAuthActiveController {
    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'actions' => ['index', 'view', 'get-comments'],
                    'allow' => true
                ],
                [
                    'actions' => ['save', 'add-comment', 'add-mark'],
                    'allow' => true,
                    'roles' => ['@']
                ],
                [
                    'actions' => ['delete'],
                    'allow' => true,
                    'roles' => ['admin']
                ],
            ],
        ];
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'index' => ['GET'],
                'get-comments' => ['GET'],
                'add-comment' => ['POST'],
                'add-mark' => ['POST'],
                'delete' => ['DELETE'],
                'update' => ['PUT', 'PATCH']
            ],
        ];

        return $behaviors;
    }

    public $modelClass = 'api\models\Content';

    public function actionSave(){
        $bodyParams = Yii::$app->getRequest()->getBodyParams();
        $model = new ContentForm();
        $model->load($bodyParams, '');
        if ($model->validate() && $model->saveContent()) {
            return Content::findOne($model->id);
        } else {
            return $model;
        }
    }

    public function actionAddComment(){
        $bodyParams = Yii::$app->getRequest()->getBodyParams();
        $model = new ContentComment();
        $model->load($bodyParams, '');
        if ($model->validate() && $model->save()) {
            return $this->actionGetComments($model->content_id);
        } else {
            return $model;
        }
    }

    public function actionAddMark(){
        $bodyParams = Yii::$app->getRequest()->getBodyParams();
        $model = new ContentRating();
        $model->load($bodyParams, '');
        $existed = ContentRating::find()->where(['user_id' => Yii::$app->user->id, 'content_id' => $model->content_id])->one();
        if(!$existed) {
            if ($model->validate() && $model->save()) {
                return Content::findOne($model->content_id);
            }
        } else {
            $model->addError('content_id', 'You have already added the mark');
        }
        return $model;
    }

    public function actionGetComments($id){
        return ContentComment::find()->select(['id', 'comment', 'user_id', 'created_at', 'updated_at'])->where(['content_id' => $id])->orderBy(['created_at' => SORT_DESC])->all();
    }
}