<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version87 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('shops_chain', 'user_id', 'integer', '4', array(
             'unsigned' => '1',
             ));
    }

    public function down()
    {
        $this->removeColumn('shops_chain', 'user_id');
    }
}