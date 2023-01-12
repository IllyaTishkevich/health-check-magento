<?php

use yii\db\Migration;

/**
 * Class m230112_091456_project_table_prefix
 */
class m230112_091456_project_table_prefix extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%project}}', 'prefix', $this->string(8));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%project}}', 'prefix');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230112_091456_project_table_prefix cannot be reverted.\n";

        return false;
    }
    */
}
