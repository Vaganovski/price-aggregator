<?php

/**
 * Хелпер для получения представления одного коментария
 */
class Comments_View_Helper_RenderCommentItem extends Zend_View_Helper_Abstract
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
    <div class="comment textmod">
        <div class="inner">
            <div class="hd">
                <img src="/images/user-small.gif" alt="#"/>
            </div>
            <div style="width: 504px;" class="bd">
                <div class="text">{$this->view->escape($comment->text)}</div>
                <div>
                    <small>{$comment->User->login}
                    <span>{$date->toString("dd MMMM YYYY")}</span></small>
                </div>
                <div>
HTML;

        $resource = sprintf('mvc:%s:%s', $this->view->CommentsTree()->getParam('module'),
                $this->view->CommentsTree()->getParam('controller'));

        if ($this->view->IsAllowed($resource, 'reply')) {
           $output .= <<<HTML
            <a href="#{$id}" class="reply-comment" id="o{$comment->id}">
                 {$this->view->translate('Reply')}
            </a>
HTML;
        }
        if ($this->view->IsAllowed($resource, 'delete')) {
           $output .= <<<HTML
            &nbsp;&nbsp;
            <a href="#{$id}" class="delete-comment" id="d{$comment->id}">
                 {$this->view->translate('Delete')}
            </a>
HTML;
        }
        if ($this->view->IsAllowed($resource, 'edit')) {
           $output .= <<<HTML
            &nbsp;&nbsp;
            <a href="#{$id}" class="edit-comment" id="e{$comment->id}">
                 {$this->view->translate('Edit')}
            </a>
HTML;
        }
        $output .= <<<HTML
                </div>
            </div>
        </div>
    </div>
HTML;

        return $output;
    }

}