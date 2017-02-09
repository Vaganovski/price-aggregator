<?php

/**
 * Description of App_Settings
 *
 */

class App_Settings
{
    protected $_values = array();

    public function __get($id)
    {
        $this->setUp();
        return $this->_values[$id]['value'];
    }

    public function getValues()
    {
        $this->setUp();
        return $this->_values;
    }

    public function refresh()
    {
        $this->_values = array();
    }

    protected function setUp()
    {
        if(empty($this->_values)){
            $settingsService = new Default_Model_SettingsService();
            $storage = $settingsService->getMapper()->findAll(Doctrine_Core::HYDRATE_ARRAY);

            $result = array();
            foreach ($storage as $record) {
                $result[$record['key']] = $record;
            }
            $this->_values = $result;
        }
    }
}

