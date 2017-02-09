<?php

class Users_Model_User extends Users_Model_Base_User
{

    /**
     * manager role name
     */
    const ROLE_MANAGER    = 'manager';

    public static $localizedRoles;
/**
     * Возвращает локализованный статус
     *
     * @return string
     */
    public static function getLocalizedRoles()
    {
        $view = Zend_Layout::getMvcInstance()->getView();
        if (!self::$localizedRoles) {
            self::$localizedRoles = array(
                Users_Model_User::ROLE_GUEST => $view->translate('Гость'),
                Users_Model_User::ROLE_MEMBER => $view->translate('Пользователь'),
                Users_Model_User::ROLE_MERCHANT => $view->translate('Продавец'),
                Users_Model_User::ROLE_MANAGER => $view->translate('Менеджер'),
                Users_Model_User::ROLE_ADMINISTRATOR => $view->translate('Администратор'),
            );
        }
        return self::$localizedRoles;
    }

    public function getLocalizedRole() {
        $roles = self::getLocalizedRoles();
        return $roles[$this->role];
    }
}
