<?php

/**
 * Хелпер
 */
class Comments_View_Helper_CommentsTree extends Zend_View_Helper_Abstract
{

     /**
     * Объект сервис лайера
     */
    protected $_comment;
     /**
     * Название модуля
     */
    protected $_module;
     /**
     * Название модели
     */
    protected $_model;
    /**
     * Название контролера
     */
    protected $_controller;
    
    protected $_renderItemMethod = 'renderCommentItem';

    static $first = true;

    /**
     * Выполняет роль конструктора
     *
     */
    public function CommentsTree()
    {
        if (!$this->_comment) {
            $this->setServiceLayer('comments', 'comment', 'index');
        }
        return $this;
    }

    /**
     * Блок списока комитариев
     *
     * @param integer $entityId
     * @param string $viewScript
     * @param Users_Model_UserBase $user
     *
     * @return string
     */
    public function getCommentsTree($entityId, $viewScript = 'list')
    {
        $comment = $this->_comment;
        $commentsTree = $comment->getCommentsTreeByEnityId($entityId);
        if ($commentsTree && $commentsTree->getFirst()->getNode()->hasChildren()) {
            $this->view->commentsTree = $this->_renderTree($commentsTree->getFirst()->get('__children'));
        }

        $formNew = $comment->getForm('New');
        $formReply = $comment->getForm('Reply');
//        $formReportSpam = $comment->getForm('ReportSpam');
        if (($auth = Zend_Auth::getInstance()) && $auth->hasIdentity()) {
            $formNew->getElement('user_id')->setValue($auth->getIdentity()->id);
            $formNew->getElement('entity_id')->setValue($entityId);

            $formReply->getElement('user_id')->setValue($auth->getIdentity()->id);
            $formReply->getElement('entity_id')->setValue($entityId);
        }
        $this->view->formCommentsNew = $formNew;
        $this->view->formCommentsReply = $formReply;

        // формирование урлов для джаваскрипт обработчиков
        $replyUrl = $this->view->url(array('module'=>$this->getParam('module'),
                                             'controller'=>$this->getParam('controller'),
                                             'action'=>'reply',
                                             'format' => 'json'), 'default');
        $newUrl = $this->view->url(array('module'=>$this->getParam('module'),
                                             'controller'=>$this->getParam('controller'),
                                             'action'=>'new',
                                             'format' => 'json'), 'default');
        $editUrl = $this->view->url(array('module'=>$this->getParam('module'),
                                             'controller'=>$this->getParam('controller'),
                                             'action'=>'edit',
                                             'format' => 'json'), 'default');
        $deleteUrl = $this->view->url(array('module'=>$this->getParam('module'),
                                             'controller'=>$this->getParam('controller'),
                                             'action'=>'delete',
                                             'format' => 'json'), 'default');

        // сообщения для джаваскрипта
        $messageEmpty = $this->view->translate("Сообщение не должно быть пустым");
        $messageError = $this->view->translate("Произошла ошибка, пожалуйста повторите!");
        $messageDelete = $this->view->translate("Вы уверены, что хотите удалить комментарий?");


        $this->view->headScript()->appendFile('/javascripts/comments.js');
        
        $script = 'var commentsUrlDelete = "' . $deleteUrl . '";
                   var commentsUrlEdit = "' . $editUrl . '";
                   var commentsMessageError = "' . $messageError . '";
                   var commentsMessageEmpty = "' . $messageEmpty . '";
                   var commentsMessageDelete = "' . $messageDelete . '";
                       
                    jQuery(document).ready( function() {
                        commentFormSubmit("' . $formNew->getName() . '", "'. $newUrl .'");
                        commentFormSubmit("' . $formReply->getName() . '", "'. $replyUrl .'");
                    });';
        // Добавляем пути к скриптам представления
        $this->view->headScript()->appendScript($script);
        $path = APPLICATION_PATH . '/modules/comments/views/_abstract/index';
        $this->view->addScriptPath($path);
        $path = APPLICATION_PATH . '/modules/'.$this->getParam('module').'/views/scripts/'.$this->getParam('controller');
        $this->view->addScriptPath($path);

        return $this->view->render('/'. $viewScript .'.phtml');
    }

     /**
     * Устанавливаем нужный сервис лайер
     *
     * @param string $module
     * @param string $model
     * @param string $controller
     *
     */
    public function setServiceLayer($module, $model, $controller) {
        $this->_module = $module;
        $this->_model = $model;
        $this->_controller = $controller;

        $module = ucfirst($module);
        $model = ucfirst($model);
        $class = $module . '_Model_' . $model . 'Service';
        $layer = new  $class;
        if ($layer instanceof Comments_Model_CommentService) {
            $this->_comment = $layer;
        }
        return $this;
    }

     /**
     * Получаем один из параметро: модуль, модель, контролер
     *
     * @param string $name
     *
     */
    public function getParam($name) {
        switch ($name) {
            case 'module':
                return $this->_module;
                break;
            case 'model':
                return $this->_model;
                break;
            case 'controller':
                return $this->_controller;
                break;
        }
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
        $output = ''; $selected = '';

        $output .= (self::$first || $parent_id > 0) ? '<ul class="tree-comments">' : '';
        self::$first = false;
        // перебор всех коментариев
        foreach ($comments as $item) {
            $this->view->comments = array($item);
            $output .= '<li class="comment-container" id="c' . $item->id . '">';
            $output .= call_user_method($this->_renderItemMethod, $this->view, $item);
            if ($item->get('__children')) {
                   //рекурсивно вызываем метод для получения следующого уровня каметариев
                   $output .= $this->_renderTree($item->get('__children'), $item->id);
                $output .= '</li>';
            }
        }
        $output .= '</ul>';
        return $output;
    }

    /**
     * set method name for rendering item
     * @param $methodName
     *
     * @return void
     */
    public function setRenderItemMethod($methodName)
    {
        $this->_renderItemMethod = $methodName;
        return $this;
    }
}