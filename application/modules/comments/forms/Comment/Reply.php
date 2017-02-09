<?php

/**
 * Create new comment form
 */
class Comments_Form_Comment_Reply extends Comments_Form_Comment_Abstract
{

    public function init()
    {
        $this->setName(strtolower(__CLASS__));

        parent::init();

        $this->submit->setLabel(_('Ответить'));

$script = <<<JS
            jQuery(document).ready( function() {
                jQuery("a.reply-comment").live("click", function(){
                        var id = jQuery(this).attr('id');
                        id = id.substr(1);
                        jQuery("#reply-form").appendTo("li#c" + id + ">.koment");
                        jQuery("#reply-form").show();
                        jQuery("#reply-form textarea[name=text]").val('');
                        jQuery("#{$this->getName()} input[name=parent_id]").val(id);
                        return false;
                });
            });
JS;

        $this->getView()->headScript()->appendScript($script);

        return $this;
    }
}