<?php
/**
 * @copyright Copyright (c) 2009 Tanasiychuk Stepan (http://stfalcon.com)
 */

/**
 * Хелпер для загрузки меню пользователя
 */
class Default_View_Helper_HeaderMenu extends ZFEngine_View_Helper_Abstract
{

    private $_content = null;


    /**
     *
     * @return string
     */
    public function headerMenu()
    {
        $menu = array(
            '/catalog/categories/list' => array(
                'label'         => _('Каталог'),
                'route'         => 'default',
                'resource'      => 'mvc:catalog:index',
                'privilege'     => 'profile',
            ),
            '/shops/index/index' => array(
                'label' => _('Магазины'),
                'route' => 'default',
                'resource' => 'mvc:shops:index',
                'privilege' => 'index',
                'content-append' => ' <span>' . $this->view->ShopsCount() . '</span>',
            ),
            '/catalog/brands/index' => array(
                'label'         => _('Производители'),
                'route'         => 'default',
                'resource'      => 'mvc:catalog:brands',
                'privilege'     => 'list',
                'content-append' => ' <span>' . $this->view->BrandsCount() . '</span>',
            ),
            '/marketplace/categories/list' => array(
                'label'         => _('Барахолка'),
                'route'         => 'default',
                'resource'      => 'mvc:marketplace:categories',
                'privilege'     => 'list',
                'content-append' => ' <span>' . $this->view->MarketplaceProductsCount() . '</span>',
            ),
        );


        $navigation = new ZFEngine_Navigation($menu);
        $navigation->setCurrentActiveClass('haaderMenuActive');

        return $this->view->navigationRender($navigation)->renderMenu();
    }

}