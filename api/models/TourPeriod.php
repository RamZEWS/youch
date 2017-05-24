<?php

namespace api\models;

use common\models\TourPeriod as CommonTourPeriod;

class TourPeriod extends CommonTourPeriod {

    public function fields() {
        return [
            'id',
            'tour',
            'date_start',
            'created_at',
            'updated_at'
        ];
    }

    public function getTour() {
        return $this->hasOne(Content::className(), ['id' => 'tour_id']);
    }
}
