<?php

/**
 * Class Zf1_Form_SearchFull
 */
class Zf1_Form_SearchFull extends Zend_Form
{
    /**
     * @throws Zend_Form_Exception
     */
    public function init()
    {
        $action = '/catalog/item/search-full';
        if (Zf1_Helper_Server::isApache() && ! Zf1_Helper_Server::isNginx()) {
            $action = "/index.php$action";
        }
        // initialize form
        $this->setAction($action)
            ->setMethod('get');

        // create text input for keywords
        $query = new Zend_Form_Element_Text('q');
        $query->setLabel('Keywords:')
            ->setOptions(array('size' => '20'))
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim');
        $query->setDecorators(array(
            array('ViewHelper'),
            array('Errors'),
            array('Label', array('tag' => '<span>')),
        ));

        // create submit button
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Search')
            ->setOptions(array('class' => 'submit'));
        $submit->setDecorators(array(
            array('ViewHelper'),
        ));

        // attach elements to form
        $this->addElement($query)
            ->addElement($submit);
    }
}
