<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version92 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->addColumn('shops', 'description', 'string', '', array(
             ));
    }

    public function down()
    {
        $this->removeColumn('shops', 'description');
    }
}