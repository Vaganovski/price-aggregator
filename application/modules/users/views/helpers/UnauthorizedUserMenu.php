<?php
/**
 * @copyright Copyright (c) 2009 Tanasiychuk Stepan (http://stfalcon.com)
 */

/**
 * Хелпер для загрузки меню пользователя
 */
class Users_View_Helper_UnauthorizedUserMenu extends ZFEngine_View_Helper_Abstract
{

    /**
     * Возвращает отрендереное меню пользователя
     *
     * @return string
     */
    public function unauthorizedUserMenu()
    {
        // количество товаро в моем списке
        $namespace = new Zend_Session_Namespace('product-to-mylist');
        $countMyList = count($namespace->product_ids);
        // количество товаро к сравнению
        $namespace = new Zend_Session_Namespace('product-to-compare');
        $countCompare = count($namespace->product_ids);

        $menu = array(

            '/catalog/products/my-list' => array(
                'label'         => _('Мой список'),
                'label-params' => array(''),
                'route'         => 'default',
                'resource'      => 'mvc:catalog:products',
                'privilege'     => 'my-list',
                'content-append' => $countMyList > 0 ? ' (' . $countMyList . ')' : ''
            ),
            '/catalog/products/compare/products/' . implode('-', $namespace->product_ids) => array(
                'label'         => _('Товары к сравнению'),
                'label-params' => array(''),
                'route'         => 'default',
                'resource'      => 'mvc:catalog:products',
                'privilege'     => 'compare',
                'content-append' => $countCompare > 0 ? ' (' . $countCompare . ')' : ''
            ),
        );

        $navigation = new ZFEngine_Navigation($menu);
        $navigation->setNavigationMenuClass('myListMenu');
        $navigation->setCurrentActiveClass('myListMenuActive');
        
        return $this->view->navigationRender($navigation)->renderMenu();
    }

}