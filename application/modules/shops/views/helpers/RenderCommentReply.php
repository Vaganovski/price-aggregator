<?php

/**
 * Хелпер для получения представления одного коментария
 */
class Shops_View_Helper_RenderCommentReply extends Zend_View_Helper_Abstract
{


    /**
     * Get comments tree
     *
     * @return string
     */
    public function renderCommentReply($comment)
    {
      // инициализация переменных

        if ($comment->level == 0) return;
        $date = new Zend_Date($comment->created_at);
        
        $output = <<<HTML
            <div class="blueOtzivBlock" id="r{$comment->id}">
                <h6>Ответ от</h6>
                <h5> {$comment->CommentedShop->name}:</h5>
                <p>{$comment->text}</p>
                <span class="blueOtzivBlockChange">Изменить</span>
                    <div class="blueOtzivBlockCor1"></div>
                <div class="blueOtzivBlockCor2"></div>
                <div class="blueOtzivBlockCor3"></div>
                <div class="blueOtzivBlockCor4"></div>
            </div>
HTML;

       return $output;
    }

}