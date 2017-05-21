<?php
namespace common\models\forms;

use Yii;
use yii\base\Model;
use common\models\Content;
use common\models\ContentCategory;
use common\models\WeekDay;
use common\models\WeekDayContent;
use yii\web\UploadedFile;

class ContentForm extends Model
{
    public $id;
    public $title;
    public $description;
    public $price_from;
    public $price_to;
    public $is_free;
    public $is_tour;
    public $date_from;
    public $date_to;
    public $time_from;
    public $time_to;
    public $site;
    public $phone;
    public $city;
    public $image;

    private $content;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'description', 'site', 'phone', 'time_from', 'time_to'], 'string'],
            [['is_free', 'is_tour', 'id'], 'integer'],
            [['price_from', 'price_to'], 'double'],
            [['date_from', 'date_to'], 'safe'],
            [['city'], 'validateCity']
        ];
    }

    public function saveContent(){
        if ($this->validate()) {
            $this->content = $this->findContent();
            if($this->content) {
                $this->content->title = $this->title;
                $this->content->description = $this->description;
                $this->content->price_from = $this->price_from;
                $this->content->price_to = $this->price_to;
                $this->content->is_free = $this->is_free;
                $this->content->is_free = $this->is_tour;
                $this->content->date_from = $this->date_from;
                $this->content->date_to = $this->date_to;
                $this->content->time_from = $this->time_from;
                $this->content->time_to = $this->time_to;
                $this->content->site = $this->site;
                $this->content->phone = $this->phone;
                $this->content->city_id = $this->city ? $this->city->id : null;

                $this->uploadImage();
                $this->content->file_base_url = isset($this->image['base_url']) ? $this->image['base_url'] : null;
                $this->content->file_url = isset($this->image['file_name']) ? $this->image['file_name'] : null;

                if($this->content->validate() && $this->content->save()) {
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

    public function saveCategories(){
        ContentCategory::deleteAll(['content_id' => $this->content->id]);
        $bodyParams = Yii::$app->getRequest()->getBodyParams();
        $categories = $bodyParams['category_ids'];
        foreach($categories as $cID) {
            $new = new ContentCategory(['category_id' => $cID, 'content_id' => $this->content->id]);
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
                $new = new WeekDayContent([
                    'week_day_id' => $weekday->id,
                    'content_id' => $this->content->id
                ]);
                $new->load($hour, '');
                $new->week_day_id = $weekday->id;
                $new->content_id = $this->content->id;
                $new->save();
            }
        }   
    }

    public function uploadImage(){
        $model = new UploadForm();
        $model->file = UploadedFile::getInstanceByName('file');
        if ($model->file && $model->validate()) {
            $this->image = $model->saveImage('/upload/content/');
        }
        if(!$this->image) {
            return $model;
        } else {
            $this->content = $this->findContent();
            if($this->content->file_url) {
                $file = implode('', [$_SERVER['DOCUMENT_ROOT'], $this->content->file_base_url, $this->content->file_url]);
                if(file_exists($file)) {
                    unlink($file);
                }
            }
        }
    }
}
