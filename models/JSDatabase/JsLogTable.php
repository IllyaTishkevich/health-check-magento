<?php


namespace app\models\JSDatabase;

use yii\db\Migration;

class JsLogTable extends Migration
{
    protected $prefix;

    public function __construct($prefix, $config = [])
    {
        $this->prefix = $prefix;
        parent::__construct($config);
    }

    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%'.$this->prefix.'_js_log}}', [
            'id' => $this->primaryKey(),
            'level_id' => $this->integer(),
            'message' => $this->text(),
            'create' => $this->dateTime(),
            'ip' => $this->string(15),
        ]);

//        $this->createTable('{{%'.$this->prefix.'_js_event}}', [
//            'id' => $this->primaryKey(),
//            'message' => $this->text(),
//            'create' => $this->dateTime(),
//            'ip' => $this->string(15),
//        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%'.$this->prefix.'_js_log}}');
//        $this->dropTable('{{%'.$this->prefix.'_js_event}}');
    }
}