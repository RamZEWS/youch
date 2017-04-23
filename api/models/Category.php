<?php

namespace api\models;

use common\models\Category as CommonCategory;

class Category extends CommonCategory {

    public function fields() {
        return [
            'id',
            'name_ru',
            'name_en',
            'created_at',
            'updated_at'
        ];
    }
}
