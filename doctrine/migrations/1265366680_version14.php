<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version14 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->createForeignKey('features_fields', 'features_fields_features_set_id_features_sets_id', array(
             'name' => 'features_fields_features_set_id_features_sets_id',
             'local' => 'features_set_id',
             'foreign' => 'id',
             'foreignTable' => 'features_sets',
             'onUpdate' => 'CASCADE',
             'onDelete' => 'CASCADE',
             ));
        $this->createForeignKey('features_sets', 'features_sets_features_group_id_features_groups_id', array(
             'name' => 'features_sets_features_group_id_features_groups_id',
             'local' => 'features_group_id',
             'foreign' => 'id',
             'foreignTable' => 'features_groups',
             'onUpdate' => 'CASCADE',
             'onDelete' => 'CASCADE',
             ));
        $this->createForeignKey('features_value', 'features_value_features_field_id_features_fields_id', array(
             'name' => 'features_value_features_field_id_features_fields_id',
             'local' => 'features_field_id',
             'foreign' => 'id',
             'foreignTable' => 'features_fields',
             'onUpdate' => 'CASCADE',
             'onDelete' => 'CASCADE',
             ));
        $this->addIndex('features_fields', 'features_fields_features_set_id', array(
             'fields' => 
             array(
              0 => 'features_set_id',
             ),
             ));
        $this->addIndex('features_sets', 'features_sets_features_group_id', array(
             'fields' => 
             array(
              0 => 'features_group_id',
             ),
             ));
        $this->addIndex('features_value', 'features_value_features_field_id', array(
             'fields' => 
             array(
              0 => 'features_field_id',
             ),
             ));
    }

    public function down()
    {
        $this->dropForeignKey('features_fields', 'features_fields_features_set_id_features_sets_id');
        $this->dropForeignKey('features_sets', 'features_sets_features_group_id_features_groups_id');
        $this->dropForeignKey('features_value', 'features_value_features_field_id_features_fields_id');
        $this->removeIndex('features_fields', 'features_fields_features_set_id', array(
             'fields' => 
             array(
              0 => 'features_set_id',
             ),
             ));
        $this->removeIndex('features_sets', 'features_sets_features_group_id', array(
             'fields' => 
             array(
              0 => 'features_group_id',
             ),
             ));
        $this->removeIndex('features_value', 'features_value_features_field_id', array(
             'fields' => 
             array(
              0 => 'features_field_id',
             ),
             ));
    }
}