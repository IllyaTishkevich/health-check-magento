<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "project".
 *
 * @property int                 $id
 * @property string|null         $name
 * @property string|null         $auth_key
 *
 * @property LevelNotification[] $levelNotifications
 * @property Message[]           $messages
 * @property ProjectUser[]       $projectUsers
 */
class Project extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'auth_key'], 'string', 'max' => 16],
            [['project_url'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'name'        => 'Name',
            'auth_key'    => 'Auth Key',
            'project_url' => 'Project Url',
        ];
    }

    /**
     * Gets query for [[LevelNotifications]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLevelNotifications()
    {
        return $this->hasMany(LevelNotification::className(), ['project_id' => 'id']);
    }

    /**
     * Gets query for [[Messages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMessages()
    {
        return $this->hasMany(Message::className(), ['project_id' => 'id']);
    }

    /**
     * Gets query for [[ProjectUsers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectUsers()
    {
        return $this->hasMany(ProjectUser::className(), ['project_id' => 'id']);
    }
}
