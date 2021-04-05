<?php

/**
 * Class LocaleController
 */
class LocaleController extends Zend_Controller_Action
{
    /**
     * Action to manually override locale
     * @throws Zend_Validate_Exception
     */
    public function indexAction()
    {
        // if supported locale, add to session
        if (Zend_Validate::is($this->getRequest()->getParam('locale'), 'InArray',
            array('haystack' => array('en_US', 'en_GB', 'de_DE', 'fr_FR', 'ru_RU')))) {
            $session = new Zend_Session_Namespace('zf1.l10n');
            $session->locale = $this->getRequest()->getParam('locale');
        }

        // redirect to requesting URL
        $url = $this->getRequest()->getServer('HTTP_REFERER');
        $this->_redirect($url);
    }
}
