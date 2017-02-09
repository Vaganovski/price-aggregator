<?php
abstract class Comments_Form_Comment_Abstract extends Zend_Form
{

    public function init()
    {
        $text = new Zend_Form_Element_Textarea('text');
        $text->setAttribs(array(
            'rows'=>4
        ));
        $text->setRequired();
        $text->addFilter(new Zend_Filter_StripTags());
        $text->addFilter(new Zend_Filter_StringTrim());
        $text->addFilter(new Zend_Filter_HtmlEntities(array('charset'=>'UTF-8')));
        $this->addElement($text);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setIgnore(true);
        $this->addElement($submit);

        $userId = new Zend_Form_Element_Hidden('user_id');
        $this->addElement($userId);

        $shopId = new Zend_Form_Element_Hidden('entity_id');
        $this->addElement($shopId);

        $parentId = new Zend_Form_Element_Hidden('parent_id');
        $this->addElement($parentId);

        $this->setElementDecorators(array(
            'ViewHelper',
        ), array('submit', 'text'), false);

        return $this;
    }
}

?>
