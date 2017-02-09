<?php
/**
 * @copyright Copyright (c) 2009 Tanasiychuk Stepan (http://stfalcon.com)
 */

/**
 * Хелпер для загрузки меню пользователя
 */
class Default_View_Helper_BreadCrumbs extends Zend_View_Helper_Placeholder_Container_Standalone
{
    public function BreadCrumbs()
    {
        return $this;
    }

    /**
     * @return string
     */
    public function appendBreadCrumb($title, $url = NULL)
    {
        return $this->getContainer()->append(array('title' => $title, 'url' => $url));
    }

    public function toString($indent = null)
    {
        $titles = $this->view->headTitle();

        $output = '<ul class="innerChrumb">
		<li><a href="/">Главная</a></li>';
        $this->getContainer()->ksort();
        foreach ($this as $item) {
            $output .= '<li>';
            if ($item['url']) {
                $str = '<a href="' . $item['url'] . '">%s</a>';
            } else {
                $str = '%s';
            }
            $output .= sprintf($str, $item['title']) . '</li>';
        }
        if (count($titles) > 1) {
            $output .= '<li>' . $titles[0] . '</li>';
        }
        $output .= '</ul>';
        return $output;
    }
}
