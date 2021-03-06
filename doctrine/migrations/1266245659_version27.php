<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version27 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->dropTable('catalog_products_images');
        $this->createTable('catalog_products_images', array(
             'id' => 
             array(
              'type' => 'integer',
              'unsigned' => '1',
              'primary' => '1',
              'autoincrement' => '1',
              'length' => '4',
             ),
             'product_id' => 
             array(
              'type' => 'integer',
              'unsigned' => '1',
              'length' => '4',
             ),
             'image_filename' => 
             array(
              'type' => 'string',
              'notnull' => '1',
              'length' => '45',
             ),
             ), array(
             'type' => 'INNODB',
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
        $this->createTable('catalog_products_images', array(
             'id' => 
             array(
              'type' => 'integer',
              'unsigned' => '1',
              'primary' => '1',
              'autoincrement' => '1',
              'length' => '4',
             ),
             'product_id' => 
             array(
              'type' => 'integer',
              'unsigned' => '1',
              'length' => '4',
             ),
             'image_filename' => 
             array(
              'type' => 'string',
              'notnull' => '1',
              'length' => '45',
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
        $this->dropTable('catalog_products_images');
    }
}