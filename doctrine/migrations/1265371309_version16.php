<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version16 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->changeColumn('catalog_products', 'visible', 'boolean', '25', array(
             'notnull' => '1',
             'default' => '1',
             ));
        $this->changeColumn('catalog_products', 'clicks', 'int', '4', array(
             'notnull' => '1',
             ));
    }

    public function down()
    {

    }
}