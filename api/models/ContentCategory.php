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
}
