<?php

use yii\db\Migration;

/**
 * Class m221219_085652_sites_table_create
 */
class m221219_085652_sites_table_create extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%sites}}', [
            'id' => $this->primaryKey(),
            'site_url' => $this->string(128),
            'status_code' => $this->string(5),
            'available' => $this->string(10),
            'cron_status' => $this->string(10)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221219_085652_sites_table_create cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221219_085652_sites_table_create cannot be reverted.\n";

        return false;
    }
    */
}
