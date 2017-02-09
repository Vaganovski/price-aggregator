<?php

class Default_View_Helper_RequestQuery extends Zend_View_Helper_Abstract
{

    /**
    * Возвращает query-параметры из запроса
    *
    * @return string 
    */
    public function requestQuery()
    {
      $query = '';
      $request = Zend_Controller_Front::getInstance()->getRequest();
      if ($request->category):
          $query = explode('?', $request->getRequestUri());
          $query = '?' . $query[count($query) - 1];
      endif;
    return $query;
  }

}