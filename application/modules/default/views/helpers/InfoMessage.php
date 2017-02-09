<?php

class Default_View_Helper_InfoMessage extends Zend_View_Helper_Abstract
{

    /**
     * render info message
     *
     */
    public function infoMessage($message, $type = 'info')
    {
        $content = '<ul class="info-message">';
            $content .= '<li class="'.$type.'">';
                $content .= $message;
            $content .= '</li>';
        $content .= '</ul>';
        return $content;
    }

}