<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version61 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('advertisment_banners', 'impressions', 'int', '4', array(
             'notnull' => '1',
             ));
    }

    public function down()
    {
        $this->removeColumn('advertisment_banners', 'impressions');
    }
}