<?php

namespace api\models;


use common\models\Address as CommonAddress;
/**
 * Address model
 */
class Address extends CommonAddress {

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
