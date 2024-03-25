<?php

/**
 * Class Zf1_Form_CreateIndexes
 */
class Zf1_Form_CreateIndexes extends Zend_Form
{
    /**
     * @throws Zend_Form_Exception
     */
    public function init()
    {
        $action = '/admin/catalog/indexes';
        if (Zf1_Helper_Server::isApache() && ! Zf1_Helper_Server::isNginx()) {
            $action = "/index.php$action";
        }
        // initialize form
        $this->setAction($action)
            ->setMethod('post');

        // create submit button
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Build')
            ->setOptions(array('class' => 'submit'));
        $submit->setDecorators(array(
            array('ViewHelper'),
        ));

        // attach elements to form
        $this->addElement($submit);
    }
}
