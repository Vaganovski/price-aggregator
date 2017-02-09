<?php
/**
 * @copyright Copyright (c) 2009 Tanasiychuk Stepan (http://stfalcon.com)
 */

class Comments_Model_CommentService extends ZFEngine_Model_Service_Database_Abstract
{

    /**
     *
     */
    public function init()
    {
        $this->setFormsModelNamespace(__CLASS__);
        // @todo refact
        $this->_modelName = 'Comments_Model_Comment';
    }


    /**
     * Проверка или существует рутовска категория для данного типа модели
     * если не найдена, создается новая рутовская категория
     *
     * @param string $calledClassName
     * @return Doctrine_Record
     */
    protected function _checkAndCreateRootRecord($entityId)
    {
       $rootComment = $this->getMapper()->findOneByEntity_idAndLevel($entityId, 0);

       if ($rootComment) {
           return $rootComment;
       } else {
           $newComment = new $this->_modelName;
           $newComment->text = 'Root';
           $newComment->entity_id = $entityId;
           $newComment->save();
           $treeObject = $this->getMapper()->getTree();
           $treeObject->createRoot($newComment);
           return $newComment;
       }
    }

    /**
     * Validate and save comment reply
     *
     * @param array $rawData
     * @return boolean
     */
    public function processFormReply($rawData)
    {
        $form = $this->getForm('reply');
        if ($form->isValid($rawData)) {

            $this->getModel()->fromArray($form->getValues());
            $this->getModel()->save();
            $parentId = $rawData['parent_id'];
            $entityId = $rawData['entity_id'];
            $parentComment = $this->getMapper()->find($parentId);
            // если переданная родительская катгория не существует,
            // то получаем рутовскую категорию
            if (!$parentComment) {
                $parentComment = $this->_checkAndCreateRootRecord($entityId);
            }
            $this->getModel()->getNode()->insertAsLastChildOf($parentComment);
            return true;
        } else {
            $form->populate($rawData);
            return false;
        }
    }

    /**
     * Edit
     *
     * @param array $rawData
     * @return boolean
     */
    public function processFormEdit($rawData)
    {
        if (array_key_exists('text', $rawData) && array_key_exists('id', $rawData)) {
            $filter = new Zend_Filter_StripTags();
            $rawData['text'] = $filter->filter($rawData['text']);
            $filter = new Zend_Filter_StringTrim();
            $rawData['text'] = $filter->filter($rawData['text']);
            $filter = new Zend_Filter_HtmlEntities(array('charset'=>'UTF-8'));
            $rawData['text'] = $filter->filter($rawData['text']);

            $this->findModelById($rawData['id']);
            $this->getModel()->text = $rawData['text'];
            $this->getModel()->save();
            
            return true;
        } else {
            return false;
        }
    }
    

    /**
     * Delete
     *
     * @param array $rawData
     * @return boolean
     */
    public function processFormDelete($id)
    {
        try {
            $this->findModelById($id);
            $this->getModel()->getNode()->delete();
        } catch (Zend_Exception $e) {
            return false;
        }
        return  true;
    }

    /**
     * Get Comment by id
     *
     * @param integer $id
     * @return Comments_Model_Comment
     */
    public function getModelById($id)
    {

        $instruction = $this->getMapper()->findOneById((int) $id);

        if ($instruction == false) {
            throw new Exception($this->getView()->translate('Instruction not founds.'));
        }

        return $instruction;
    }
    
    /**
     * find Comment by id and set model object for service layer
     *
     * @param integer $id
     * @return Comments_Model_CommentService
     */
    public function findModelById($id)
    {
        $this->setModel($this->getModelById($id));
        return $this;
    }

     /**
     * Get comments tree by entity id
     *
     * @return Doctrine_Tree|boolean
     */
    public function getCommentsTreeByEnityId($enityId)
    {
        $rootComment = $this->getMapper()->findOneByEntity_idAndLevel($enityId,0);
        if ($rootComment) {
            $treeObject = $this->getMapper()->getTree();
            $treeObject->setBaseQuery($this->getMapper()->findAllByEntityIdAsQuery($enityId));
            return $treeObject->fetchTree(array('root_id'=>$rootComment->id), Doctrine_Core::HYDRATE_RECORD_HIERARCHY);
        }
        return false;
    }


}