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
        return [
            'id',
            'title',
            'description',
            'user',
            'rating',
            'categories',
            'days',
            'price_from',
            'price_to',
            'is_free',
            'is_tour',
            'date_from',
            'date_to',
            'time_from',
            'time_to',
            'site',
            'phone',
            'city',
            'state',
            'file',
            'counts',
            'created_at',
            'updated_at'
        ];
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

    public function getCity(){
        return $this->hasOne(City::className(), ['id' => 'city_id']);
    }

    public function getCategories() {
        return $this->hasMany(Category::className(), ['id' => 'category_id'])
            ->viaTable(ContentCategory::tableName(), ['content_id' => 'id']);
    }

    public function getDays() {
        $days = [];
        $contentDays = WeekDayContent::find()->where(['content_id' => $this->id])->all();
        foreach($contentDays as $d) $days[$d->day->code] = ['name' => $d->day->name_en, 'from' => $d->from, 'to' => $d->to];
        return $days;
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
