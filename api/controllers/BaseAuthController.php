<?php

namespace api\controllers;

use yii\helpers\ArrayHelper;
use api\filters\auth\HttpBearerAuth;
use filsh\yii2\oauth2server\filters\ErrorToExceptionFilter;

class BaseAuthController extends \yii\rest\Controller
{
    public function verbs()
    {
        return ArrayHelper::merge(parent::verbs(), [
            'index' => ['GET'],
            'create' => ['POST'],
            'delete' => ['DELETE'],
            'update' => ['PUT', 'PATCH']
        ]);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'class' => HttpBearerAuth::className()
            ],
            'exceptionFilter' => [
                'class' => ErrorToExceptionFilter::className()
            ],
        ]);
    }
}