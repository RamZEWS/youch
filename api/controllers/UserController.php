<?php
namespace api\controllers;

use api\components\ApiRestController;

class UserController extends ApiRestController {
    public $modelClass = 'api\models\User';
}