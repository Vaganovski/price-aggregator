<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version18 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('features_fields', 'type', 'enum', '', array(
             'notnull' => '1',
             'values' => 
             array(
              0 => 'select',
              1 => 'text',
             ),
             ));
    }

    public function down()
    {
        $this->removeColumn('features_fields', 'type');
    }
}