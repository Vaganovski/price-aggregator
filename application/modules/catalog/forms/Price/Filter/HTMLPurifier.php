<?php
class Catalog_Form_Price_Filter_HTMLPurifier implements Zend_Filter_Interface
{
    public function filter($value)
    {
        require_once APPLICATION_PATH . '/../library/HTMLPurifier/HTMLPurifier.auto.php';

            $purifierConfig = HTMLPurifier_Config::createDefault();
            $purifierConfig->set("HTML.AllowedElements", array("p", "ul", "ol", "li", "br", "div"));
            $purifierConfig->set('AutoFormat.AutoParagraph', true);
            $purifierConfig->set('AutoFormat.RemoveEmpty.RemoveNbsp', true);
            $purifierConfig->set('AutoFormat.RemoveEmpty', true);
            $purifierConfig->set('Core.EscapeInvalidTags', true);

            $purifier = new HTMLPurifier($purifierConfig);
            return $purifier->purify($value);
    }
}