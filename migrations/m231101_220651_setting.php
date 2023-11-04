<?php

use yii\db\Migration;
use yii\db\Query;
use app\framework\ConfigManager;

/**
 * Class m231101_220651_setting
 */
class m231101_220651_setting extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%setting}}', [
            'id' => $this->primaryKey(),
            'project_id' => $this->integer(),
            'path' => $this->text(32),
            'value' => $this->text(),
            'create' => $this->dateTime(),
            'update' => $this->dateTime(),
        ]);

        $fields = (new Query())->select('*')->from('project')->all();

        $configManager = new ConfigManager();
        foreach ($fields  as $field) {
            $configManager->setConfigSet('gmt', $field['gmt'], $field['id']);
            $configManager->setConfigSet('enable_server_check', $field['enable_server_check'], $field['id']);
        }

        $this->dropColumn('project', 'gmt');
        $this->dropColumn('project', 'enable_server_check');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%setting}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231101_220651_setting cannot be reverted.\n";

        return false;
    }
    */
}
