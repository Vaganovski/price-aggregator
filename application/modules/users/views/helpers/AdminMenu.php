<?php
/**
 * @copyright Copyright (c) 2009 Tanasiychuk Stepan (http://stfalcon.com)
 */

/**
 * Возвращает отрендереное меню администратора
 */
class Users_View_Helper_AdminMenu extends ZFEngine_View_Helper_Abstract
{

    private $_content = null;


    /**
     * @return string
     */
    public function adminMenu()
    {
        $productsCount = $this->view->ProductsCount();
        $shopsCount = $this->view->ShopsCount();
        $reviewsCount = $this->view->ReviewsCount();
        $shopCommentsCount = $this->view->ShopCommentsCount();
        $marketplaceProductsCount = $this->view->MarketplaceProductsCount();
        $menu = array(
            '/shops/index/list-admin/status/new' => array(
                'label' => _('Фирмы / Продавцы'),
                'route' => 'default',
                'resource' => 'mvc:shops:index',
                'privilege' => 'list-admin',
                'active' => '/shops/index/*/*',
                'content-append' => $shopsCount > 0 ? ' <span>(' . $shopsCount . ')</span>' : '',
            ),
            '/catalog/products/list-admin' => array(
                'label' => _('Товары'),
                'route' => 'default',
                'resource' => 'mvc:catalog:products',
                'privilege' => 'list-admin',
                'content-append' => $productsCount > 0 ? ' <span>(' . $productsCount . ')</span>' : '',
            ),
            '/shops/comments/list-admin/tab/new' => array(
                'label' => _('Отзывы к продавцам'),
                'route' => 'default',
                'resource' => 'mvc:shops:comments',
                'privilege' => 'list-admin',
                'content-append' => $shopCommentsCount > 0 ? ' <span>(' . $shopCommentsCount . ')</span>' : '',
            ),
            '/catalog/reviews/list-admin/tab/all' => array(
                'label' => _('Отзывы к товарам'),
                'route' => 'default',
                'resource' => 'mvc:catalog:reviews',
                'privilege' => 'list-admin',
                'content-append' => $reviewsCount > 0 ? ' <span>(' . $reviewsCount . ')</span>' : '',
            ),

            '@separator#1' => array(
            ),

            '/marketplace/products/list-admin' => array(
                'content-before' => '<li class="titleh6">' . $this->view->translate('Барахолка') . ':</li>',
                'label' => _('Товары'),
                'route' => 'default',
                'resource' => 'mvc:marketplace:products',
                'privilege' => 'list-admin',
                'content-append' => $marketplaceProductsCount > 0 ? ' <span>(' . $marketplaceProductsCount . ')</span>' : '',
            ),

            '/marketplace/categories/list-admin' => array(
                'label' => _('Категории'),
                'route' => 'default',
                'resource' => 'mvc:marketplace:categories',
                'privilege' => 'list-admin',
            ),

            '@separator#2' => array(
            ),

            '/catalog/products/popular-admin' => array(
                'label' => _('Популярные товары'),
                'route' => 'default',
                'resource' => 'mvc:catalog:products',
                'privilege' => 'popular-admin',
                'active' => '/catalog/products/popular-admin/*',
            ),

            '/catalog/products/new-admin' => array(
                'label' => _('Новые товары'),
                'route' => 'default',
                'resource' => 'mvc:catalog:products',
                'privilege' => 'new-admin',
                'active' => '/catalog/products/new-admin/*',
            ),

            '@separator#3' => array(
            ),

            '/pages/index/list' => array(
                'label' => _('Статические страницы'),
                'route' => 'default',
                'resource' => 'mvc:pages:index',
                'privilege' => 'list',
            ),

            '/catalog/categories/list-admin' => array(
                'label' => _('Категории товаров'),
                'route' => 'default',
                'resource' => 'mvc:catalog:categories',
                'privilege' => 'list-admin',
            ),

            '/features/index/list-admin' => array(
                'label' => _('Шаблоны характеристик'),
                'route' => 'default',
                'resource' => 'mvc:features:index',
                'privilege' => 'list-admin',
            ),
            
            '/users/index/list' => array(
                'label' => _('Пользователи'),
                'route' => 'default',
                'resource' => 'mvc:users:list',
                'privilege' => 'list',
            ),

            '/advertisment/index/index' => array(
                'label' => _('Реклама'),
                'route' => 'default',
                'resource' => 'mvc:advertisment:index',
                'privilege' => 'index',
            ),

            '@separator#4' => array(
            ),

            '/default/settings/index' => array(
                'label' => _('Настройка'),
                'route' => 'default',
                'resource' => 'mvc:default:settings',
                'privilege' => 'index',
            ),
            
        );


        $navigation = new ZFEngine_Navigation($menu);
        $navigation->setCurrentActiveClass('adminMainMenuActiveItem');
        $menuShopAndProduct = $this->view->navigationRender($navigation)->renderMenu();
        
$content = <<<HTML
    <ul class="adminMainMenu">
        <li>
            $menuShopAndProduct
        </li>
    </ul>
HTML;

        return $content;
    }

}