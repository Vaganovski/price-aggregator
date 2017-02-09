<?php

class Users_Form_User_Edit extends Users_Form_User_Registration
{
    protected $_serviceLayer;
    /**
     * @param object $_serviceLayer
     * @param array $options
     */
    function  __construct($_serviceLayer, $options = null) {
        $this->_serviceLayer = $_serviceLayer;
        parent::__construct($options);
    }

    public function init()
    {
        $decorators = $this->getDecorators();
        parent::init();
        $this->setName(strtolower(__CLASS__));
        $this->removeElement('login');
        $this->removeElement('password');
        $this->removeElement('password_confirm');
        $this->removeElement('captcha');
        $this->name->setLabel(_('Имя'));

        $role = new Zend_Form_Element_Select('role');
        $role->setRequired()
             ->setLabel('Роль')
             ->addMultiOptions(Users_Model_User::getLocalizedRoles());
        $this->addElement($role);

        $this->setElementDecorators(array(
            'ViewHelper',
            'Errors',
            'FormErrors',
            'HtmlTag',
            'Label',
            'DtDdWrapper',
        ));
        
        $this->getElement('submit')->setOrder(100)
                                   ->setLabel(_('Сохранить'))
                                   ->removeDecorator('Label');

        $id = new ZFEngine_Form_Element_Value('id');
        $this->addElement($id);
        $this->setDecorators($decorators);
    }

    /**
     * Validate the form
     *
     * @param  array $data
     * @return boolean
     */
    public function isValid($data)
    {
        // @todo refact
        $user = Doctrine::getTable($this->_serviceLayer->getModelName()) ->find($data['id']);
        if ($data['email'] == $user->email) {
            $this->email->removeValidator('ZFEngine_Validate_Doctrine_NoRecordExist');
        }

        return parent::isValid($data);
    }
}