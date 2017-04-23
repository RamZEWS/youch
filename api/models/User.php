<?php
namespace api\models;

use common\models\User as CommonUser;
/**
 * User model
 */
class User extends CommonUser {
    public static $statuses = [
        self::STATUS_DELETED => 'deleted',
        self::STATUS_ACTIVE => 'active',
        self::STATUS_BANNED => 'inactive'
    ];

    public function fields() {
        return [
            'id',
            'username',
            'email',
            'first_name',
            'last_name',
            'state',
            'site',
            'avatar',
            'get_messages',
            'hide_events',
            'birthday',
            'about',
            'city'
        ];
    }

    public function getState(){
        return isset(self::$statuses[$this->status]) ? self::$statuses[$this->status] : "unknown";
    }

    public function getAvatar(){
        if($this->avatar_url) {
            return implode('/', [$this->avatar_base_url, $this->avatar_url]);
        }
        return null;
    }

    public function getCity(){
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }
}
