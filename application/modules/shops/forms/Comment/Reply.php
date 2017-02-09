<?php

/**
 * Create new comment form
 */
class Shops_Form_Comment_Reply extends Shops_Form_Comment_Abstract
{

    public function init()
    {
        $this->setName(strtolower(__CLASS__));

        parent::init();

        $this->submit->setLabel(_('Ответить'));
        $this->mark->setRequired(false);

$script = <<<JS
            jQuery(document).ready( function() {
                jQuery(".otherPagesWriteOtziv").live("click", function(){
                        var id = jQuery(this).parent().attr('id');
                        id = id.substr(1);
                        var oldId = jQuery("#reply-form").parent().parent().attr('id');
                        forAppend = jQuery(this).parent().find(".leftColOtziv");
                        jQuery("#reply-form").appendTo(forAppend);
                        jQuery("#reply-form").show();
                        jQuery("#reply-form textarea[name=text]").val('');
                        jQuery("#{$this->getName()} input[name=parent_id]").val(id);
                        jQuery(this).hide();
                        if (oldId) {
                            replyId = jQuery("#" + oldId + " .blueOtzivBlock").attr('id')
                            if (!replyId) {
                                jQuery("#" + oldId + " .otherPagesWriteOtziv").show();
                            }
                        }
                        return false;
                });
            });
JS;

        $this->getView()->headScript()->appendScript($script);
        $this->setDecorators(array(
            'FormErrors',
            array('ViewScript', array('viewScript' => 'forms/reply.phtml'))
        ));
        return $this;
    }
}