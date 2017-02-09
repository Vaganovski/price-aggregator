<?php

class Marketplace_SearchController extends Zend_Controller_Action
{

    public function intit()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('index', 'html')->initContext();
        parent::init();
    }


    /**
     * Поиск
     */
    public function indexAction()
    {
        $this->view->BreadCrumbs()->appendBreadCrumb('Барахолка', $this->view->url(array(
                                        'module'=>'marketplace',
                                        'controller'=>'categories',
                                        'action'=>'list'
                                    ), 'default', true));
        $this->view->setTitle(_('Поиск'));
        $keywords = $this->_request->getParam('keywords');

        if (!$this->_request->city) {
            $city = 'Алматы';
        } else {
            $city = $this->_request->city;
        }
        $this->view->city = $city;
        $this->view->keywords = $keywords;

        $search = new Marketplace_Model_SearchService();
        $hits = $search->search($keywords);
        $productsIdArray = array();
        $productsIdAndScoreArray = array();
        foreach ($hits as $hit){
            $productsIdArray[] = $hit->product_id;
//            $productsIdAndScoreArray[] = array('product_id' => $hit->product_id, 'score' => $hit->score);
        }

        if (!empty($productsIdArray)) {
            $product = new Marketplace_Model_ProductService();
            $query = $product->getMapper()->findAllByApprove(2, true, $productsIdArray, $this->_request->type, $city);
            $paginator =  $this->_helper->paginator->getPaginator($query);
            $this->view->paginator = $paginator;
            $this->view->products = $paginator->getCurrentItems();
        } else {
            $this->view->searchFall = 1;
        }
        $this->view->type = $this->_request->type;
    }

    private function _sortArray(&$array, $field , $sortOrder = 'ASC')
    {
        $size = count($array);
        for( $i = 0; $i < $size; $i++) {            // i - номер прохода
            for($j = $size - 1; $j > $i; $j--) {     // внутренний цикл прохода
                if ($sortOrder == 'DESC') {
                    if ($array[$j-1][$field] < $array[$j][$field]) {
                        $x = $array[$j - 1];
                        $array[$j - 1] = $array[$j];
                        $array[$j] = $x;
                    }
                } else {
                    if ($array[$j-1][$field] > $array[$j][$field]) {
                        $x = $array[$j - 1];
                        $array[$j - 1] = $array[$j];
                        $array[$j] = $x;
                    }
                }
            }
         }
    }
}
