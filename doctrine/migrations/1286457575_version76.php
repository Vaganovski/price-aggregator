<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version76 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('shops', 'price_status', 'enum', '', array(
             'notnull' => '1',
             'values' => 
             array(
              0 => 'absent',
              1 => 'queue',
              2 => 'processed',
             ),
             'default' => 'absent',
             ));
    }

    public function down()
    {
        $this->removeColumn('shops', 'price_status');
    }
}