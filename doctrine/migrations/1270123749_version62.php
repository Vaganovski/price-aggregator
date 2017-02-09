<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version62 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->createTable('pages', array(
             'id' => 
             array(
              'type' => 'integer',
              'unsigned' => '1',
              'primary' => '1',
              'autoincrement' => '1',
              'length' => '4',
             ),
             'alias' => 
             array(
              'type' => 'string',
              'unique' => '1',
              'notnull' => '1',
              'length' => '32',
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
        $this->createTable('pages_translation', array(
             'id' => 
             array(
              'type' => 'integer',
              'unsigned' => '1',
              'length' => '4',
              'primary' => '1',
             ),
             'title' => 
             array(
              'type' => 'string',
              'notnull' => '1',
              'length' => '255',
             ),
             'content' => 
             array(
              'type' => 'string',
              'notnull' => '1',
              'length' => '',
             ),
             'lang' => 
             array(
              'fixed' => '1',
              'primary' => '1',
              'type' => 'string',
              'length' => '2',
             ),
             ), array(
             'type' => 'INNODB',
             'primary' => 
             array(
              0 => 'id',
              1 => 'lang',
             ),
             'collate' => 'utf8_unicode_ci',
             'charset' => 'utf8',
             ));
    }

    public function down()
    {
        $this->dropTable('pages');
        $this->dropTable('pages_translation');
    }
}