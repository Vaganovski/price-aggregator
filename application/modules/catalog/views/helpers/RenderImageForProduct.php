<?php

/**
 * Хелпер
 */
class Catalog_View_Helper_RenderImageForProduct extends ZFEngine_View_Helper_Abstract
{

    /**
     * Генерация картинок для продукта
     */
    public function renderImageForProduct(Catalog_Model_Product $product)
    {
        $content = '';
        foreach ($product->Images as $image) {
            $content .= '<a rel="lightbox-product" href="'.$image->image_url.'"><img alt="' . $product->name . '" src="'. $image->image_thumbnail_url . '"></a>';
        }
        $this->view->headScript()->appendFile('/javascripts/jquery.lightbox.pack.js');
        $this->view->headLink()->appendStylesheet('/stylesheets/jquery.lightbox.css');
$script = <<<JS
    jQuery(document).ready(function(){
        jQuery('a[rel*=lightbox]').not('a[href=/images/no-photo.png]').lightBox({
            imageLoading: '/images/lightbox/lightbox-ico-loading.gif',
            imageBtnClose: '/images/lightbox/lightbox-btn-close.gif',
            imageBtnPrev: '/images/lightbox/lightbox-btn-prev.gif',
            imageBtnNext: '/images/lightbox/lightbox-btn-next.gif',
            imageBlank: '/images/lightbox/lightbox-blank.gif',
            txtImage: 'Изображение',
            txtOf: 'из'
        });
        jQuery('a[href=/images/no-photo.png]').click(function(){
            return false;
        });
    });
JS;
        $this->view->headScript()->appendScript($script);
        return $content;
    }

}