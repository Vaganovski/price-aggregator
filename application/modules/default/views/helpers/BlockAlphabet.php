<?php

/**
 * Хелпер для генерации блока букв
 */
class Default_View_Helper_BlockAlphabet extends ZFEngine_View_Helper_Abstract
{
    public function blockAlphabet($data)
    {
        $this->view->alpabetData = $data;
        $content = $this->view->render($this->getViewScript());
        unset($this->view->alpabetData);
        return $content;
    }
}
