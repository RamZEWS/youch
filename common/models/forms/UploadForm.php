<?php
namespace common\models\forms;

use yii\base\Model;
use yii\web\UploadedFile;

/**
 * UploadForm is the model behind the upload form.
 */
class UploadForm extends Model
{
    /**
     * @var UploadedFile file attribute
     */
    public $file;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['file'], 'file', 'extensions' => 'gif, jpg, png'],
        ];
    }

    public function saveImage($path){
        $filename = implode('.', [md5(time()), $this->file->extension]);
        $this->file->saveAs($_SERVER['DOCUMENT_ROOT'] . $path . $filename);
        if(file_exists($_SERVER['DOCUMENT_ROOT'].$path.$filename)) {
            return [
                'base_url' => $path,
                'file_name' => $filename
            ];
        } else {
            $this->addError('file', 'File is not uploaded');    
        }
    }
}