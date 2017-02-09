<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version53 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->dropForeignKey('marketplace_categories', 'marketplace_categories_features_group_id_features_groups_id');
    }

    public function down()
    {
        $this->createForeignKey('marketplace_categories', 'marketplace_categories_features_group_id_features_groups_id', array(
             'name' => 'marketplace_categories_features_group_id_features_groups_id',
             'local' => 'features_group_id',
             'foreign' => 'id',
             'foreignTable' => 'features_groups',
             'onUpdate' => 'CASCADE',
             'onDelete' => 'CASCADE',
             ));
    }
}