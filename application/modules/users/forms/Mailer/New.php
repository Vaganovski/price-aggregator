<?php
/**
 *  User_Form_Mailer_New
 *  new Mailer form 
 */
class Users_Form_Mailer_New extends Zend_Form
{
    /**
     *  code after __construct
     */
    public function init()
    {
        $this->setName(strtolower(__CLASS__));

        $mode = new Zend_Form_Element_Radio('mode');
        $mode->setMultiOptions(array(
                                'users'   => 'all users',
                                'groups'  => 'group of users'))
                ->setLabel(_('To:'))
                ->setRequired(false)
                ->setValue('users');
        $this->addElement($mode);

        $groups = new Zend_Form_Element_Multiselect('user_role');
        $groups->setRequired(false);

        $this->addElement($groups);

$script = <<<JS
$(document).ready(function () {
    $('#user_role').hide();

    $('#mode-groups').click(function(){
        if (this.checked) $('#user_role').show();
    })

    $('#mode-users').click(function(){
        if (this.checked) $('#user_role').hide();
    })
})
JS;
        $this->getView()->headScript()->appendScript($script);

        $message = new Zend_Form_Element_Text('subject');
        $message->setLabel(_('Тема'))
                ->addFilter(new Zend_Filter_StripTags())
                ->addFilter(new Zend_Filter_StringTrim())
                ->addFilter(new Zend_Filter_HtmlEntities())
                ->setRequired(true);
        $this->addElement($message);

        $message = new Zend_Form_Element_Textarea('message');
        $message->setLabel(_('Сообщение'))
                ->addFilter(new Zend_Filter_StripTags())
                ->addFilter(new Zend_Filter_StringTrim())
                ->addFilter(new Zend_Filter_HtmlEntities())
                ->setRequired(true);
        $this->addElement($message);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel(_('Отправить'))
               ->setIgnore(true);
        $this->addElement($submit);

        return $this;
    }

    /**
     * override function populate
     */
    public function populate()
    {

    }
    
}