<?php

/**
 * Marketplace_Model_Base_Product
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property int $clicks
 * @property integer $user_id
 * @property enum $type
 * @property decimal $price
 * @property integer $approve
 * @property Users_Model_User $user
 * @property Doctrine_Collection $Categories
 * @property Doctrine_Collection $CategoryProduct
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class Marketplace_Model_Base_Product extends Products_Model_Product
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();
        $this->setTableName('marketplace_products');
        $this->hasColumn('clicks', 'int', 4, array(
             'type' => 'int',
             'notnull' => true,
             'length' => '4',
             ));
        $this->hasColumn('user_id', 'integer', 4, array(
             'type' => 'integer',
             'unsigned' => true,
             'length' => '4',
             ));
        $this->hasColumn('type', 'enum', null, array(
             'type' => 'enum',
             'values' => 
             array(
              0 => 'buy',
              1 => 'sell',
             ),
             ));
        $this->hasColumn('price', 'decimal', 14, array(
             'type' => 'decimal',
             'length' => '14',
             'scale' => '2',
             ));
        $this->hasColumn('approve', 'integer', 1, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => '1',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Users_Model_User as user', array(
             'local' => 'user_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE',
             'onUpdate' => 'CASCADE'));

        $this->hasMany('Marketplace_Model_Category as Categories', array(
             'refClass' => 'Marketplace_Model_CategoryProduct',
             'local' => 'product_id',
             'foreign' => 'category_id'));

        $this->hasMany('Marketplace_Model_CategoryProduct as CategoryProduct', array(
             'local' => 'id',
             'foreign' => 'product_id'));
    }
}