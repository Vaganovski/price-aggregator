<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version35 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->removeColumn('catalog_prices', 'filename');
        $this->addColumn('shops', 'price_filename', 'string', '45', array(
             'notnull' => '1',
             ));
    }

    public function down()
    {
        $this->addColumn('catalog_prices', 'filename', 'string', '45', array(
             'notnull' => '1',
             ));
        $this->removeColumn('shops', 'price_filename');
    }
}