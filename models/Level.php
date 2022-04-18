<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "level".
 *
 * @property int $id
 * @property string|null $key
 *
 * @property LevelNotification[] $levelNotifications
 * @property Message[] $messages
 */
class Level extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'level';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['key'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
        ];
    }

    /**
     * Gets query for [[LevelNotifications]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLevelNotifications()
    {
        return $this->hasMany(LevelNotification::className(), ['level_id' => 'id']);
    }

    /**
     * Gets query for [[Messages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMessages()
    {
        return $this->hasMany(Message::className(), ['level_id' => 'id']);
    }
}
