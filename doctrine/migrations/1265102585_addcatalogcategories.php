<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Addcatalogcategories extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->createTable('catalog_categories', array(
             'id' => 
             array(
              'type' => 'integer',
              'unsigned' => true,
              'primary' => true,
              'autoincrement' => true,
              'length' => 4,
             ),
             'root_id' => 
             array(
              'type' => 'integer',
              'notnull' => true,
              'length' => 8,
             ),
             'lft' => 
             array(
              'type' => 'integer',
              'length' => 4,
             ),
             'rgt' => 
             array(
              'type' => 'integer',
              'length' => 4,
             ),
             'level' => 
             array(
              'type' => 'integer',
              'length' => 2,
             ),
             'title' => 
             array(
              'type' => 'string',
              'notnull' => true,
              'length' => 255,
             ),
             'alias' => 
             array(
              'type' => 'string',
              'notnull' => true,
              'length' => 255,
             ),
             'short_description' => 
             array(
              'type' => 'string',
              'notnull' => true,
              'length' => 255,
             ),
             'description' => 
             array(
              'type' => 'string',
              'notnull' => true,
              'length' => NULL,
             ),
             'created_at' => 
             array(
              'notnull' => true,
              'type' => 'timestamp',
              'length' => 25,
             ),
             'updated_at' => 
             array(
              'notnull' => true,
              'type' => 'timestamp',
              'length' => 25,
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
        $this->dropTable('catalog_categories');
    }
}