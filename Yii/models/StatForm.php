<?php

namespace app\models;

use Yii;
use yii\base\Model;


/**
 * ContactForm is the model behind the contact form.
 */
class StatForm extends Model {

    public $logLevel;
    public $fromTime;
    public $toTime;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['logLevel', 'fromTime'], 'required'],
        ];
    }


}
