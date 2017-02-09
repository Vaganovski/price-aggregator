<?php

class Users_Model_UserBaseService extends ZFEngine_Model_Service_Database_Abstract
{
    /**
     *
     */
    public function init()
    {
        $this->setFormsModelNamespace(__CLASS__);
        // @todo refact
        $this->_modelName = 'Users_Model_User';
    }

    /**
     * Обработка формы логина
     *
     * @param array $postData
     * @return boolean
     */
    public function processFormLogin($postData)
    {
        $form = $this->getForm('login');

        if ($form->isValid($postData)) {
            if (Users_Model_User::authenticate($form->getValue('login'), $form->getValue('password'))) {
                return true;
            } else {
                $form->addErrorMessage($this->getView()->translate('Ошибка авторизации. Проверьте правильность ввода логина и пароля.'));
                return false;
            }
        } else {
            $form->populate($postData);
            return false;
        }
    }


    /**
     * Обработка формы востановления пароля
     *
     * @param array $postData
     * @return boolean
     */
    public function processFormPasswordRestore($postData)
    {
        $form = $this->getForm('passwordRestore');

        if ($form->isValid($postData)) {
            $this->findUserByLogin($form->getValue('login'));

            $this->getModel()->password_reset_code = $this->getModel()->generateRestoreCode();
            $this->getModel()->save();

            $this->getView()->user = $this->getModel();
            $body = $this->getView()->render('/mails/password-restore.phtml');
            try {
                // send link to password reset
                Users_Model_MailerService::sendmail(
                        $this->getModel()->email,
                        $this->getView()->translate('Восстановление пароля'),
                        $body
                );
                return true;
            } catch (Zend_Exception $e) {
                throw new Exception(
                    $this->getView()->translate('Произошла ошибка при отправке Вам на почту ссылки для восстановления пароля!') . '<br/>' .
                    $this->getView()->translate('Свяжитесь пожалуйста с администратором сайта.')
                );
            }
        } else {
            $form->populate($postData);
            return false;
        }
    }


    /**
     * Обработка формы востановления пароля
     *
     * @param array $postData
     * @return boolean
     */
    public function processFormPasswordReset($postData)
    {
        $form = $this->getForm('passwordReset');

        if ($form->isValid($postData)) {
            
            $this->getModel()->password = $form->getValue('password');
            $this->getModel()->password_reset_code = null;
            $this->getModel()->save();
            
            $this->auth();
            
            return true;
        } else {
            $form->populate($postData);
            return false;
        }
    }


    /**
     * Обработка формы востановления пароля
     *
     * @param array $postData
     * @return boolean
     */
    public function processFormPasswordChange($postData)
    {
        $form = $this->getForm('passwordChange');

        if ($form->isValid($postData)) {
            $this->getModel()->changePassword($postData['password'], $postData['old_password']);
            $this->getModel()->save();

            $this->auth();
            
            return true;
        } else {
            $form->populate($postData);
            return false;
        }
    }


    /**
     * Обработка формы редактирование профиля пользователя
     *
     * @param array $postData
     * @return boolean
     */
    public function processFormEdit($postData)
    {
        $form = $this->getForm('edit');

        if ($form->isValid($postData)) {
            $this->getModel()->fromArray($form->getValues());
            $this->getModel()->save();
            return true;
        } else {
            $form->populate($postData);
            return false;
        }
    }
    

    /**
     * Обработка формы удаления пользователя
     *
     * @param array $postData
     * @return boolean
     */
    public function processFormDelete($postData)
    {
        if (array_key_exists('submit_ok', $postData)) {
            $this->getModel()->delete();
            return true;
        }
        return false;
    }


    /**
     * Авторизация пользователя
     */
    public function auth()
    {
        $user = new stdClass();
        $data = $this->getModel()->toArray();
        unset($data['password_hash'], $data['password_salt']);
        foreach ($data as $key => $value) {
            $user->$key = $value;
        }
        Zend_Auth::getInstance()->getStorage()->write($user);

        // remember user for 2 weeks
        Zend_Session::rememberMe(60*60*24*14);
    }

    /**
     * Сброс авторизации
     */
    public function logout()
    {
        Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::forgetMe();
    }


