<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "project".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $auth_key
 * @property string|null $url
 * @property string|null $prefix
 * @property int $owner
 * @property int $gmt
 * @property boolean $active
 * @property boolean $enable_server_check
 *
 * @property LevelNotification[] $levelNotifications
 * @property Message[]           $messages
 * @property ProjectUser[]       $projectUsers
 */
class Project extends \yii\db\ActiveRecord
{
    public function afterFind()

    {
        $GLOBALS['prefix'] = $this->prefix;

        parent::afterFind();

    }

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
            [['url'], 'string', 'max' => 64],
            [['prefix'], 'string', 'max' => 8],
            [['owner'], 'integer'],
            [['active', 'enable_server_check'], 'boolean']
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
            'auth_key' => 'Auth Key',
            'url' => 'Project Url',
            'owner' => 'Project Owner',
            'gmt' => 'Project Server GMT',
            'enable_server_check' => 'Enable Server Status Checker'
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

    public function insert($runValidation = true, $attributes = null)
    {
        parent::insert($runValidation, $attributes);

        $projectUser = new ProjectUser();
        $projectUser->setAttribute('project_id', $this->getAttribute('id'));
        $projectUser->setAttribute('user_id', Yii::$app->user->getId());
        $projectUser->save();
    }

    public function getId()
    {
        return $this->getAttribute('id');
    }

    public function getGmtList()
    {
        $result = [];
        for ($i = -12; $i <= 12; $i++)
        {
            if ($i <= 0) {
                $result[$i] = 'GMT '.$i;
            } else {
                $result[$i] = 'GMT +'.$i;
            }
        }

        return $result;
    }
}
