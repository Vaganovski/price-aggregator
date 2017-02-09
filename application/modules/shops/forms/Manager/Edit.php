<?php

class Shops_Form_Manager_Edit extends Shops_Form_Manager_New
{
    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->setName(strtolower(__CLASS__));

        $this->getElement('submit')
            ->setLabel(_('Сохранить'))
            ->setDescription('');

        $this->getElement('password')->setLabel(_('Сменить пароль'));
        
        $id = new ZFEngine_Form_Element_Value('id');
        $this->addElement($id);
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
        $manager = Doctrine_Core::getTable('Users_Model_User')->find($data['id']);
        if ($data['login'] == $manager->login) {
            $this->login->removeValidator('ZFEngine_Validate_Doctrine_NoRecordExist');
        }
        
        if (!strlen($data['password'])) {
            $this->password->setRequired(false);
        }

        return parent::isValid($data);
    }

}