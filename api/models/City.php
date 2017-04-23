<?php

namespace api\models;


use common\models\City as CommonCity;
/**
 * City model
 */
class City extends CommonCity {

    public function fields() {
        return [
            'name',
            'city',
            'google_id',
            'lat',
            'lng'
        ];
    }
}
