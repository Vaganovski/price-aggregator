<?php

/**
 * Catalog_Model_Category
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
class Catalog_Model_Category extends Catalog_Model_Base_Category
{
    /**
     * Путь к изображению относительно public
     */
    const PUBLIC_UPLOAD_PATH = '/upload/images/categories';

     /**
     * Устанавливает имя для каринки
     * Также удаляет файл предыдущей картинки, если такой был
     *
     * @param string $imageFilename
     */
    public function setImageFilename($imageFilename)
    {
        if (strlen($imageFilename) && $this->_get('image_filename') != $imageFilename) {
            $this->unlinkImages();
            $this->_set('image_filename', $imageFilename);
        }
    }

    /**
     * Return url path to the image file
     *
     * @return string
     */
    public function getImageUrl()
    {
        if (strlen($this->image_filename) &&
                file_exists(self::getImageAbsoluteUploadPath() . DIRECTORY_SEPARATOR . $this->image_filename)) {
            return self::PUBLIC_UPLOAD_PATH
                . '/'
                . $this->image_filename;
        }
        return NULL;
    }

    /**
     * Delete images
     *
     * @return void
     */
    public function unlinkImages()
    {
        if (!strlen($this->image_filename)) {
            return null;
        }

        if (file_exists($this->getImageFullPath())) {
            unlink($this->getImageFullPath());
        }
    }

    /**
     * Return absolute path to the image file
     *
     * @return string|NULL
     */
    public function getImageFullPath()
    {
        if (strlen($this->image_filename)) {
            return self::getImageAbsoluteUploadPath() . DIRECTORY_SEPARATOR . $this->image_filename;
        }
        return NULL;
    }

    /**
     * Возвращает абсолютный путь к папке с оригинальными изображениями
     *
     * @return string
     */
    public static function getImageAbsoluteUploadPath()
    {
        return realpath(APPLICATION_PATH . '/../public' . self::PUBLIC_UPLOAD_PATH);
    }

    /**
     * Все подкатегории
     * @param int $limit
     * @return array
     */
    public function getAllChildrenIds($limit = null)
    {
        $baseAlias = $this->getTable()->getTree()->getBaseAlias();
        $query = $this->getTable()->getTree()->getBaseQuery();
        $params = array($this->lft, $this->rgt);

        $query->select($baseAlias . '.id');
        $query->addWhere("$baseAlias.lft > ? AND $baseAlias.rgt < ?", $params)->addOrderBy("$baseAlias.lft asc");
        if (!is_null($limit)) {
            $query->limit($limit);
        }
        $query = $this->getTable()->getTree()->returnQueryWithRootId($query, $this->getNode()->getRootValue());

        $ids = array();
        foreach ($query->fetchArray() as $item) {
            $ids[] = (int)$item['id'];
        }

        if (count($ids) <= 0) {
            return false;
        }
        return $ids;
    }

}