<?php

/**
 * Хелпер
 */
class Advertisment_View_Helper_Banner extends ZFEngine_View_Helper_Abstract
{


    /**
     * Выполняет роль конструктора
     *
     */
    public function Banner()
    {
        if (!$this->_serviceLayer) {
            $this->setServiceLayer('advertisment', 'banner', 'index');
        }
        return $this;
    }

    /**
     * Верхний баннер
     *
     *
     * @return string
     */
    public function getTopBanner()
    {
        if ($this->_getRequest()->getModuleName() == 'default' && $this->_getRequest()->getControllerName() == 'index') {
            $banner = $this->_serviceLayer->getMapper()->findOneByShowOn('on-main', 'top');
        } else {
            $requestUri = $_SERVER['REQUEST_URI'];
            if ($requestUri{0} == '/') {
                $requestUri = substr($requestUri, 1);
            }
            $banner = $this->_serviceLayer->getMapper()->findOneByShowOn('in-place', 'top', $requestUri);
            if (!$banner) {
                $banner = $this->_serviceLayer->getMapper()->findOneByShowOn('anywhere', 'top');
            }
        }

        if ($banner) {
            $this->view->bannerTop = $banner;
            $banner->impressions++;
            $banner->save();
        }

        return $this->view->render($this->getViewScript('top-banner'));
    }
    /**
     * Верхний баннер
     *
     *
     * @return string
     */
    public function getRightBanner()
    {
        if ($this->_getRequest()->getModuleName() == 'default' && $this->_getRequest()->getControllerName() == 'index') {
            $banner = $this->_serviceLayer->getMapper()->findOneByShowOn('on-main', 'right');
        } else {
            $banner = $this->_serviceLayer->getMapper()->findOneByShowOn('anywhere', 'right');
        }

         if ($banner) {
            $this->view->bannerRight = $banner;
            $banner->impressions++;
            $banner->save();
        }

        return $this->view->render($this->getViewScript('right-banner'));
    }


    public function setControllerName($name)
    {
        $this->_controllerName = $name;
        return $this;
    }
}