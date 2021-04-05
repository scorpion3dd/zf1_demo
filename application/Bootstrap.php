<?php

/**
 * Class Bootstrap
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * @return Zend_Config
     */
    protected function _initConfigReg()
    {
        $config = new Zend_Config($this->getOptions(), true);
        Zend_Registry::set('config', $config);
        return $config;
    }

    /**
     * @return Doctrine_Connection
     * @throws Doctrine_Exception
     * @throws Doctrine_Manager_Exception
     * @throws Zend_Exception
     */
    protected function _initDoctrine()
    {
        $libraryBaseDir = SITE_ROOT_DIR . '/library/Doctrine/lib/Doctrine.php';
        require_once $libraryBaseDir;
        $this->getApplication()->getAutoloader()
            ->pushAutoloader(array('Doctrine', 'autoload'), 'Doctrine');

        $manager = Doctrine_manager::getInstance();
        $manager->setAttribute(
            Doctrine::ATTR_MODEL_LOADING,
            Doctrine::MODEL_LOADING_CONSERVATIVE
        );

        $host = Zend_Registry::get('config')->resources->db->zf1->host;
        $username = Zend_Registry::get('config')->resources->db->zf1->username;
        $password = Zend_Registry::get('config')->resources->db->zf1->password;
        $dbname = Zend_Registry::get('config')->resources->db->zf1->dbname;

        $conn = Doctrine_manager::connection("mysql://$username:$password@$host/$dbname",'doctrine');

        return $conn;
    }

    /**
     * @throws Zend_Locale_Exception
     */
    protected function _initLocale()
    {
        $session = new Zend_Session_Namespace('zf1.l10n');
        if ($session->locale) {
            $locale = new Zend_Locale($session->locale);
        }

        if ($locale === null) {
            try {
                $locale = new Zend_Locale('browser');
            } catch (Zend_Locale_Exception $e) {
                $locale = new Zend_Locale('en_GB');
            }
        }

        $registry = Zend_Registry::getInstance();
        $registry->set('Zend_Locale', $locale);
    }

    /**
     * @throws Zend_Translate_Exception
     */
    protected function _initTranslate()
    {
        $translate = new Zend_Translate('array', APPLICATION_PATH . '/../languages/',
            null, array('scan' => Zend_Translate::LOCALE_FILENAME, 'disableNotices' => 1));
        $registry = Zend_Registry::getInstance();
        $registry->set('Zend_Translate', $translate);
    }

    /**
     * _initRoutes
     */
    protected function _initRoutes()
    {
        $front = Zend_Controller_Front::getInstance();
        $router = $front->getRouter();
        $restRoute = new Zend_Rest_Route($front, array(), array('api'));
        $router->addRoute('api', $restRoute);
    }

    /**
     * @throws Zend_Config_Exception
     * @throws Zend_Navigation_Exception
     */
    protected function _initNavigation()
    {
        // read navigation XML and initialize container
        $config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml');
        $container = new Zend_Navigation($config);

        // register navigation container
        $registry = Zend_Registry::getInstance();
        $registry->set('Zend_Navigation', $container);

        // add action helper
        Zend_Controller_Action_HelperBroker::addHelper(
            new Zf1_Controller_Action_Helper_Navigation()
        );
    }

    /**
     * @throws Zend_Application_Bootstrap_Exception
     */
    protected function _initDojo()
    {
        // get view resource
        $this->bootstrap('view');
        $view = $this->getResource('view');

        // add helper path to view
        Zend_Dojo::enableView($view);

        // configure Dojo view helper, disable
        $view->dojo()->setCdnBase(Zend_Dojo::CDN_BASE_GOOGLE)
            ->addStyleSheetModule('dijit.themes.tundra')
            ->disable();
    }

    /**
     * @throws Zend_Application_Bootstrap_Exception
     */
    protected function _initCache()
    {
        $this->bootstrap('cachemanager');
        $manager = $this->getResource('cachemanager');
        $memoryCache = $manager->getCache('memory');
        Zend_Locale::setCache($memoryCache);
        Zend_Translate::setCache($memoryCache);
    }
}

