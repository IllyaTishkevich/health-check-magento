<?php

use yii\db\Migration;

/**
 * Class m210618_084212_add_column_project_url_project_table
 */
class m210618_084212_add_column_project_url_project_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%project}}', "project_url", $this->string(64));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%project}}', "project_url");
    }
    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210618_084212_add_column_project_url_project_table cannot be reverted.\n";

        return false;
    }
    */
}
