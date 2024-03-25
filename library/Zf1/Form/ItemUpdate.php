<?php

/**
 * Class Zf1_Form_ItemUpdate
 */
class Zf1_Form_ItemUpdate extends Zf1_Form_ItemCreate
{
    /**
     * @throws Zend_Form_Exception
     */
    public function init()
    {
        // get parent form
        parent::init();

        $action = '/admin/catalog/item/update';
        if (Zf1_Helper_Server::isApache() && ! Zf1_Helper_Server::isNginx()) {
            $action = "/index.php$action";
        }
        // set form action (set to false for current URL)
        $this->setAction($action);

        // remove unwanted elements
        $this->removeElement('Captcha');
        $this->removeDisplayGroup('verification');

        // create hidden input for item ID
        $id = new Zend_Form_Element_Hidden('RecordID');
        $id->addValidator('Int')
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim');

        // create select input for item display status
        $display = new Zend_Form_Element_Select('DisplayStatus',
            array('onChange' => "javascript:handleInputDisplayOnSelect('DisplayStatus', 'divDisplayUntil', new Array('1'));"));
        $display->setLabel('Display status:')
            ->setRequired(true)
            ->addValidator('Int')
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim');
        $display->addMultiOptions(array(
            0 => 'Hidden',
            1 => 'Visible'
        ));

        // create hidden input for item display date
        $displayUntil = new Zend_Form_Element_Hidden('DisplayUntil');
        $displayUntil->addValidator('Date')
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim');

        // create select inputs for item display date
        $displayUntilDay = new Zend_Form_Element_Select('DisplayUntil_day');
        $displayUntilDay->setLabel('Display until:')
            ->addValidator('Int')
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim')
            ->addFilter('StringToUpper')
            ->setDecorators(array(
                array('ViewHelper'),
                array('Label', array('tag' => 'dt')),
                array('HtmlTag',
                    array(
                        'tag' => 'div',
                        'openOnly' => true,
                        'id' => 'divDisplayUntil',
                        'placement' => 'prepend'
                    )
                ),
            ));
        for ($x = 1; $x <= 31; $x++) {
            $displayUntilDay->addMultiOption($x, sprintf('%02d', $x));
        }

        $displayUntilMonth = new Zend_Form_Element_Select('DisplayUntil_month');
        $displayUntilMonth->addValidator('Int')
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim')
            ->setDecorators(array(
                array('ViewHelper')
            ));
        for ($x = 1; $x <= 12; $x++) {
            $displayUntilMonth->addMultiOption($x, date('M', mktime(1, 1, 1, $x, 1, 1)));
        }

        $displayUntilYear = new Zend_Form_Element_Select('DisplayUntil_year');
        $displayUntilYear->addValidator('Int')
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim')
            ->setDecorators(array(
                array('ViewHelper'),
                array('HtmlTag',
                    array(
                        'tag' => 'div',
                        'closeOnly' => true
                    )
                ),
            ));
        for ($x = 2000; $x <= 2030; $x++) {
            $displayUntilYear->addMultiOption($x, $x);
        }

        // attach element to form
        $this->addElement($id)
            ->addElement($display)
            ->addElement($displayUntil)
            ->addElement($displayUntilDay)
            ->addElement($displayUntilMonth)
            ->addElement($displayUntilYear);

        // create display group for status
        $this->addDisplayGroup(
            array('DisplayStatus', 'DisplayUntil_day',
                'DisplayUntil_month', 'DisplayUntil_year',
                'DisplayUntil'), 'display');
        $this->getDisplayGroup('display')
            ->setOrder(25)
            ->setLegend('Display Information');
    }
}
