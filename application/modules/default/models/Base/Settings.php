<?php

/**
 * Default_Model_Base_Settings
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property string $key
 * @property string $value
 * @property string $description
 * @property enum $type
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class Default_Model_Base_Settings extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('settings');
        $this->hasColumn('key', 'string', 128, array(
             'type' => 'string',
             'primary' => true,
             'unique' => true,
             'length' => '128',
             ));
        $this->hasColumn('value', 'string', 128, array(
             'type' => 'string',
             'length' => '128',
             ));
        $this->hasColumn('description', 'string', null, array(
             'type' => 'string',
             'length' => '',
             ));
        $this->hasColumn('type', 'enum', null, array(
             'type' => 'enum',
             'notnull' => true,
             'values' => 
             array(
              0 => 'boolean',
              1 => 'int',
             ),
             'default' => 'boolean',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        
    }
}