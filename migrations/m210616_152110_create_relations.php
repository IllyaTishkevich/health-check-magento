<?php

use yii\db\Migration;

/**
 * Class m210616_152110_create_relations
 */
class m210616_152110_create_relations extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        //level_notitfication
        $this->addForeignKey('notification_level_to_project','level_notification','project_id','project','id');
        $this->addForeignKey('notification_level_to_level','level_notification','level_id','level','id');
        $this->addForeignKey('notification_level_to_notitfication','level_notification','notification_id','notification','id');

        //project_user
        $this->addForeignKey('project_to_user','project_user','user_id','user','id');
        $this->addForeignKey('user_to_project','project_user','project_id','project','id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210616_152110_create_relations cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210616_152110_create_relations cannot be reverted.\n";

        return false;
    }
    */
}
