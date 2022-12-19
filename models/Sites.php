<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sites".
 *
 * @property int $id
 * @property string|null $site_url
 * @property string|null $status_code
 * @property string|null $available
 * @property string|null $cron_status
 */
class Sites extends \yii\db\ActiveRecord
{
    const PENDING = 'pending';
    const COMPILTE = 'complite';
    const ERROR = 'error';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sites';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['site_url'], 'string', 'max' => 128],
            [['status_code'], 'string', 'max' => 5],
            [['available', 'cron_status'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'site_url' => 'Site Url',
            'status_code' => 'Status Code',
            'available' => 'Available',
            'cron_status' => 'Cron Status',
        ];
    }
}
