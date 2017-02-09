<?php
/**
 * @copyright Copyright (c) 2009 Tanasiychuk Stepan (http://stfalcon.com)
 */

/**
 * Хелпер для загрузки меню продавца
 */
class Shops_View_Helper_ShopMenu extends ZFEngine_View_Helper_Abstract
{

    /**
     * Возвращает отрендереное меню продавца
     *
     * @param int $shopId
     * @return string
     */
    public function shopMenu($shopId)
    {
        $menu = array(
            '/shops/index/profile' => array(
                'label'         => _('Профиль'),
                'route'         => 'default',
                'resource'      => 'mvc:shops:index',
                'privilege'     => 'profile',
            ),
            '/shops/comments/my' . $shopId => array(
                'label'         => 'Отзывы',
                'route'         => 'default',
                'resource'      => 'mvc:shops:index',
                'privilege'     => 'view',
                'content-append'     => ' (33)',
            ),

            '/catalog/products/my-list' => array(
                'label'         => _('Мой список %s'),
                'label-params' => array(''),
                'route'         => 'default',
                'resource'      => 'mvc:catalog:products',
                'privilege'     => 'my-list',
            ),
            '/marketplace/products/list' => array(
                'label'         => _('Мои объявления %s'),
                'label-params' => array(''),
                'route'         => 'default',
                'resource'      => 'mvc:marketplace:products',
                'privilege'     => 'list',
            ),
            '/users/index/password-change' => array(
                'label'         => _('Сменить пароль'),
                'route'         => 'default',
                'resource'      => 'mvc:users:index',
                'privilege'     => 'password-change',
            ),
            '/shops/index/edit/' => array(
                'label'         => 'Настройки',
                'route'         => 'default',
                'resource'      => 'mvc:shops:index',
                'privilege'     => 'edit',
            ),
        );

        $navigation = new ZFEngine_Navigation($menu);
        $navigation->setNavigationMenuClass('myListMenu padTop8');
        $navigation->setCurrentActiveClass('myListMenuActive');
        
        return $this->view->navigationRender($navigation)->renderMenu();
    }

}