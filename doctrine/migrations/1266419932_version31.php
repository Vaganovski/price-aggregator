<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version31 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->createTable('catalog_brands', array(
             'id' => 
             array(
              'type' => 'integer',
              'unsigned' => '1',
              'primary' => '1',
              'autoincrement' => '1',
              'length' => '4',
             ),
             'image_filename' => 
             array(
              'type' => 'string',
              'notnull' => '1',
              'length' => '45',
             ),
             'name' => 
             array(
              'type' => 'string',
              'length' => '255',
             ),
             'description' => 
             array(
              'type' => 'string',
              'length' => '',
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
        $this->addColumn('catalog_products', 'brand_id', 'integer', '4', array(
             'unsigned' => '1',
             ));
    }

    public function down()
    {
        $this->dropTable('catalog_brands');
        $this->removeColumn('catalog_products', 'brand_id');
    }
}