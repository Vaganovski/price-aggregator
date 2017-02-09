<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    /**
     * @var  Zend_Config
     */
    protected $_config;

    /**
     * @var  Zend_Locale
     */
    protected $_locale = null;

    protected function _initViewScripts()
    {
        // @todo refact?
        Zend_Controller_Action_HelperBroker::addHelper(new ZFEngine_Controller_Action_Helper_ViewScripts());
    }

     /**
     * Подключение локали
     *
     * @return Zend_Locale
     */
    public function _initLocale()
    {
        if (!$this->_locale) {
            $this->_locale = new Zend_Locale(array_pop($this->getOption('locales')));
            Zend_Locale::setDefault($this->_locale);
            Zend_Registry::set('Zend_Locale', $this->_locale);
            $this->bootstrap('View');
            $this->getResource('View')->locale = $this->_locale;
        }
        return $this->_locale;
    }

     /**
     * Подключение переводов для валидаторов
     *
     * @return Zend_Translate
     */
    public function _initTranslate()
    {
        $this->bootstrapLocale();

        $translator = new Zend_Translate(
            'array',
            realpath(APPLICATION_PATH . '/../resources/languages'),
            $this->_locale,
            array(
                'scan' => Zend_Translate::LOCALE_DIRECTORY,
                'disableNotices' => true,
                'logUntranslated' => false,
            )
        );
        /**
         * @todo костыль для работы plural
         */
        Zend_Registry::set('Zend_Translate', $translator);

        Zend_Validate_Abstract::setDefaultTranslator($translator->getAdapter());

        return $translator;
    }

    /**
     * Save config in registry. For old code uses
     *
     * @return Zend_Config
     */
    protected function _initConfig()
    {
        if (!$this->_config) {
            $this->_config = new Zend_Config($this->getOptions(), true);
            Zend_Registry::set('config', $this->_config);
        }
        return $this->_config;
    }

    /**
     *  Save settings in registry
     *
     * @return App_Settings
     */
    protected function _initSettings()
    {
        $settings = new App_Settings();

        Zend_Registry::set('settings', $settings);

        return $settings;
    }


    /**
     * init Modules
     */
    protected function _initBootstrapModules()
    {
        /**
         * @todo убрать этот костыль
         */
        $this->bootstrapModules();
        return;
    }

    /**
     * Role
     *
     * @return string
     */
    protected function _initRole()
    {
        $auth = Zend_Auth::getInstance();
        // Get user role
        $role = ($auth->hasIdentity() && !empty($auth->getIdentity()->role))
            ? $auth->getIdentity()->role : Users_Model_User::ROLE_GUEST;

        return $role;
    }

    /**
     * Setup ACL
     *
     * @return Zend_Acl
     */
    protected function _initAcl()
    {
        $this->bootstrapRole();
        $role = $this->getResource('role');

        $this->bootstrap('Cache');
        $cache = $this->getResource('Cache');
        $cacheCore = $cache->core;

        $id = 'acl';
        if (!($acl = $cacheCore->load($id))) {
            $acl = require_once 'configs/acl.php';
        }

        // Loading plugin to access controll
        $this->bootstrap('frontcontroller');
        $front = $this->getResource('frontcontroller');
        $front->registerPlugin(new ZFEngine_Controller_Plugin_Acl($acl, $role));
        
        Zend_Controller_Action_HelperBroker::addHelper(new ZFEngine_Controller_Action_Helper_Acl());

        // Acl cling to Zend_Navigator
        Zend_View_Helper_Navigation_HelperAbstract::setDefaultAcl($acl);
        Zend_View_Helper_Navigation_HelperAbstract::setDefaultRole($role);

        return $acl;
    }

}
