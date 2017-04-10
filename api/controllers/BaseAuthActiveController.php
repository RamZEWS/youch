<?php

namespace api\controllers;

use yii\helpers\ArrayHelper;
use api\filters\auth\HttpBearerAuth;
use filsh\yii2\oauth2server\filters\ErrorToExceptionFilter;

class BaseAuthActiveController extends \yii\rest\ActiveController
{
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