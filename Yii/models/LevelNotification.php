<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "level_notification".
 *
 * @property int $id
 * @property int|null $level_id
 * @property int|null $notification_id
 * @property int|null $project_id
 * @property string|null $settings
 *
 * @property Level $level
 * @property Notification $notification
 * @property Project $project
 */
class LevelNotification extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'level_notification';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['level_id', 'notification_id', 'project_id'], 'integer'],
            [['settings'], 'string'],
            [['level_id'], 'exist', 'skipOnError' => true, 'targetClass' => Level::className(), 'targetAttribute' => ['level_id' => 'id']],
            [['notification_id'], 'exist', 'skipOnError' => true, 'targetClass' => Notification::className(), 'targetAttribute' => ['notification_id' => 'id']],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::className(), 'targetAttribute' => ['project_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'level_id' => 'Level ID',
            'notification_id' => 'Notification ID',
            'project_id' => 'Project ID',
            'settings' => 'Settings',
        ];
    }

    /**
     * Gets query for [[Level]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLevel()
    {
        return $this->hasOne(Level::className(), ['id' => 'level_id']);
    }

    /**
     * Gets query for [[Notification]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNotification()
    {
        return $this->hasOne(Notification::className(), ['id' => 'notification_id']);
    }

    /**
     * Gets query for [[Project]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['id' => 'project_id']);
    }

    public function getDropDownNotification()
    {
        $result = [];
        $notifications = Notification::find()->all();
        foreach ($notifications as $notification) {
            $result[$notification->getAttribute('id')] = $notification->getAttribute('name');
        }

        return $result;
    }

    public function getDropDownLevel()
    {
        $result = [];
        $levels = Level::find()->all();
        foreach ($levels as $level) {
            $result[$level->getAttribute('id')] = $level->getAttribute('key');
        }

        return $result;
    }
}
