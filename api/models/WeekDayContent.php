<?php

namespace api\models;

use common\models\WeekDayContent as CommonWeekDayContent;

class WeekDayContent extends CommonWeekDayContent {

    public function fields() {
        return [
            'id',
            'day',
            'content',
            'from',
            'to',
            'created_at',
            'updated_at'
        ];
    }

    public function getDay(){
        return $this->hasOne(WeekDay::className(), ['id' => 'week_day_id']);
    }

    public function getContent() {
        return $this->hasOne(Content::className(), ['id' => 'content_id']);
    }
}
