<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version59 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('advertisment_banners', 'url', 'string', '255', array(
             ));
    }

    public function down()
    {
        $this->removeColumn('advertisment_banners', 'url');
    }
}