<?php
namespace common\models\forms;

use Yii;
use yii\base\Model;
use common\models\Category;
use common\models\Content;
use common\models\ContentCategory;
use common\models\TourPeriod;
use common\models\WeekDay;
use common\models\WeekDayContent;

class TourForm extends Model
{
    public $id;
    public $title;
    public $description;
    public $price;
    public $is_free;
    public $is_tour;
    public $period;
    public $site;
    public $phone;
    public $city;
    public $category;

    public $content;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'description', 'site', 'phone'], 'string'],
            [['is_free', 'is_tour', 'id', 'period'], 'integer'],
            [['price'], 'double'],
            [['city'], 'validateCity'],
            [['category'], 'validateCategory']
        ];
    }

    public function saveContent(){
        if ($this->validate()) {
            $this->content = $this->findContent();
            if($this->content) {
                $this->content->title = $this->title;
                $this->content->description = $this->description;
                $this->content->price_from = $this->price;
                $this->content->is_free = $this->is_free;
                $this->content->is_tour = true;
                $this->content->period = $this->period;
                $this->content->site = $this->site;
                $this->content->phone = $this->phone;
                $this->content->city_id = $this->city ? $this->city->id : null;

                if($this->content->validate() && $this->content->save()) {
                    $this->savePeriods();
                    $this->saveCategories();
                    $this->saveHours();
                }
            }
        }
        return $this;
    }

    public function findContent(){
        if (($model = Content::findOne($this->id)) !== null) {
            return $model;
        } else {
            return new Content();
        }
    }

    public function validateCity($attribute, $params){
        $bodyParams = Yii::$app->getRequest()->getBodyParams();
        $model = new GoogleCityForm();
        $model->load($bodyParams['city'], '');
        $this->city = $model->getOrCreate();
        return $this;
    }

    public function validateCategory($attribute, $params){
        $bodyParams = Yii::$app->getRequest()->getBodyParams();
        if(isset($bodyParams['category']['id'])) {
            $this->category = Category::findOne($bodyParams['category']['id']);    
        }
        return $this;
    }

    public function saveCategories(){
        ContentCategory::deleteAll(['content_id' => $this->content->id]);
        if($this->category){
            $new = new ContentCategory(['category_id' => $this->category->id, 'content_id' => $this->content->id]);
            $new->save();
        }
    }

    public function savePeriods(){
        TourPeriod::deleteAll(['tour_id' => $this->content->id]);
        $bodyParams = Yii::$app->getRequest()->getBodyParams();
        $dates = $bodyParams['dates'];
        foreach($dates as $date) {
            $new = new TourPeriod();
            $new->date_start = date('Y-m-d H:i:s', strtotime($date));
            $new->tour_id = $this->content->id;
            $new->save();
        }   
    }

    public function saveHours(){
        WeekDayContent::deleteAll(['content_id' => $this->content->id]);
        $bodyParams = Yii::$app->getRequest()->getBodyParams();
        $hours = $bodyParams['hours'];
        foreach($hours as $code => $hour) {
            $weekday = WeekDay::find()->where(['code' => $code])->one();
            if($weekday) {
                $new = new WeekDayContent();
                $new->from = date('H:i', strtotime($hour['from']));
                $new->to = date('H:i', strtotime($hour['to']));
                $new->week_day_id = $weekday->id;
                $new->content_id = $this->content->id;
                $new->save();
            }
        }   
    }
}
