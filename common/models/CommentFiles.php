<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * @property integer $id
 * @property integer $comment_id
 * @property string $file_base_url
 * @property string $file_url
 * @property integer $created_at
 * @property integer $updated_at
 */
class CommentFiles extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%comment_files}}';
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
            [['comment_id'], 'integer'],
            [['file_base_url', 'file_url'], 'string']
        ];
    }

    public function getComment() {
        return $this->hasOne(ContentComment::className(), ['id' => 'comment_id']);
    }
}
