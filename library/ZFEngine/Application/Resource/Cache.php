<?php
/**
 * Resource for initialization cache and settings options
 *
 * Original source copied from http://www.zfsnippets.com/snippets/view/id/72
 * @author      Alex Davidovich
 *
 * @category    ZFEngine
 * @package     ZFEngine_Application
 * @subpackage  Resource
 * @copyright   Copyright (c) 2009-Present Stepan Tanasiychuk (http://stfalcon.com)
 * @license     http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZFEngine_Application_Resource_Cache extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * Cache instance
     *
     * @var Zend_Cache
     */
    protected $_cache = null;

    /**
     * Inititalize cache resource
     *
     * @return ZFEngine_Application_Resource_Cache
     */
    public function init()
    {
        if (is_null($this->_cache)) {
            $options = $this->getOptions();

            foreach($options as $key => $value) {
                // Create cache instance
                $this->_cache[$key] = Zend_Cache::factory(
                    $options[$key]['frontend']['adapter'],
                    $options[$key]['backend']['adapter'],
                    $options[$key]['frontend']['params'],
                    $options[$key]['backend']['params']
                );

                // Use as default translate cache
                if (isset($options[$key]['isDefaultTranslateCache']) && true === (bool) $options[$key]['isDefaultTranslateCache']) {
                    Zend_Translate::setCache($this->_cache[$key]);
                }

                // Use as default locale cache
                if (isset($options[$key]['isDefaultLocaleCache']) && true === (bool) $options[$key]['isDefaultLocaleCache']) {
                    Zend_Locale::setCache($this->_cache[$key]);
                }
            }
        }

        return $this;
    }

    /**
     * Get cache by name
     *
     * @param string $name
     * @return Zend_Cache
     */
    public function __get($name)
    {
        return $this->_cache[$name];
    }

}