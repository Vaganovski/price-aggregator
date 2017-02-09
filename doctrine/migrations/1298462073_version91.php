<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version91 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->createTable('settings', array(
             'key' => 
             array(
              'type' => 'string',
              'primary' => '1',
              'unique' => '1',
              'length' => '128',
             ),
             'value' => 
             array(
              'type' => 'string',
              'length' => '128',
             ),
             'description' => 
             array(
              'type' => 'string',
              'length' => '',
             ),
             'type' => 
             array(
              'type' => 'enum',
              'notnull' => '1',
              'values' => 
              array(
              0 => 'boolean',
              ),
              'default' => 'boolean',
              'length' => '',
             ),
             ), array(
             'primary' => 
             array(
              0 => 'key',
             ),
             'collate' => 'utf8_general_ci',
             ));

    }

    public function postUp()
    {
        $conn = Doctrine_Manager::connection();
        $statement = "INSERT INTO `settings` (`key`, `value`, `description`, `type`) VALUES ('moderation_in_marketplace', '1', 'Модерация в барахолке', 'boolean')";
        $conn->prepare($statement)->execute();
    }

    public function down()
    {
        $this->dropTable('settings');
    }
}