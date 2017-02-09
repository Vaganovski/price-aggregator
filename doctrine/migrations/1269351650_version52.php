<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version52 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->createForeignKey('marketplace_categories', 'marketplace_categories_features_group_id_features_groups_id', array(
             'name' => 'marketplace_categories_features_group_id_features_groups_id',
             'local' => 'features_group_id',
             'foreign' => 'id',
             'foreignTable' => 'features_groups',
             'onUpdate' => 'CASCADE',
             'onDelete' => 'CASCADE',
             ));
        $this->createForeignKey('marketplace_categories_products', 'mpmi', array(
             'name' => 'mpmi',
             'local' => 'product_id',
             'foreign' => 'id',
             'foreignTable' => 'marketplace_products',
             'onUpdate' => 'CASCADE',
             'onDelete' => 'CASCADE',
             ));
        $this->createForeignKey('marketplace_categories_products', 'mcmi', array(
             'name' => 'mcmi',
             'local' => 'category_id',
             'foreign' => 'id',
             'foreignTable' => 'marketplace_categories',
             'onUpdate' => 'CASCADE',
             'onDelete' => 'CASCADE',
             ));
        $this->createForeignKey('marketplace_products', 'marketplace_products_user_id_users_id', array(
             'name' => 'marketplace_products_user_id_users_id',
             'local' => 'user_id',
             'foreign' => 'id',
             'foreignTable' => 'users',
             'onUpdate' => 'CASCADE',
             'onDelete' => 'CASCADE',
             ));
        $this->addIndex('marketplace_categories', 'marketplace_categories_features_group_id', array(
             'fields' => 
             array(
              0 => 'features_group_id',
             ),
             ));
        $this->addIndex('marketplace_categories_products', 'marketplace_categories_products_product_id', array(
             'fields' => 
             array(
              0 => 'product_id',
             ),
             ));
        $this->addIndex('marketplace_categories_products', 'marketplace_categories_products_category_id', array(
             'fields' => 
             array(
              0 => 'category_id',
             ),
             ));
        $this->addIndex('marketplace_products', 'marketplace_products_user_id', array(
             'fields' => 
             array(
              0 => 'user_id',
             ),
             ));
    }

    public function down()
    {
        $this->dropForeignKey('marketplace_categories', 'marketplace_categories_features_group_id_features_groups_id');
        $this->dropForeignKey('marketplace_categories_products', 'mpmi');
        $this->dropForeignKey('marketplace_categories_products', 'mcmi');
        $this->dropForeignKey('marketplace_products', 'marketplace_products_user_id_users_id');
        $this->removeIndex('marketplace_categories', 'marketplace_categories_features_group_id', array(
             'fields' => 
             array(
              0 => 'features_group_id',
             ),
             ));
        $this->removeIndex('marketplace_categories_products', 'marketplace_categories_products_product_id', array(
             'fields' => 
             array(
              0 => 'product_id',
             ),
             ));
        $this->removeIndex('marketplace_categories_products', 'marketplace_categories_products_category_id', array(
             'fields' => 
             array(
              0 => 'category_id',
             ),
             ));
        $this->removeIndex('marketplace_products', 'marketplace_products_user_id', array(
             'fields' => 
             array(
              0 => 'user_id',
             ),
             ));
    }
}