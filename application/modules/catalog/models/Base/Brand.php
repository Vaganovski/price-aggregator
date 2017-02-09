<?php

/**
 * Catalog_Model_Base_Brand
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $image_filename
 * @property string $name
 * @property string $description
 * @property Doctrine_Collection $Products
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class Catalog_Model_Base_Brand extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('catalog_brands');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('image_filename', 'string', 45, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '45',
             ));
        $this->hasColumn('name', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('description', 'string', null, array(
             'type' => 'string',
             'length' => '',
             ));

        $this->option('type', 'INNODB');
        $this->option('collate', 'utf8_unicode_ci');
        $this->option('charset', 'utf8');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('Catalog_Model_Product as Products', array(
             'local' => 'id',
             'foreign' => 'brand_id'));
    }
}