<?php
/**
 * ZFEngine
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://zfengine.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zfengine.com so we can send you a copy immediately.
 *
 * @category   ZFEngine
 * @package    ZFEngine_Model
 * @copyright  Copyright (c) 2009-2010 Stepan Tanasiychuk (http://stfalcon.com)
 * @license    http://zfengine.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Abstract service for model which does not use database
 *
 * @category   ZFEngine
 * @package    ZFEngine_Model
 * @copyright  Copyright (c) 2009-2010 Stepan Tanasiychuk (http://stfalcon.com)
 * @license    http://zfengine.com/license/new-bsd     New BSD License
 */
abstract class ZFEngine_Model_Service_Abstract
{

    /**
     * @var Zend_View_Interface
     */
    protected $_view;

    /**
     * @var array
     */
    protected $_messages = array();

    const MESSAGE_INFO          = 'info';
    const MESSAGE_WARNING   = 'warning';
    const MESSAGE_ERROR       = 'error';
    const MESSAGE_SUCCESS     = 'success';

    /**
     * @var 
     */
    private $_forms = array(
        '_options' => array(
            'overrideAll' => false
        )
    );


    public function  __construct()
    {
        $this->init();
    }

    public function init()
    {
        // init form code
    }

    // ####################################### //

    public function hasFormInstance($name = 'new')
    {
        if (isset($this->_forms[$name]['instance'])) {
            return true;
        } else {
            return false;
        }
    }

    public function getFormInstance($name = 'new')
    {
        if (isset($this->_forms[$name]['instance'])) {
            return $this->_forms[$name]['instance'];
        } else {
            return false;
        }
    }

    public function setFormInstance($name, $form)
    {
        if (!empty($name) && is_object($form)) {
            $this->_forms[$name]['instance'] = $form;
        }
        return  $this;
    }

    /**
     * Зануление объекта формы
     *
     * @param string $name
     * @return ZFEngine_Model_Service_Abstract
     */
    public function clearForm($name)
    {
        // Если передано имя, и объект с такой формой есть - зануляем
        if (strlen($name) && isset($this->_forms[$name]['instance'])) {
            unset($this->_forms[$name]['instance']);
        }
        return  $this;
    }

    public function getFormPreffix($name = 'new')
    {
        if (isset($this->_forms[$name]['formPreffix']) && !$this->getFormsOption('overrideAll')) {
            return $this->_forms[$name]['formPreffix'];
        } else {
            return $this->getFormsPreffix();
        }
    }

    public function setFormPreffix($name, $preffix)
    {
        if (!empty($name)) {
            $this->_forms[$name]['formPreffix'] = $preffix;
        }
        return  $this;
    }

    public function getFormModelNamespace($name = 'new')
    {
        if (isset($this->_forms[$name]['modelNamespace']) && !$this->getFormsOption('overrideAll')) {
            return $this->_forms[$name]['modelNamespace'];
        } else {
            return $this->getFormsModelNamespace();
        }
    }

    public function setFormModelNamespace($name, $modelNamespace)
    {
        if (!empty($name)) {
            $this->_forms[$name]['modelNamespace'] = $modelNamespace;
        }
        return  $this;
    }




    public function getFormsModelNamespace()
    {
        if (isset($this->_forms['_options']['modelNamespace'])) {
            return $this->_forms['_options']['modelNamespace'];
        } else {
            return false;
        }
    }

    public function setFormsModelNamespace($modelNamespace, $overrideAll = false)
    {
        $this->_forms['_options']['modelNamespace'] = $modelNamespace;
        $this->_forms['_options']['overrideAll'] = $overrideAll;
        return  $this;
    }

    public function getFormsPreffix()
    {
        if (isset($this->_forms['_options']['formPreffix'])) {
            return $this->_forms['_options']['formPreffix'];
        } else {
            return false;
        }
    }

    public function setFormsPreffix($preffix)
    {
        $this->_forms['_options']['formPreffix'] = $preffix;
        return  $this;
    }

    public function getFormsOptions()
    {
        return $this->_forms['_options'];
    }

    public function setFormsOptions(array $options)
    {
        if (is_array($options)) {
            $this->_forms['_options'] = $options;
        }
        return  $this;
    }

    public function getFormsOption($name)
    {
        if (key_exists($name, $this->_forms['_options'])) {
            return $this->_forms['_options'][$name];
        } else {
            return false;
        }
    }

    public function setFormsOption($name, $option)
    {
        if (!empty($name)) {
            $this->_forms['_options'][$name] = $option;
        }
        return  $this;
    }

    /**
     * Создание формы указаного типа
     *
     * @return Zend_Form
     */
    public function getForm($name = 'new')
    {
        if (!$this->hasFormInstance($name)) {

            $class = str_replace('Service', '', str_replace('Model', 'Form', $this->getFormModelNamespace($name)) . '_' . ucfirst($name) ) ;

            $reflectionObj = new ReflectionClass($class);

            $parameters = $reflectionObj->getMethod('__construct')->getParameters();
            
            if (count($parameters) > 1) {
                $this->setFormInstance($name, $reflectionObj->newInstanceArgs(array($this)));
            } else {
                $this->setFormInstance($name, new $class);
            }
        }
        return $this->getFormInstance($name);
    }


    /**
     * Retrieve view object
     *
     * If none registered, attempts to pull from ViewRenderer.
     *
     * @return Zend_View_Interface|null
     */
    public function getView()
    {
        if (null === $this->_view) {
            $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
            $this->setView($viewRenderer->view);
        }

        return $this->_view;
    }

    /**
     * Set view object
     *
     * @param  Zend_View_Interface $view
     * @return ZFEngine_Service_Abstract
     */
    public function setView(Zend_View_Interface $view = null)
    {
        $this->_view = $view;
        return $this;
    }

    public function addMessage($message, $type = self::MESSAGE_INFO)
    {
        switch ($type) {
            case self::MESSAGE_INFO:
            case self::MESSAGE_ERROR:
            case self::MESSAGE_SUCCESS:
            case self::MESSAGE_WARNING:
                break;
            default:
                $type = self::MESSAGE_INFO;
        }
        $this->_messages[] = array($type => $message);
        return $this;
    }

    /**
     * Get messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }

    
    /**
     * Clear messages
     */
    public function clearMessages()
    {
        $this->_messages = array();
    }
}
