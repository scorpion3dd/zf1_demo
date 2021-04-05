<?php
/**
 * Class Zf1_Controller_Action_Helper_Navigation
 */
class Zf1_Controller_Action_Helper_Navigation extends Zend_Controller_Action_Helper_Abstract
{
    protected $_container;

    /**
     * Zf1_Controller_Action_Helper_Navigation constructor.
     * @param Zend_Navigation|null $container
     */
    public function __construct(Zend_Navigation $container = null)
    {
        if (null !== $container) {
            $this->_container = $container;
        }
    }

    /**
     * Check current request and set active page
     */
    public function preDispatch()
    {
        $uri = $this->getContainer()
            ->findBy('uri', $this->getRequest()->getRequestUri());
        if(isset($uri))
        {
            $uri->active = true;
        }
    }

    /**
     * Retrieve navigation container
     * @return mixed
     * @throws Zend_Exception
     */
    public function getContainer()
    {
        if (null === $this->_container) {
            $this->_container = Zend_Registry::get('Zend_Navigation');
        }
        if (null === $this->_container) {
            throw new RuntimeException ('Navigation container unavailable');
        }
        return $this->_container;
    }
}
