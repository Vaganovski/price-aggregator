<?php

class Default_View_Helper_QueryUrl extends Zend_View_Helper_Abstract
{

    /**
    * Generates an url given the name of a route.
    *
    * @access public
    *
    * @see    Zend_View_Helper::url()
    * @param  array $urlOptions Options passed to the assemble method of the Route object.
    * @param  mixed $name The name of a Route to use. If null it will use the current Route
    * @param  bool $reset Whether or not to reset the route defaults with those provided
    * @return string Url for the link href attribute.
    */
    public function queryUrl(array $urlOptions = array(), $name = null, $reset = false)
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();

        $urlQuery = '';
        foreach ($request->getQuery() as $key=>$value) {
            $urlQuery .= $key . '=' . $this->view->escape($value) . '&';
        }

        $url = $this->view->url($urlOptions, $name, $reset);
        if (strlen($urlQuery)) {
            $url .= '?' . $urlQuery;
        }
        return $url;
  }

}