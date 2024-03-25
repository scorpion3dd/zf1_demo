<?php

/**
 * Class Zf1_Form_SearchSimple
 */
class Zf1_Form_SearchSimple extends Zend_Form
{
    public $messages = array(
        Zend_Validate_Int::INVALID => '\'%value%\' is not an integer',
        Zend_Validate_Int::NOT_INT => '\'%value%\' is not an integer'
    );

    /**
     * @throws Zend_Form_Exception
     */
    public function init()
    {
        $action = '/catalog/item/search-simple';
        if (Zf1_Helper_Server::isApache() && ! Zf1_Helper_Server::isNginx()) {
            $action = "/index.php$action";
        }
        // initialize form
        $this->setAction($action)
            ->setMethod('post');

        $this->setDecorators(array(
                array('FormErrors', array('markupListItemStart' => '', 'markupListItemEnd' => '')),
                array('FormElements'),
                array('Form'))
        );

        // create text input for Year
        $description = new Zend_Form_Element_Text('Description');
        $description->setLabel('Description:')
            ->setOptions(array('size' => '50'))
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim');

        // create text input for Year
        $year = new Zend_Form_Element_Text('y');
        $year->setLabel('Year:')
            ->setOptions(array('size' => '6'))
            ->addValidator('Int', false, array('messages' => $this->messages))
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim');


        // create text input for Price
        $price = new Zend_Form_Element_Text('p');
        $price->setLabel('Price:')
            ->setOptions(array('size' => '8'))
            ->addValidator('Int', false, array('messages' => $this->messages))
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim');

        // create text input for Grade
        $grade = new Zend_Form_Element_Select('g');
        $grade->setLabel('Grade:')
            ->addValidator('Int', false, array('messages' => $this->messages))
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim');
        foreach ($this->getGrades() as $g){
            $grade->addMultiOption($g['GradeID'], $g['GradeName']);
        }

        // create submit button
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Search')
            ->setOptions(array('class' => 'submit'));
        $submit->setDecorators(array(
            array('ViewHelper'),
        ));

        // attach elements to form
        $this->addElement($description)->addElement($year)->addElement($price)->addElement($grade)->addElement($submit);

        $this->setElementDecorators(array(
           array('ViewHelper'),
            array('Label', array('tag' => '<span>'))
        ));
    }

    /**
     * @return array
     */
    public function getGrades()
    {
        $q = Doctrine_Query::create()->from('Zf1_Model_Grade g');
        return $q->fetchArray();
    }
}
