<?php

namespace api\models;

use common\models\BlackList as CommonBlackList;

class BlackList extends CommonBlackList {

    public function fields() {
        return [
            'id',
            'user',
            'blocked',
            'created_at',
            'updated_at'
        ];
    }

    public function getBlocked(){
        return $this->hasOne(User::className(), ['id' => 'block_id']);
    }

    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
