<?php

/**
 * Хелпер для получения представления одного коментария
 */
class Shops_View_Helper_RenderCommentMyItem extends Zend_View_Helper_Abstract
{


    /**
     * Get comments tree
     *
     * @return string
     */
    public function renderCommentMyItem($comment)
    {
      // инициализация переменных

        if ($comment->level == 0) return;
        $date = new Zend_Date($comment->created_at);
        $id = 'c' . $comment->id;
        $output = <<<HTML
        <div class="rowOtziv" id = "{$id}">
HTML;

        $output .= <<<HTML
            <div class="leftColOtziv">
                <p class="userOtzivName"><a href="{$this->view->url(array(
                                            'module' => 'users',
                                            'controller' => 'index',
                                            'action' => 'view',
                                            'id' => $comment->User->id,
                                        ), 'default', true)}">{$comment->User->login}</a> {$date->toString('dd.MM.yy')}</p>
                <p class="voteRez">Оценка: 
HTML;
        switch ($comment->mark) {
            case 'good':
                $output .= '<span class="good">хороший магазин</span>';
                break;
            case 'normal':
                $output .= '<span class="normal">нормальный магазин</span>';
                break;
            case 'bad':
                $output .= '<span class="bad">плохой магазин</span>';
                break;
        }
        $output .= <<<HTML
                </p>
                <p>{$this->view->escape($comment->text)}</p>
            </div>
HTML;

        if ($comment->getNode()->hasChildren()){
            $childComment = $comment->getNode()->getChildren()->getFirst();
                $output .= <<<HTML
                <div class="blueOtzivBlock" id="r{$childComment->id}">
                    <h6>Ответ от</h6>
                    <h5> {$childComment->CommentedShop->name}:</h5>
                    <p>{$childComment->text}</p>
                    <span class="blueOtzivBlockChange">Изменить</span>
                        <div class="blueOtzivBlockCor1"></div>
                    <div class="blueOtzivBlockCor2"></div>
                    <div class="blueOtzivBlockCor3"></div>
                    <div class="blueOtzivBlockCor4"></div>
                </div>
HTML;
       } elseif ($this->view->isAllowed($comment->CommentedShop, 'reply-comment')){
           $output .= '<span class="otherPagesWriteOtziv">Ответить</span>';
       }
       $output .= '</div>';

       return $output;
    }

}