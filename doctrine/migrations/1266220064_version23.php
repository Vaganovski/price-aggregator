<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version23 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('catalog_categories', 'features_group_id', 'integer', '4', array(
             'unsigned' => '1',
             ));
    }

    public function down()
    {
        $this->removeColumn('catalog_categories', 'features_group_id');
    }
}