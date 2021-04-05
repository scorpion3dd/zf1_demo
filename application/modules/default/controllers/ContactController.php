<?php

/**
 * Class ContactController
 */
class ContactController extends Zend_Controller_Action
{
    /**
     * init
     */
    public function init()
    {
        $this->view->doctype('XHTML1_STRICT');
    }

    /**
     * @throws Zend_Config_Exception
     * @throws Zend_Form_Exception
     * @throws Zend_Mail_Exception
     */
    public function indexAction()
    {
        //$form = new Zf1_Form_Contact();
        $form = new Zf1_Form_Contact2();
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $values = $form->getValues();
                $configs = $this->getInvokeArg('bootstrap')->getOption('configs');
                $localConfig = new Zend_Config_Ini($configs['localConfigPath']);
                $to = (!empty($localConfig->user->salesEmailAddress)) ?
                    $localConfig->user->salesEmailAddress : $localConfig->global->defaultEmailAddress;
                $mail = new Zend_Mail();
                $mail->setBodyText($values['message']);
                $mail->setFrom($values['email'], $values['name']);
                $mail->addTo($to);
                $mail->setSubject('Contact form submission');
                $mail->send();
                $this->_helper->getHelper('FlashMessenger')
                    ->addMessage('Thank you. Your message was successfully sent.');
                $this->_redirect('/contact/success');
            }
        }
    }

    /**
     * autocomplete action
     */
    public function autocompleteAction()
    {
        // disable layout and view rendering
        $this->_helper->layout->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender(true);

        // get country list from Zend_Locale
        $territories = Zend_Locale::getTranslationList('territory', null, 2);
        $items = array();
        foreach ($territories as $t) {
            $items[] = array('name' => $t);
        }

        // generate and return JSON string compliant with dojo.data structure
        $data = new Zend_Dojo_Data('name', $items);
        header('Content-Type: application/json');
        echo $data->toJson();
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
