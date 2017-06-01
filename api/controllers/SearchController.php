<?php

namespace api\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use common\models\forms\EventForm;
use api\models\ContentCategory;
use api\models\Content;
use api\models\ContentRating;
use api\models\ContentComment;
use api\models\User;
use yii\data\ActiveDataProvider;
use yii\web\UploadedFile;
use common\models\forms\UploadForm;

class SearchController extends BaseAuthController {
    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'actions' => ['by-city'],
                    'allow' => true
                ]
            ],
        ];
        $behaviors['verbs'] = [
            'class' => VerbFilter::className(),
            'actions' => [
                'by-city' => ['GET'],
            ],
        ];

        return $behaviors;
    }

    public function actionByCity($id, $page = 1, $perpage = 10){
        $activeData = new ActiveDataProvider([
            'query' => Content::find(),
            'pagination' => [
                'defaultPageSize' => $perpage,
                'validatePage' => false
            ],
        ]);
        $activeData->query->joinWith('city', true)->where(['city.google_id' => $id, 'content.is_tour' => true]);
        return ['total' => $activeData->getTotalCount(), 'models' => $activeData->getModels()];
    }
}
