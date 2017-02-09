<?php

/**
 *  class Pages_Model_PageService
 */
class Pages_Model_PageService extends ZFEngine_Model_Service_Database_Abstract
{
    /**
     *
     */
    public function init()
    {
        $this->setFormsModelNamespace(__CLASS__);
        // @todo refact
        $this->_modelName = 'Pages_Model_Page';
    }

    /**
     * Processing add page form
     *
     * @param   array $postData
     * @return  boolean|int
     */
    public  function processFormNew($postData)
    {
        $form = $this->getForm('new');
        if ($form->isValid($postData)) {
            try {
                $formValues = $form->getValues();
                $this->_setFormDataToModel($formValues);
                $this->getModel()->save();
                $this->setModel($this->getModel());
                return $this->getModel()->getIncremented();

            } catch (Exception $e) {
                $view = $this->getView();
                $form->addError($view->translate('An error occurred when adding page:') . $e->getMessage());
                $form->populate($postData);
                return false;
            }
        } else {
            $form->populate($postData);
            return false;
        }
    }

    /**
     * Processing edit page form
     *
     * @param array $postData
     * @return boolean
     */
    public function processFormEdit($postData)
    {
        $form = $this->getForm('edit');
        if ($form->isValid($postData)) {
            try {
                $formValues = $form->getValues();
                $this->_setFormDataToModel($formValues);
                $this->getModel()->save();
                return true;
            } catch (Exception $e) {
                $form->addError($this->getView()->translate('An error occurred when changing page:') . $e->getMessage());
                $form->populate($postData);
                return false;
            }
        } else {
            $form->populate($postData);
            return false;
        }
    }

    /**
     * Processing delete page form
     */
    public function processFormDelete($postData)
    {
        if (array_key_exists('submit_ok',$postData)) {
            $this->getModel()->delete();
            return true;
        }
        return false;
    }

    /**
     * Get page by id
     *
     * @param integer $id
     * @return Pages_Model_Page
     */
    public function getPageById($id)
    {
        $page = $this->getMapper()->find($id);

        if (!$page) {
            throw new Exception($this->getView()->translate('Page not found.'));
        }

        return $page;
    }

    /**
     * Get page by id
     *
     * @param string $alias
     * @return Pages_Model_Page
     */
    public function getPageByAlias($alias)
    {
        $page = $this->getMapper()->findOneByAlias($alias);

        if (!$page) {
            throw new Exception($this->getView()->translate('Page not found.'));
        }

        return $page;
    }

    /**
     * Find page by id
     *
     * @param integer $id
     * @return Pages_Model_PageService
     */
    public function findPageById($id)
    {
        $this->setModel($this->getPageById($id));
        return $this;
    }

    /**
     * Find page by id
     *
     * @param integer $alias
     * @return Pages_Model_PageService
     */
    public function findPageByAlias($alias)
    {
        $this->setModel($this->getPageByAlias($alias));
        return $this;
    }

    /**
     *  put data from post form into model
     *  @param  array $formValues
     *  @return void
     */
    protected function _setFormDataToModel($formValues)
    {
        $this->getModel()->alias = $formValues['alias'];
        foreach ($formValues['title'] as $lang => $title) {
            $this->getModel()->setTitle($title, $lang);
        }
        foreach ($formValues['content'] as $lang => $content) {
            $this->getModel()->setContent($content, $lang);
        }
    }
}