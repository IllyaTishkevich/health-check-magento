<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "notification".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $object_namespace
 *
 * @property LevelNotification[] $levelNotifications
 */
class Notification extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notification';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'object_namespace'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'object_namespace' => 'Object Namespace',
        ];
    }

    /**
     * Gets query for [[LevelNotifications]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLevelNotifications()
    {
        return $this->hasMany(LevelNotification::className(), ['notification_id' => 'id']);
    }
}
