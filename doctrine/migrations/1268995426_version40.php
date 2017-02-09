<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version40 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->createTable('catalog_products_reviews', array(
             'id' => 
             array(
              'type' => 'integer',
              'unsigned' => '1',
              'primary' => '1',
              'autoincrement' => '1',
              'length' => '4',
             ),
             'mark' => 
             array(
              'type' => 'integer',
              'notnull' => '1',
              'length' => '4',
             ),
             'comment' => 
             array(
              'type' => 'string',
              'length' => '255',
             ),
             'advantages' => 
             array(
              'type' => 'string',
              'length' => '255',
             ),
             'disadvantages' => 
             array(
              'type' => 'string',
              'length' => '255',
             ),
             'product_id' => 
             array(
              'type' => 'integer',
              'unsigned' => '1',
              'length' => '4',
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
        $this->dropTable('catalog_products_reviews');
    }
}