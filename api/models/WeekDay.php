<?php

namespace api\models;

use common\models\WeekDay as CommonWeekDay;

class WeekDay extends CommonWeekDay {

    public function fields() {
        return [
            'id',
            'name_ru',
            'name_en',
            'code',
            'created_at',
            'updated_at'
        ];
    }
}
