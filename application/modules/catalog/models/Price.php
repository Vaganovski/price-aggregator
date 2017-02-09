<?php

/**
 * Catalog_Model_Price
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
class Catalog_Model_Price extends Catalog_Model_Base_Price
{
    /**
     * Путь к изображению относительно public
     */
    const PUBLIC_UPLOAD_PATH = '/upload/prices/';

    /**
     * Возвращает абсолютный путь к папке с оригинальными изображениями
     *
     * @return string
     */
    public static function getPriceAbsoluteUploadPath()
    {
        return realpath(APPLICATION_PATH . '/../public' . self::PUBLIC_UPLOAD_PATH);
    }

    public function getAvailable()
    {
        $available = $this->_get('available');

        if ($available == 'in_stock') {
            return _('На заказ');
        } elseif ($available == 'available') {
            return _('в наличии');
        }
    }

    public function getPriceFilenameUrl()
    {
        if (strlen($this->price_filename)) {
            return self::PUBLIC_UPLOAD_PATH
                . '/'
                . $this->price_filename;
        }
        return NULL;
    }

    public function getPrice()
    {
        $currency = new Zend_Currency();
        $price = $this->_get('price');
        if (floor($price) == $price) {
            $currency->setFormat(array('precision' => 0, 'symbol' => ''));
            $price = $currency->toCurrency($price);
        } else {
            $currency->setFormat(array('precision' => 2, 'symbol' => ''));
            $price = $currency->toCurrency($price);
        }
        return $price;
    }
}