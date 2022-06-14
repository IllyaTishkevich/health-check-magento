<?php

namespace app\models;

use Yii;
use app\models\Notifications\Handler;

/**
 * This is the model class for table "message".
 *
 * @property int         $id
 * @property int|null    $project_id
 * @property int|null    $level_id
 * @property string|null $message
 * @property string|null $create
 * @property string|null $ip
 *
 * @property Level       $level
 * @property Project     $project
 */
class Message extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'message';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['project_id', 'level_id'], 'integer'],
            [['message'], 'string'],
            [['create'], 'safe'],
            [['ip'], 'string', 'max' => 15],
            [
                ['level_id'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => Level::className(),
                'targetAttribute' => ['level_id' => 'id'],
            ],
            [
                ['project_id'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => Project::className(),
                'targetAttribute' => ['project_id' => 'id'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'project_id' => 'Project ID',
            'level_id'   => 'Level ID',
            'message'    => 'Message',
            'create'     => 'Create',
            'ip'         => 'Ip',
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
        return $this->hasOne(Project::className(), ['id' => 'project_id']);
    }

    public function getLevelId()
    {
        return $this->hasOne(LevelSearch::className(), ['key' => 'level_id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        $projectId = $this->project_id;
        $levelId = $this->level_id;

        $notification = LevelNotification::find()->where(['level_id' => $levelId, 'project_id' => $projectId])->one();
        if ($notification !== null && $notification->active) {
           Handler::notify($this, $notification);
        }
        parent::afterSave($insert, $changedAttributes);
    }
}
