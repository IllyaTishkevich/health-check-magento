<?php

namespace app\models;

use Yii;
use app\models\Notifications\Handler;

/**
 * This is the model class for table "message".
 *
 * @property int         $id
 * @property int|null    $level_id
 * @property string|null $message
 * @property string|null $events
 * @property string|null $trace
 * @property string|null $create
 * @property string|null $ip
 * @property string|null $user_id
 * @property string|null $user_agent
 * @property string|null $url
 *
 * @property Level       $level
 * @property Project     $project
 */
class JsMessage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::getPrefix() . '_js_log';
    }

    protected static function getPrefix()
    {
        return $GLOBALS['prefix'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['level_id'], 'integer'],
            [['message'], 'string'],
            [['create'], 'safe'],
            [['ip'], 'string', 'max' => 15],
            [['user_id'], 'string', 'max' => 8],
            [['user_agent'], 'string', 'max' => 128],
            [['url'], 'string', 'max' => 256],
            [
                ['level_id'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => Level::className(),
                'targetAttribute' => ['level_id' => 'id'],
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'level_id'   => 'Level ID',
            'message'    => 'Message',
            'create'     => 'Create',
            'ip'         => 'Ip',
            'user-id'    => 'User Unique Key',
            'user-agent' => 'User Browser',
            'url'        => 'UrlKey'
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
     * Gets query for [[Project]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        $prefix = $GLOBALS['prefix'];
        $project = Project::findOne(['prefix' => $prefix]);
        return $project;
    }

    public function getLevelId()
    {
        return $this->hasOne(LevelSearch::className(), ['key' => 'level_id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        $prefix = $GLOBALS['prefix'];
        $project = Project::findOne(['prefix' => $prefix]);
        $levelId = $this->level_id;

        $notification = LevelNotification::find()->where(['level_id' => $levelId, 'project_id' => $project->id])->one();
        if ($notification !== null && $notification->active) {
           Handler::notify($this, $notification);
        }
        parent::afterSave($insert, $changedAttributes);
    }
}
