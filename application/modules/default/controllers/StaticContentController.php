<?php

/**
 * Class StaticContentController
 */
class StaticContentController extends Zend_Controller_Action
{
    /**
     * init
     */
    public function init()
    {
    }

    /**
     * Display static views
     * @throws Zend_Controller_Action_Exception
     * @throws Zend_View_Exception
     */
    public function displayAction()
    {
      $page = $this->getRequest()->getParam('page');
		  if (file_exists($this->view->getScriptPath(null) . "/" . $this->getRequest()->getControllerName() . "/$page." . $this->viewSuffix)) {
        $this->render($page);
      } else {
        throw new Zend_Controller_Action_Exception('Page not found', 404);
      }
    }
}

