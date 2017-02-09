<?php

/**
 * Хелпер
 * <?php echo $this->CategoriesTree()
                ->setServiceLayer('products', 'category')
                ->setControllerName('categories')
                ->getCategoriesTree(); ?>
 */
class Categories_View_Helper_CategoriesTree extends ZFEngine_View_Helper_Abstract
{

    /**
     * Выполняет роль конструктора
     *
     */
    public function CategoriesTree()
    {
        if (!$this->_serviceLayer) {
            $this->setServiceLayer('categories', 'category', 'index');
        }
        return $this;
    }

    /**
     * Блок списока комитариев
     *
     * @param integer $entityId
     * @param Users_Model_UserBase $user
     *
     * @return string
     */
    public function getCategoriesTree()
    {
        $this->view->moduleName = $this->getModuleName();
        $this->view->controllerName = $this->getContorllerName();

        $this->view->categories = $this->_serviceLayer->getCategoryCildren(0, '');

        $this->view->headScript()->appendFile('/javascripts/jquery.simple.tree.js');
        $this->view->headLink()->appendStylesheet('/stylesheets/tree.css', 'screen');
$script = <<<JS
var simpleTreeCollection;
jQuery(document).ready(function(){
    simpleTreeCollection = jQuery('.simpleTree').simpleTree({
            autoclose: true,
            afterClick:function(node){
                    //alert("text-"+jQuery('span:first',node).text());
            },
            afterMouseEnter:function(node){
                jQuery(node).find('span.controls-wrapper:first').show();
                jQuery('.text span:first',node).addClass('adminBaraholkaMainListPartsActive');
               // jQuery('a[class=controls]:eq(1)',node).show();
               // jQuery('a[class=controls]:eq(2)',node).show();
            },
            afterMouseLeave:function(node){
                jQuery(node).find('span.controls-wrapper:first').hide();
                jQuery('.text span:first',node).removeClass('adminBaraholkaMainListPartsActive');
            },
            afterDblClick:function(node){
                    //alert("text-"+jQuery('span:first',node).text());
            },
            afterMove:function(destination, source, pos){
                    //alert("destination-"+destination.attr('id')+" source-"+source.attr('id')+" pos-"+pos);
                jQuery.ajax({
                    type: "POST",
                    url: "/{$this->getModuleName()}/{$this->getContorllerName()}/move",
                    data: "destination=" + destination.attr('id') + "&source=" + source.attr('id') + "&position=" + pos,
                    dataType: 'json',
                    success: function(content) {
                        alert(content.error);
                    }
                });
            },
            afterAjax:function()
            {
                    //alert('Loaded');
            },
            animate:true
            //,docToFolderConvert:true
    });
});
JS;
        $this->view->headScript()->appendScript($script);
        
      
        return $this->view->render($this->getViewScript('tree'));
    }

    public function setControllerName($name)
    {
        $this->_controllerName = $name;
        return $this;
    }

}