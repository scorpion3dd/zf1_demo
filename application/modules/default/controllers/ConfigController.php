<?php

/**
 * Class ConfigController
 */
class ConfigController extends Zend_Controller_Action
{
    protected $localConfigPath;

    /**
     * init
     */
    public function init()
    {
        // set doctype
        $this->view->doctype('XHTML1_STRICT');

        // retrieve path to local config file
        $config = $this->getInvokeArg('bootstrap')->getOption('configs');
        $this->localConfigPath = $config['localConfigPath'];
    }

    /**
     * action to handle admin URLs
     */
    public function preDispatch()
    {
        // set admin layout
        // check if user is authenticated
        // if not, redirect to login page
        $url = $this->getRequest()->getRequestUri();
        $this->_helper->layout->setLayout('admin');
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $session = new Zend_Session_Namespace('zf1.auth');
            $session->requestURL = $url;
            $this->_redirect('/admin/login');
        }
    }

    /**
     * Action to save configuration data
     * @throws Zend_Config_Exception
     * @throws Zend_Form_Exception
     */
    public function indexAction()
    {
        // generate input form
        $form = new Zf1_Form_Configure();;
        $this->view->form = $form;

        // if config file exists
        // read config values
        // pre-populate form with values
        if (file_exists($this->localConfigPath)) {
            $config = new Zend_Config_Ini($this->localConfigPath);
            $data['defaultEmailAddress'] = $config->global->defaultEmailAddress;
            $data['salesEmailAddress'] = $config->user->salesEmailAddress;
            $data['itemsPerPage'] = $config->admin->itemsPerPage;
            $data['displaySellerInfo'] = $config->user->displaySellerInfo;
            $data['logExceptionsToFile'] = $config->global->logExceptionsToFile;
            $form->populate($data);
        }

        // test for valid input
        // if valid, create new config object
        // create config sections
        // save config values to file,
        // overwriting previous version
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $values = $form->getValues();
                $config = new Zend_Config(array(), true);
                $config->global = array();
                $config->admin = array();
                $config->user = array();
                $config->global->defaultEmailAddress = $values['defaultEmailAddress'];
                $config->user->salesEmailAddress = $values['salesEmailAddress'];
                $config->admin->itemsPerPage = $values['itemsPerPage'];
                $config->user->displaySellerInfo = $values['displaySellerInfo'];
                $config->global->logExceptionsToFile = $values['logExceptionsToFile'];
                $writer = new Zend_Config_Writer_Ini();
                $writer->write($this->localConfigPath, $config);
                $this->_helper->getHelper('FlashMessenger')->addMessage('Thank you. Your configuration was successfully saved.');
                $this->_redirect('/admin/config/success');
            }
        }
    }

    /**
     * success action
     */
    public function successAction()
    {
        if ($this->_helper->getHelper('FlashMessenger')->getMessages()) {
            $this->view->messages = $this->_helper->getHelper('FlashMessenger')->getMessages();
        } else {
            $this->_redirect('/');
        }
    }
}
