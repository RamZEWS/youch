<?php

namespace api\models;


use common\models\City as CommonCity;
/**
 * City model
 */
class City extends CommonCity {

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
