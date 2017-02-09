<?php

/**
 * Хелпер для получения представления одного коментария
 */
class Shops_View_Helper_RenderCommentItem extends Zend_View_Helper_Abstract
{


    /**
     * Get comments tree
     *
     * @return string
     */
    public function renderCommentItem($comment)
    {
      // инициализация переменных

        if ($comment->level == 0) return;
        $date = new Zend_Date($comment->created_at);
        $id = 'c' . $comment->id;
        $output = <<<HTML
        <div class="rowOtziv" id = "{$id}">
HTML;
        if ($comment->getNode()->hasChildren()){
            $childComment = $comment->getNode()->getChildren()->getFirst();
                $output .= <<<HTML
                    <div class="ourAnswer">
                        <div class="topBord"></div>
                        <div class="botBord"></div>
                        <h4>Ответ от</h4>
                        <h5>{$childComment->CommentedShop->name}:</h5>
                        <p>{$childComment->text}</p>
                    </div>
HTML;
        }
        $output .= <<<HTML
            <div class="leftColOtziv">
                <p class="userOtzivName"><a href="{$this->view->url(array(
                                            'module' => 'users',
                                            'controller' => 'index',
                                            'action' => 'profile',
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
       $output .= '</div>';

       return $output;
    }

}