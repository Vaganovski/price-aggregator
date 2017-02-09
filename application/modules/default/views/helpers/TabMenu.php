<?php
/**
 * @copyright Copyright (c) 2009 Tanasiychuk Stepan (http://stfalcon.com)
 */

/**
 * 
 */
class Default_View_Helper_TabMenu extends ZFEngine_View_Helper_Abstract
{

    /**
     *
     * @return string
     */
    public function tabMenu($type, $count = null)
    {
        // @todo добавити плурал
        $tabs = array();
        switch ($type){
            case 'shops':
                $tabs = array(
                    '/shops/index/list-admin/status/new' => array(
                        'label'         => _('заявки на регистрацию'),
                        'route'         => 'default',
                        'resource'      => 'mvc:shops:index',
                        'privilege'     => 'list-admin',
                        'active'    => array(
                            array('route' => 'default', 'url' => '/shops/index/list-admin/status/new/*')
                        ),
                        'is-active'     => array(
                            'content-append' =>($count) ? " <br/><br/><i>$count<br>новых</i>" :'',
                        ),
                        'pages' => array (
                            '/shops/index/list-admin/status/new/price/absent' => array(
                            'label'         => _('Без прайса'),
                            'route'         => 'default',
                            'resource'      => 'mvc:shops:index',
                            'privilege'     => 'list-admin',
                            'active'    => array(
                                array('route' => 'default', 'url' => '/shops/index/list-admin/status/new/price/absent/*')
                                ),
                            ),   
                            '/shops/index/list-admin/status/new/price/uploaded' => array(
                            'label'         => _('С прайсом'),
                            'route'         => 'default',
                            'resource'      => 'mvc:shops:index',
                            'privilege'     => 'list-admin',
                            'active'    => array(
                                array('route' => 'default', 'url' => '/shops/index/list-admin/status/new/price/uploaded/*')
                                ),
                            )   
                        ),
                    ),
                    '/shops/index/list-admin/status/available' => array(
                        'label' => _('зарегистрированные компании'),
                        'route' => 'default',
                        'resource'      => 'mvc:shops:index',
                        'privilege' => 'list-admin',
                        'active'    => array(
                            array('route' => 'default', 'url' => '/shops/index/list-admin/status/available/*')
                        ),
                        'is-active'     => array(
                            'content-append' => ($count) ? " <i>$count<br>всего</i>" :'',
                        ),
                    ),
                    '/shops/index/list-admin/status/disable' => array(
                        'label'         => _('отключенные компании'),
                        'route'         => 'default',
                        'resource'      => 'mvc:shops:index',
                        'privilege'     => 'list-admin',
                        'active'    => array(
                            array('route' => 'default', 'url' => '/shops/index/list-admin/status/disable/*')
                        ),
                        'is-active'     => array(
                            'content-append' => ($count) ? " <i>$count<br>всего</i>" :'',
                        ),
                    ),
                );
                break;
             case 'reviews':
                $tabs = array(
                    '/catalog/reviews/list-admin/tab/all' => array(
                        'label'         => _('все'),
                        'route'         => 'default',
                        'resource'      => 'mvc:catalog:reviews',
                        'privilege'     => 'list-admin',
                        'is-active'     => array(
                            'content-append' =>($count) ? " <i>$count</i>" :'',
                        ),
                    ),
                    '/catalog/reviews/list-admin/tab/approved' => array(
                        'label' => _('одобренные'),
                        'route' => 'default',
                        'resource'      => 'mvc:catalog:reviews',
                        'privilege' => 'list-admin',
                        'is-active'     => array(
                            'content-append' => ($count) ? " <i>$count<br>всего</i>" :'',
                        ),
                    ),
                    '/catalog/reviews/list-admin/tab/no-approved' => array(
                        'label'         => _('отклоненные'),
                        'route'         => 'default',
                        'resource'      => 'mvc:catalog:reviews',
                        'privilege'     => 'list-admin',
                        'is-active'     => array(
                            'content-append' => ($count) ? " <i>$count<br>всего</i>" :'',
                        ),
                    ),
                );
                break;
             case 'shops-comments':
                $tabs = array(
                    '/shops/comments/list-admin/tab/new' => array(
                        'label'         => _('новые'),
                        'route'         => 'default',
                        'resource'      => 'mvc:shops:comments',
                        'privilege'     => 'list-admin',
                        'is-active'     => array(
                            'content-append' =>($count) ? " <i>$count<br>новых</i>" :'',
                        ),
                    ),
                    '/shops/comments/list-admin/tab/deleted' => array(
                        'label' => _('удаленные'),
                        'route' => 'default',
                        'resource'      => 'mvc:shops:comments',
                        'privilege' => 'list-admin',
                        'is-active'     => array(
                            'content-append' => ($count) ? " <i>$count</i>" :'',
                        ),
                    ),
                );
                break;
        }
        
//        $tabs = array_merge_recursive($tabs, $customTabs);

        $content = '';

        if (!empty ($tabs)) {
            $navigation = new ZFEngine_Navigation($tabs);
            $navigation->setNavigationMenuClass('listAdminFirms');
            $content = $this->view->navigationRender($navigation)->renderMenu();
        }

        return $content;
    }

}