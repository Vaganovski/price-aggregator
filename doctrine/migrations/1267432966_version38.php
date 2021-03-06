<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version38 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->createForeignKey('catalog_my_products_list', 'catalog_my_products_list_product_id_catalog_products_id', array(
             'name' => 'catalog_my_products_list_product_id_catalog_products_id',
             'local' => 'product_id',
             'foreign' => 'id',
             'foreignTable' => 'catalog_products',
             'onUpdate' => 'CASCADE',
             'onDelete' => 'CASCADE',
             ));
        $this->createForeignKey('catalog_my_products_list', 'catalog_my_products_list_user_id_users_id', array(
             'name' => 'catalog_my_products_list_user_id_users_id',
             'local' => 'user_id',
             'foreign' => 'id',
             'foreignTable' => 'users',
             'onUpdate' => 'CASCADE',
             'onDelete' => 'CASCADE',
             ));
        $this->addIndex('catalog_my_products_list', 'catalog_my_products_list_product_id', array(
             'fields' => 
             array(
              0 => 'product_id',
             ),
             ));
        $this->addIndex('catalog_my_products_list', 'catalog_my_products_list_user_id', array(
             'fields' => 
             array(
              0 => 'user_id',
             ),
             ));
    }

    public function down()
    {
        $this->dropForeignKey('catalog_my_products_list', 'catalog_my_products_list_product_id_catalog_products_id');
        $this->dropForeignKey('catalog_my_products_list', 'catalog_my_products_list_user_id_users_id');
        $this->removeIndex('catalog_my_products_list', 'catalog_my_products_list_product_id', array(
             'fields' => 
             array(
              0 => 'product_id',
             ),
             ));
        $this->removeIndex('catalog_my_products_list', 'catalog_my_products_list_user_id', array(
             'fields' => 
             array(
              0 => 'user_id',
             ),
             ));
    }
}