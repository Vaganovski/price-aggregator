<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version54 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->removeColumn('marketplace_categories', 'features_group_id');
    }

    public function down()
    {
        $this->addColumn('marketplace_categories', 'features_group_id', 'integer', '4', array(
             'unsigned' => '1',
             ));
    }
}