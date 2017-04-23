<?php

namespace api\models;

use common\models\Content as CommonContent;

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
            'categories',
            'days',
            'price_from',
            'price_to',
            'is_free',
            'date_from',
            'date_to',
            'site',
            'phone',
            'city',
            'state',
            'file',
            'created_at',
            'updated_at'
        ];
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
        foreach($contentDays as $d) $days[$d->day->code] = ['from' => $d->from, 'to' => $d->to];
        return $days;
    }

    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}