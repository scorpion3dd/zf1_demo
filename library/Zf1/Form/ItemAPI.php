<?php

/**
 * Class Zf1_Form_ItemAPI
 */
class Zf1_Form_ItemAPI extends Zend_Form
{
    /**
     * @throws Zend_Form_Exception
     */
    public function init()
    {
        // initialize form
        $this->setAction('/catalog/item/create')
            ->setMethod('post');

        // create text input for name
        $name = new Zend_Form_Element_Text('SellerName');
        $name->setLabel('Name:')
            ->setOptions(array('size' => '35'))
            ->setRequired(true)
            ->addValidator('Regex', false, array(
                'pattern' => '/^[a-zA-Z]+[A-Za-z\'\-\. ]{1,50}$/'
            ))
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim');

        // create text input for email address
        $email = new Zend_Form_Element_Text('SellerEmail');
        $email->setLabel('Email address:');
        $email->setOptions(array('size' => '50'))
            ->setRequired(true)
            ->addValidator('EmailAddress', false)
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim')
            ->addFilter('StringToLower');

        // create text input for tel number
        $tel = new Zend_Form_Element_Text('SellerTel');
        $tel->setLabel('Telephone number:');
        $tel->setOptions(array('size' => '50'))
            ->addValidator('StringLength', false, array('min' => 8))
            ->addValidator('Regex', false, array(
                'pattern'   => '/^\+[1-9][0-9]{6,30}$/',
                'messages'  => array(
                    Zend_Validate_Regex::INVALID    =>
                        '\'%value%\' does not match international number format +XXYYZZZZ',
                    Zend_Validate_Regex::NOT_MATCH  =>
                        '\'%value%\' does not match international number format +XXYYZZZZ'
                )
            ))
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim');

        // create text input for address
        $address = new Zend_Form_Element_Textarea('SellerAddress');
        $address->setLabel('Postal address:')
            ->setOptions(array('rows' => '6','cols' => '36'))
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim');

        // create text input for item title
        $title = new Zend_Form_Element_Text('Title');
        $title->setLabel('Title:')
            ->setOptions(array('size' => '60'))
            ->setRequired(true)
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim');

        // create text input for item year
        $year = new Zend_Form_Element_Text('Year');
        $year->setLabel('Year:')
            ->setOptions(array('size' => '8', 'length' => '4'))
            ->setRequired(true)
            ->addValidator('Between', false, array('min' => 1700, 'max' => 2030))
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim');

        // create select input for item country
        $country = new Zend_Form_Element_Select('CountryID');
        $country->setLabel('Country:')
            ->setRequired(true)
            ->addValidator('Int')
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim')
            ->addFilter('StringToUpper');
        foreach ($this->getCountries() as $c) {
            $country->addMultiOption($c['CountryID'], $c['CountryName']);
        }

        // create text input for item denomination
        $denomination = new Zend_Form_Element_Text('Denomination');
        $denomination->setLabel('Denomination:')
            ->setOptions(array('size' => '8'))
            ->setRequired(true)
            ->addValidator('Float')
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim');

        // create radio input for item type
        $type = new Zend_Form_Element_Radio('TypeID');
        $type->setLabel('Type:')
            ->setRequired(true)
            ->addValidator('Int')
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim');
        foreach ($this->getTypes() as $t) {
            $type->addMultiOption($t['TypeID'], $t['TypeName']);
        }
        $type->setValue(1);

        // create select input for item grade
        $grade = new Zend_Form_Element_Select('GradeID');
        $grade->setLabel('Grade:')
            ->setRequired(true)
            ->addValidator('Int')
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim');
        foreach ($this->getGrades() as $g) {
            $grade->addMultiOption($g['GradeID'], $g['GradeName']);
        };

        // create text input for sale price (min)
        $priceMin = new Zend_Form_Element_Text('SalePriceMin');
        $priceMin->setLabel('Sale price (min):')
            ->setOptions(array('size' => '8'))
            ->setRequired(true)
            ->addValidator('Float')
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim');

        // create text input for sale price (max)
        $priceMax = new Zend_Form_Element_Text('SalePriceMax');
        $priceMax->setLabel('Sale price (max):')
            ->setOptions(array('size' => '8'))
            ->setRequired(true)
            ->addValidator('Float')
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim');

        // create text input for item description
        $notes = new Zend_Form_Element_Textarea('Description');
        $notes->setLabel('Description:')
            ->setOptions(array('rows' => '15','cols' => '60'))
            ->setRequired(true)
            ->addFilter('HtmlEntities')
            ->addFilter('StripTags')
            ->addFilter('StringTrim');

        // attach elements to form
        $this->addElement($name)
            ->addElement($email)
            ->addElement($tel)
            ->addElement($address)
            ->addElement($title)
            ->addElement($year)
            ->addElement($country)
            ->addElement($denomination)
            ->addElement($type)
            ->addElement($grade)
            ->addElement($priceMin)
            ->addElement($priceMax)
            ->addElement($notes);
    }

    public function getCountries() {
        $q = Doctrine_Query::create()
            ->from('Zf1_Model_Country c');
        return $q->fetchArray();
    }

    public function getGrades() {
        $q = Doctrine_Query::create()
            ->from('Zf1_Model_Grade g');
        return $q->fetchArray();
    }

    public function getTypes() {
        $q = Doctrine_Query::create()
            ->from('Zf1_Model_Type t');
        return $q->fetchArray();
    }
}
