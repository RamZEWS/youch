<?php

namespace api\modules\oauth2\controllers;

use api\models\User;
use filsh\yii2\oauth2server\models\OauthAccessTokens;
use Yii;

class RestController extends \filsh\yii2\oauth2server\controllers\RestController
{
    public function actionToken()
    {
        $response = $this->module->getServer()->handleTokenRequest();
        $parameters = $response->getParameters();
        /*
         * Имитируем разлогин юзера на одном устройстве, если получен токен на другом.
         * При выдаче нового токена удаляем все предыдущие выданные
         */
        if (array_key_exists('access_token', $parameters)) {
            $token = $parameters['access_token'];
            $username = Yii::$app->request->post('username');
            if ($username && $token) {
                $user = User::findOne(['username' => $username]);
                OauthAccessTokens::deleteAll("user_id = $user->id and access_token <> '$token'");
            }
        }
        return $response->getParameters();
    }
}