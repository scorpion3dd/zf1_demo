<?php

/**
 * Class Zf1_Form_GeneratesFakeData
 */
class Zf1_Form_GeneratesFakeData extends Zend_Form
{
    /**
     * @throws Zend_Form_Exception
     */
    public function init()
    {
        $action = '/admin/catalog/generates';
        if ($this->isLinux()) {
            $action = "/index.php$action";
        }
        // initialize form
        $this->setAction($action)
            ->setMethod('post');

        // create text input for keywords
        $countFaker = new Zend_Form_Element_Text('countFaker');
        $countFaker->setLabel('Count faker:')
            ->setOptions(array('size' => '4', 'type' => 'number'))
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim')
            ->setRequired(true)
            ->addValidator('Int')
            ->addValidator('Between', false, [1, 1000]);
        $countFaker->setDecorators(array(
            array('ViewHelper'),
            array('Errors'),
            array('Label', array('tag' => '<span>')),
        ));

        // create submit button
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Set')
            ->setOptions(array('class' => 'submit'));
        $submit->setDecorators(array(
            array('ViewHelper'),
        ));

        // attach elements to form
        $this->addElement($countFaker)
            ->addElement($submit)
            ->setDefaults(['countFaker' => 30]);
    }
}
