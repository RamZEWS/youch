<?php

namespace api\models;

use common\models\ContentComment as CommonContentComment;

class ContentComment extends CommonContentComment {

    public function fields() {
        return [
            'id',
            'content',
            'user',
            'comment',
            'file',
            'rating',
            'ratings',
            'answers',
            'created_at',
            'updated_at'
        ];
    }

    public function getUser(){
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getContent() {
        return $this->hasOne(Content::className(), ['id' => 'content_id']);
    }

    public function getAnswers() {
        return $this->hasMany(self::className(), ['comment_id' => 'id']);
    }

    public function getRatings() {
        $models = CommentRating::find()->where(['comment_id' => $this->id])->orderBy(['rating' => SORT_DESC])->all();
        if($models) {
            $arr = [];
            $total = 0;
            foreach($models as $m){
                if(!isset($arr[$m->rating])) $arr[$m->rating] = 0;
                $arr[$m->rating] = $arr[$m->rating] + 1;
                $total++;
            }

            $result = [];
            foreach($arr as $rating => $count) {
                $percent = ($count * 100) / $total;
                $result[$rating] = ['count' => $count, 'percent' => $percent];
            }
            return $result;
        }
        return null;
    }
}