    /**
     * Проверка кода сброса пароля
     *
     * @param string $code код сброса пароля
     * @return boolean
     */
    public function checkResetPasswordCode($code)
    {
        if (strlen($code) && $code == $this->getModel()->password_reset_code) {
            $timeDiff = time() - strtotime($this->getModel()->password_reset_code_created_at);
            if ($timeDiff > 7200) {
                throw new Exception($this->getView()->translate('Прошло больше 2х часов с момента генерации вашего кода для восстановления пароля.'));
            }
            return true;
        } else {
            return false;
        }
    }


    /**
     * Проверка кода сброса пароля
     *
     * @param string $code код активации
     * @return boolean
     */
    public function checkActivation($code)
    {
        if (!$this->getModel()->activated && $code == $this->getModel()->activation_code) {
            $this->getModel()->activated = true;
            $this->getModel()->save();
            return true;
        } elseif ($this->getModel()->activated) {
            throw new Exception(sprintf($this->getView()->translate('Пользователь %s уже активирован.'), $this->getModel()->login));
        } else {
            throw new Exception($this->getView()->translate('Неправильный код активации.'));
        }

    }


    /**
     * Send e-mail user
     *
     * @param string $title
     * @param string $body
     */
//    public function sendmail($title, $body)
//    {
//        User_Model_Service_Mailer::sendmail($this->email, $title, $body);
//    }

    /**
     * Get user by auth
     *
     * @return Users_Model_User
     */
    public function getUserByAuth()
    {
        if (!$this->getUserAuthIdentity()) {
            throw new Exception($this->getView()->translate('User not found.'));
        }
        return $this->getMapper()->findOneBy('id', $this->getUserAuthIdentity()->id);
    }

    /**
     * Get user auth identity
     *
     * @return mixed|null
     */
    public function getUserAuthIdentity()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            return null;
        }
        return $auth->getIdentity();
    }

    /**
     * find user by auth and set model object for service layer
     *
     * @return Users_Model_UserService
     */
    public function findUserByAuth()
    {
        $this->setModel($this->getUserByAuth());
        return $this;
    }

    /**
     * Get user by id
     *
     * @param integer $id
     * @return Users_Model_User
     */
    public function getUserById($id)
    {
        $user = $this->getMapper()->findOneById((int) $id);
       
        if (!$user) {
            throw new Exception($this->getView()->translate('User not founds.'));
        }

        return $user;
    }
    
    /**
     * find user by id and set model object for service layer
     *
     * @param integer $id
     * @return Users_Model_UserService
     */
    public function findUserById($id)
    {
        $this->setModel($this->getUserById($id));
        return $this;
    }
    

    /**
     * Get user by email
     *
     * @param string $email
     * @return User
     */
    public function getUserByEmail($email)
    {
        $user =$this->getMapper()->findOneByEmail($email);
        if (!$user) {
            throw new Exception($this->getView()->translate('Пользователь не найден.'));
        }

        return $user;
    }
    
    /**
     * find user by email and set model object for service layer
     *
     * @param integer $email
     * @return Users_Model_UserService
     */
    public function findUserByEmail($email)
    {
        $this->setModel($this->getUserByEmail($email));
        return $this;
    }

    /**
     * Get user by login
     *
     * @param string $login
     * @return User
     */
    public function getUserByLogin($login)
    {
        $user =$this->getMapper()->findOneByLogin($login);
        if (!$user) {
            throw new Exception($this->getView()->translate('Пользователь не найден.'));
        }

        return $user;
    }


    /**
     * find user by login and set model object for service layer
     *
     * @param string $login
     * @return Users_Model_UserService
     */
    public function findUserByLogin($login)
    {
        $this->setModel($this->getUserByLogin($login));
        return $this;
    }


    /**
     * Get user by id or auth
     *
     * @param integer $id
     * @return Users_Model_User
     */
    public function getUserByIdOrAuth($id)
    {
        if ($id) {
            $user = $this->getUserById($id);
        } else {
            $user = $this->getUserByAuth();
        }
        
        if (!$user) {
            throw new Exception($this->getView()->translate('User not founds.'));
        }

        return $user;
    }


    /**
     * find user by id or auth
     *
     * @param integer $id
     * @return Users_Model_UserBaseService
     */
    public function findUserByIdOrAuth($id)
    {
        $this->setModel($this->getUserByIdOrAuth($id));
        return $this;
    }

}