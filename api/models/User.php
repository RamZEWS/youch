<?php
namespace api\models;

use common\models\User as CommonUser;
/**
 * User model
 */
class User extends CommonUser {
    public static $statuses = [
        self::STATUS_DELETED => 'Удален',
        self::STATUS_ACTIVE => 'Активен',
        self::STATUS_BANNED => 'Забанен'
    ];

    public function fields() {
        return [
            'id',
            'username',
            'email',
            'state',
            'cityname'
        ];
    }

    public function getState(){
        return isset(self::$statuses[$this->status]) ? self::$statuses[$this->status] : "Неизвестно";
    }

    public function getCityname(){
        return $this->city ? $this->city->name_ru : false;
    }
}
