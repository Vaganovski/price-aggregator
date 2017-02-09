<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version30 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->createForeignKey('catalog_prices', 'catalog_prices_product_id_catalog_products_id', array(
             'name' => 'catalog_prices_product_id_catalog_products_id',
             'local' => 'product_id',
             'foreign' => 'id',
             'foreignTable' => 'catalog_products',
             'onUpdate' => 'CASCADE',
             'onDelete' => 'CASCADE',
             ));
        $this->createForeignKey('catalog_prices', 'catalog_prices_shop_id_shops_id', array(
             'name' => 'catalog_prices_shop_id_shops_id',
             'local' => 'shop_id',
             'foreign' => 'id',
             'foreignTable' => 'shops',
             'onUpdate' => 'CASCADE',
             'onDelete' => 'CASCADE',
             ));
        $this->addIndex('catalog_prices', 'catalog_prices_product_id', array(
             'fields' => 
             array(
              0 => 'product_id',
             ),
             ));
        $this->addIndex('catalog_prices', 'catalog_prices_shop_id', array(
             'fields' => 
             array(
              0 => 'shop_id',
             ),
             ));
    }

    public function down()
    {
        $this->dropForeignKey('catalog_prices', 'catalog_prices_product_id_catalog_products_id');
        $this->dropForeignKey('catalog_prices', 'catalog_prices_shop_id_shops_id');
        $this->removeIndex('catalog_prices', 'catalog_prices_product_id', array(
             'fields' => 
             array(
              0 => 'product_id',
             ),
             ));
        $this->removeIndex('catalog_prices', 'catalog_prices_shop_id', array(
             'fields' => 
             array(
              0 => 'shop_id',
             ),
             ));
    }
}