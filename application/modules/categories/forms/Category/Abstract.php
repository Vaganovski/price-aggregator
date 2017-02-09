<?php

abstract class Categories_Form_Category_Abstract extends Zend_Form
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

    /**
     * Form initialization
     *
     * @return void
     */
    public function init()
    {
        $this->setName(strtolower(__CLASS__));

        $subFormCategory = new Zend_Form_SubForm();
        $category = new Zend_Form_Element_Select('0');
        $category->setLabel(_('Категория'));
        $category->setAttrib('id', 'category_1');
        $subFormCategory->addElement($category);
        $this->addSubForm($subFormCategory, 'category');
  
        $title = new Zend_Form_Element_Text('title');
        $title->setLabel(_('Название'))
            ->setRequired()
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim');
        $this->addElement($title);

        $alias = new Zend_Form_Element_Text('alias');
        $alias->setLabel(_('Алиас'))
            ->setDescription(_('Название в строке адреса'))
            ->addValidator('regex', false, array("/^[a-z0-9\-_]{2,32}$/i"))
               // @todo refact нужна автоматическая подстановка модели в валидатор
            ->addValidator(new ZFEngine_Validate_Doctrine_NoRecordExist($this->_serviceLayer->getModelName(), 'alias'))
            ->addFilter('StringTrim')
            ->setRequired();
        $this->addElement($alias);

        $shortDescription = new Zend_Form_Element_Textarea('short_description');
        $shortDescription->setLabel(_('Краткое описание'))
                         ->addFilter('StringTrim');
        $this->addElement($shortDescription);

        $description = new Zend_Form_Element_Textarea('description');
        $description->setLabel(_('Описание'))
            ->addFilter('StringTrim');
        $this->addElement($description);

        $this->getView()->headScript()->appendFile('/javascripts/jq-cascade.js');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setIgnore(true)->setOrder(100);
        $this->addElement($submit);

        return $this;
    }

}