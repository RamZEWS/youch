<?php

namespace api\models;

use common\models\ContentCategory as CommonContentCategory;

class ContentCategory extends CommonContentCategory {

    public function fields() {
        return [
            'id',
            'category_id',
            'content_id',
            'created_at',
            'updated_at'
        ];
    }

    public function getCategory() {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    public function getContent() {
        return $this->hasOne(Content::className(), ['id' => 'content_id']);
    }
}
