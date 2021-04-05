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
        // initialize form
        $this->setAction('/admin/catalog/indexes')
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
