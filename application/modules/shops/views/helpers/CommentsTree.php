<?php

/**
 * Хелпер
 */
class Shops_View_Helper_CommentsTree extends Comments_View_Helper_CommentsTree
{


    /**
     * Блок списока комитариев
     *
     * @param integer $entityId
     * @param Users_Model_UserBase $user
     *
     * @return string
     */
    public function getCommentsTree($entityId, $viewScript = 'list')
    {
        $shop = new Shops_Model_ShopService();
        $shop->findModelById($entityId);
        // для проверки если мой магазин тогда можно ответы делать, если чужой то можно создавать новые.
        $this->view->shop = $shop->getModel();
        $this->view->countComments = $this->_comment->getMapper()->countByShopId($shop->getModel()->id);
        return parent::getCommentsTree($entityId, $viewScript);
       
    }

    /**
     * Recursive method for building comments tree
     * @param $comments
     * @param $parent_id
     *
     * @return string
     */
    protected function _renderTree($comments, $parent_id = 0)
    {
        // инициализация переменных
        $output = ''; 

        // перебор всех коментариев
        foreach ($comments as $item) {
            $this->view->comments = array($item);
            $output .= call_user_func_array(array($this->view, $this->_renderItemMethod), array($item));

        }
        return $output;
    }
}