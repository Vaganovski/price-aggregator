<?php

/**
 * Shops_Model_Base_Shop
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property string $description
 * @property string $work_time
 * @property string $credit
 * @property string $delivery
 * @property enum $status
 * @property enum $type
 * @property int $period
 * @property string $price_filename
 * @property enum $price_status
 * @property timestamp $price_updated_at
 * @property datetime $last_renewal_date
 * @property datetime $untill_date
 * @property string $email
 * @property integer $chain_shop_id
 * @property string $chain_name
 * @property integer $manager_id
 * @property integer $message_id
 * @property Users_Model_User $User
 * @property Users_Model_User $Manager
 * @property Shops_Model_ChainShop $ChainShop
 * @property Doctrine_Collection $Prices
 * @property Doctrine_Collection $ShopComments
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class Shops_Model_Base_Shop extends Shops_Model_ShopBase
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();
        $this->setTableName('shops');
        $this->hasColumn('description', 'string', null, array(
             'type' => 'string',
             'length' => '',
             ));
        $this->hasColumn('work_time', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('credit', 'string', 120, array(
             'type' => 'string',
             'length' => '120',
             ));
        $this->hasColumn('delivery', 'string', 120, array(
             'type' => 'string',
             'length' => '120',
             ));
        $this->hasColumn('status', 'enum', null, array(
             'type' => 'enum',
             'notnull' => true,
             'values' => 
             array(
              0 => 'available',
              1 => 'disable',
              2 => 'new',
             ),
             'default' => 'new',
             ));
        $this->hasColumn('type', 'enum', null, array(
             'type' => 'enum',
             'notnull' => true,
             'values' => 
             array(
              0 => 'normal',
              1 => 'internet',
             ),
             'default' => 'normal',
             ));
        $this->hasColumn('period', 'int', 4, array(
             'type' => 'int',
             'notnull' => true,
             'length' => '4',
             ));
        $this->hasColumn('price_filename', 'string', 45, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '45',
             ));
        $this->hasColumn('price_status', 'enum', null, array(
             'type' => 'enum',
             'notnull' => true,
             'values' => 
             array(
              0 => 'absent',
              1 => 'queue',
              2 => 'processed',
             ),
             'default' => 'absent',
             ));
        $this->hasColumn('price_updated_at', 'timestamp', null, array(
             'type' => 'timestamp',
             'notnull' => false,
             ));
        $this->hasColumn('last_renewal_date', 'datetime', null, array(
             'type' => 'datetime',
             'length' => '',
             ));
        $this->hasColumn('untill_date', 'datetime', null, array(
             'type' => 'datetime',
             'length' => '',
             ));
        $this->hasColumn('email', 'string', 128, array(
             'type' => 'string',
             'notnull' => true,
             'unique' => false,
             'email' => true,
             'length' => '128',
             ));
        $this->hasColumn('chain_shop_id', 'integer', 4, array(
             'type' => 'integer',
             'unsigned' => true,
             'length' => '4',
             ));
        $this->hasColumn('chain_name', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('manager_id', 'integer', 4, array(
             'type' => 'integer',
             'unsigned' => true,
             'length' => '4',
             ));
        $this->hasColumn('message_id', 'integer', 4, array(
             'type' => 'integer',
             'unsigned' => true,
             'length' => '4',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Users_Model_User as User', array(
             'local' => 'user_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE',
             'onUpdate' => 'CASCADE'));

        $this->hasOne('Users_Model_User as Manager', array(
             'local' => 'manager_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE',
             'onUpdate' => 'CASCADE'));

        $this->hasOne('Shops_Model_ChainShop as ChainShop', array(
             'local' => 'chain_shop_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE',
             'onUpdate' => 'CASCADE'));

        $this->hasMany('Catalog_Model_Price as Prices', array(
             'local' => 'id',
             'foreign' => 'shop_id'));

        $this->hasMany('Shops_Model_Comment as ShopComments', array(
             'local' => 'id',
             'foreign' => 'entity_id'));
    }
}