<?php

/**
 * Shops_Model_Base_ShopBase
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $name
 * @property string $city
 * @property string $address
 * @property string $site
 * @property string $phone
 * @property string $email
 * @property string $image_filename
 * @property integer $user_id
 * @property Users_Model_User $User
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class Shops_Model_Base_ShopBase extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('shops');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('name', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('city', 'string', 140, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '140',
             ));
        $this->hasColumn('address', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('site', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('phone', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('email', 'string', 128, array(
             'type' => 'string',
             'notnull' => true,
             'unique' => true,
             'email' => true,
             'length' => '128',
             ));
        $this->hasColumn('image_filename', 'string', 45, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '45',
             ));
        $this->hasColumn('user_id', 'integer', 4, array(
             'type' => 'integer',
             'unsigned' => true,
             'length' => '4',
             ));

        $this->option('type', 'INNODB');
        $this->option('collate', 'utf8_unicode_ci');
        $this->option('charset', 'utf8');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Users_Model_User as User', array(
             'local' => 'user_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE',
             'onUpdate' => 'CASCADE'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}