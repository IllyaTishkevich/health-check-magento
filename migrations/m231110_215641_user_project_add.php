<?php

use yii\db\Migration;

/**
 * Class m231110_215641_user_project_add
 */
class m231110_215641_user_project_add extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%project_user}}', 'add', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%project_user}}', 'add');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231110_215641_user_project_add cannot be reverted.\n";

        return false;
    }
    */
}
