<?php

class Users_Model_MailerService extends ZFEngine_Model_Service_Database_Abstract
{
    public function init()
    {
        $this->setFormsModelNamespace(__CLASS__);
        // @todo refact
        $this->_modelName = 'Users_Model_Mailer';
    }

    /**
     * Send e-mail
     *
     * @param string $email
     * @param string $subject
     * @param string $body
     */
    public static function sendmail($email, $subject, $body)
    {
        $mail = new Zend_Mail('UTF-8');

        $mail->addTo($email);
        $mail->setSubject($subject);
        $mail->setBodyHtml($body, 'UTF-8', Zend_Mime::ENCODING_BASE64);

        $emailConfig = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('email');
        
        $mail->setFrom($emailConfig['noreply']);
        $mail->send();
    }


    /**
     *  Processing new mailer form
     *  @param  array   $postData
     *  @param  Users_Model_UserService $user
     *  @return boolean
     */
    public function processFormNew($postData, $user)
    {
        $form = $this->getForm('new');
        if ($form->isValid($postData)) {

            switch ($postData['mode']) {
                case 'users':
                    $recipients = $user->getMapper()
                                    ->findAll();
                    break;
                case 'groups':
                    $recipients = $user->getMapper()
                                    ->getAllUsersAssociateWithRoles($postData['user_role']);
                    break;
                default:
                    break;
            }
            
            if ($recipients) {
                $this->getView()->body = $postData['message'];
                $text = $this->getView()->render('/mails/standart.phtml');

                foreach ($recipients as $recipient)
                {
                    $body = preg_replace('/\%username\%/', $recipient->login, $text);
                    $mailer = $this->getModel(true);
                    $mailer->subject = $postData['subject'];
                    $mailer->body = $body;
                    $mailer->email = $recipient->email;
                    $mailer->save();
                }
                return count($recipient);
            }
        } else {
            $this->getForm('new')->populate();
            return false;
        }
    }
}