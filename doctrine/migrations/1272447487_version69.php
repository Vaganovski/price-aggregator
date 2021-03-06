<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version69 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('advertisment_banners', 'page_placement', 'string', '255', array(
             ));
        $this->changeColumn('advertisment_banners', 'show_on', 'enum', '', array(
             'notnull' => '1',
             'values' => 
             array(
              0 => 'anywhere',
              1 => 'on-main',
              2 => 'none',
              3 => 'in-place',
             ),
             'default' => 'none',
             ));
    }

    public function down()
    {
        $this->removeColumn('advertisment_banners', 'page_placement');
    }
}