<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * @property integer $id
 * @property string $title
 * @property text $description
 * @property integer $status
 * @property integer $user_id
 * @property double $rating
 * @property double $price_from
 * @property double $price_to
 * @property integer $is_free
 * @property integer $is_tour
 * @property integer $period
 * @property string $period_type
 * @property datetime $date_from
 * @property datetime $date_to
 * @property string $time_from
 * @property string $time_to
 * @property string $site
 * @property string $phone
 * @property string $file_base_url
 * @property string $file_url
 * @property integer $city_id
 * @property integer $address_id
 * @property integer $created_at
 * @property integer $updated_at
 */
class Content extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 10;
    const STATUS_DELETED = 20;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%content}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'description', 'site', 'phone', 'file_base_url', 'file_url', 'time_from', 'time_to', 'period_type'], 'string'],
            [['user_id', 'city_id', 'address_id', 'status', 'period'], 'integer'],
            [['price_from', 'price_to', 'rating'], 'double'],
            [['date_from', 'date_to'], 'safe'],
            ['user_id', 'default', 'value' => Yii::$app->user->id],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            [['is_tour', 'is_free'], 'boolean']
        ];
    }

    public function getCategories() {
        return $this->hasOne(ContentCategory::className(), ['content_id' => 'id']);
    }

    public function getDays() {
        return $this->hasMany(WeekDayContent::className(), ['content_id' => 'id']);
    }

    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getFile(){
        if($this->file_base_url) {
            return implode('', [$this->file_base_url, $this->file_url]);
        }
        return null;
    }
}
