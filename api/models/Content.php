<?php

namespace api\models;

use common\models\Content as CommonContent;
use api\components\DateFormatter;

class Content extends CommonContent {
    public static $statuses = [
        self::STATUS_DELETED => 'deleted',
        self::STATUS_ACTIVE => 'active',
        self::STATUS_INACTIVE => 'inactive'
    ];

    public function fields() {
        $fields = [
            'id',
            'title',
            'description',
            'user',
            'rating',
            'category',
            'days',
            'is_free',
            'is_tour',
            'site',
            'phone',
            'city',
            'state',
            'file',
            'counts',
            'created_at',
            'updated_at'
        ];

        if($this->is_tour) {
            $fields[] = 'price';
            $fields[] = 'period';
            $fields[] = 'dates';
        } else {
            $fields[] = 'price_from';
            $fields[] = 'price_to';
            $fields[] = 'date_from';
            $fields[] = 'date_to';
            $fields[] = 'time_from';
            $fields[] = 'time_to';
        }

        return $fields;
    }

    public function __get($name) {
        if(in_array($name, ['is_free', 'is_tour'])){
            return (bool)$this->getAttribute($name);
        } else if(in_array($name, ['date_from', 'date_to'])){
            return DateFormatter::convert($this->getAttribute($name));
        } else if(in_array($name, ['time_from', 'time_to'])){
            return DateFormatter::convert($this->getAttribute($name), 'time');
        }
        return parent::__get($name);
    }

    public function getState(){
        return isset(self::$statuses[$this->status]) ? self::$statuses[$this->status] : "unknown";
    }

    public function getPrice(){
        return $this->price_from;
    }    

    public function getCity(){
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    public function getCategory() {
        return $this->hasOne(Category::className(), ['id' => 'category_id'])
            ->viaTable(ContentCategory::tableName(), ['content_id' => 'id']);
    }

    public function getDays() {
        $days = [];
        $contentDays = WeekDayContent::find()->where(['content_id' => $this->id])->all();
        foreach($contentDays as $d) $days[$d->day->code] = ['name' => $d->day->name_en, 'from' => $d->from, 'to' => $d->to];
        return $days;
    }

    public function getDates() {
        $dates = [];
        $contentDays = TourPeriod::find()->where(['tour_id' => $this->id])->all();
        foreach($contentDays as $d) {
            $start = date('c', strtotime($d->date_start));
            $end = date('c', strtotime('+ '.$this->period.' days', strtotime($start)));
            $dates[] = ['from' => $start, 'to' => $end];   
        }
        return $dates;
    }

    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getCounts(){
        return [
            'ratings' => ContentRating::find()->where(['content_id' => $this->id])->count(),
            'comments' => ContentComment::find()->where(['content_id' => $this->id])->count(),
        ];
    }
}
