<?php
/**
 * @copyright Copyright (c) 2009 Tanasiychuk Stepan (http://stfalcon.com)
 */

/**
 * Хелпер для загрузки меню пользователя
 */
class Users_View_Helper_UserMenu extends ZFEngine_View_Helper_Abstract
{

    /**
     * Возвращает отрендереное меню пользователя
     *
     * @return string
     */
    public function userMenu($user = NULL)
    {
        $menu2 = array();

        if (!in_array(Users_Model_UserService::getUserAuthIdentity()->role, array(Users_Model_User::ROLE_MERCHANT, Users_Model_User::ROLE_MEMBER))) {
            return;
        }

        if (is_null($user)) {
            $service = new Users_Model_UserService();
            $user = $service->getUserByAuth();
        }

        $menu = array(
            '/shops/index/profile/' => array(
                'label'         => _('Магазины'),
                'route'         => 'default',
                'resource'      => 'mvc:shops:index',
                'privilege'     => 'profile',
                'active'    => array(
                    array('route' => 'default', 'url' => '/shops/index/profile/*')
                ),
            ),
            '/shops/chain-shop/list/' => array(
                'label'         => _('Сеть магазинов'),
                'route'         => 'default',
                'resource'      => 'mvc:shops:chain-shop',
                'privilege'     => 'list',
                'active'    => array(
                    array('route' => 'default', 'url' => '/shops/chain-shop/*')
                ),
            ),
        );

        $myList = new Catalog_Model_MyProductsListService();
        $myListCount = $myList->getMapper()->countProductsFromList($user->id);

        $marketplace = new Marketplace_Model_ProductService();
        $marketplaceCount = $marketplace->getMapper()->countByUserId($user->id);

        $menu = array_merge($menu, array(

            '/catalog/products/my-list' => array(
                'label'         => _('Мой список'),
                'label-params' => array(''),
                'route'         => 'default',
                'resource'      => 'mvc:catalog:products',
                'privilege'     => 'my-list',
                'content-append' => $myListCount > 0 ? ' (' . $myListCount . ')' : ''
            ),
            '/marketplace/products/list' => array(
                'label'         => _('Мои объявления %s'),
                'label-params' => array(''),
                'route'         => 'default',
                'resource'      => 'mvc:marketplace:products',
                'privilege'     => 'list',
                'content-append' => $marketplaceCount > 0 ? ' (' . $marketplaceCount . ')' : ''
            ),
            '/users/index/profile/' => array(
                'label'         => _('Мой профиль'),
                'route'         => 'default',
                'resource'      => 'mvc:users:index',
                'privilege'     => 'profile',
            ),
            '/users/index/password-change' => array(
                'label'         => _('Сменить пароль'),
                'route'         => 'default',
                'resource'      => 'mvc:users:index',
                'privilege'     => 'password-change',
            ),
        ), $menu2);

        $navigation = new ZFEngine_Navigation($menu);
        $navigation->setNavigationMenuClass('myListMenu');
        $navigation->setCurrentActiveClass('myListMenuActive');
        
        return $this->view->navigationRender($navigation)->renderMenu();
    }

}