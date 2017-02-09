<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Addcatalogcategoriesproducts extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->createTable('catalog_categories_products', array(
             'id' => 
             array(
              'type' => 'integer',
              'unsigned' => true,
              'primary' => true,
              'autoincrement' => true,
              'length' => 4,
             ),
             'product_id' => 
             array(
              'type' => 'integer',
              'unsigned' => true,
              'length' => 4,
             ),
             'category_id' => 
             array(
              'type' => 'integer',
              'unsigned' => true,
              'length' => 4,
             ),
             ), array(
             'type' => 'INNODB',
             'indexes' => 
             array(
             ),
             'primary' => 
             array(
              0 => 'id',
             ),
             'collate' => 'utf8_general_ci',
             'charset' => 'utf8',
             ));
    }

    public function down()
    {
        $this->dropTable('catalog_categories_products');
    }
}