<?php
namespace common\models\forms;

use Yii;
use yii\base\Model;
use common\models\City;

/**
 * Login form
 */
class GoogleCityForm extends Model
{
    public $city;
    public $name;
    public $id;
    public $lat;
    public $long;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['city', 'name', 'id'], 'string'],
            [['city', 'name', 'id'], 'required'],
            [['lat', 'long'], 'double']
        ];
    }

    public function getOrCreate(){
        if ($this->validate()) {
            $city = City::find()->where(['google_id' => $this->id])->one();
            if(!$city) {
                $city = new City([
                    'name' => $this->name,
                    'city' => $this->city,
                    'google_id' => $this->id,
                    'lat' => $this->lat,
                    'lng' => $this->long
                ]);
                if($city->validate()) {
                    $city->save();
                }
            }
            return $city;
        }
        return false;
    }
}
