<?php

namespace api\models;

use common\models\ContentComment as CommonContentComment;

class ContentComment extends CommonContentComment {

    public function fields() {
        return [
            'id',
            'content',
            'user',
            'comment',
            'created_at',
            'updated_at'
        ];
    }

    public function getUser(){
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getContent() {
        return $this->hasOne(Content::className(), ['id' => 'content_id']);
    }
}
