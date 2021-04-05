<?php

/**
 * Class LoginController
 */
class LoginController extends Zend_Controller_Action
{
    /**
     * init
     */
    public function init()
    {
        $this->view->doctype('XHTML1_STRICT');
        $this->_helper->layout->setLayout('admin');
    }

    /**
     * @throws Zend_Form_Exception
     */
    public function loginAction()
    {
        $form = new Zf1_Form_Login;
        $this->view->form = $form;

        // check for valid input
        // authenticate using adapter
        // persist user record to session
        // redirect to original request URL if present
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $values = $form->getValues();
                $adapter = new Zf1_Auth_Adapter_Doctrine(
                    $values['username'], $values['password']
                );
                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($adapter);
                if ($result->isValid()) {
                    $session = new Zend_Session_Namespace('zf1.auth');
                    $session->user = $adapter->getResultArray('Password');
                    if (isset($session->requestURL)) {
                        $url = $session->requestURL;
                        unset($session->requestURL);
                        $this->_redirect($url);
                    } else {
                        $this->_helper->getHelper('FlashMessenger')
                            ->addMessage('You were successfully logged in.');
                        $this->_redirect('/admin/login/success');
                    }
                } else {
                    $this->view->message = 'You could not be logged in. Please try again.';
                }
            }
        }
    }

    /**
     * success action
     */
    public function successAction()
    {
        if ($this->_helper->getHelper('FlashMessenger')->getMessages()) {
            $this->view->messages = $this->_helper
                ->getHelper('FlashMessenger')
                ->getMessages();
        } else {
            $this->_redirect('/');
        }
    }

    /**
     * logout action
     */
    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::destroy();
        $this->_redirect('/admin/login');
    }
}
