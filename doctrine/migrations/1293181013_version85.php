<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version85 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->changeColumn('users', 'role', 'enum', '', array(
             'notnull' => '1',
             'values' => 
             array(
              0 => 'administrator',
              1 => 'member',
              2 => 'merchant',
              3 => 'manager',
             ),
             ));
    }

    public function down()
    {

    }
}