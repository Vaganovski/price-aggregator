<?php

class Catalog_Form_Product_ProductMainImage extends Catalog_Form_Product_ProductImage
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->setName(strtolower(__CLASS__));
        return $this;
    }



}