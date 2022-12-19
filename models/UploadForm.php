<?php


namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    /**
     * @var UploadedFile[]
     */
    public $file;

    public function rules()
    {
        return [
            [['file'], 'file', 'checkExtensionByMimeType' => false, 'extensions' => 'csv', 'maxSize' => 1024 * 1024 * 1024 * 5  ],
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $fileName = 'uploads/' . $this->generateKey(8) . '.' . $this->file->extension;
            $this->file->saveAs($fileName);
            return $fileName;
        } else {
            return false;
        }
    }

    protected function generateKey($length = 16) {
        $max = ceil($length / 40);
        $random = '';
        for ($i = 0; $i < $max; $i ++) {
            $random .= sha1(microtime(true).mt_rand(10000,90000));
        }
        return substr($random, 0, $length);
    }
}