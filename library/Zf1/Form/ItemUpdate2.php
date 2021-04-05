<?php

/**
 * Class Zf1_Form_ItemUpdate2
 */
class Zf1_Form_ItemUpdate2 extends Zf1_Form_ItemCreate
{
    /**
     * @throws Zend_Form_Exception
     */
    public function init()
    {
        // get parent form
        parent::init();

        // set form action (set to false for current URL)
        $this->setAction('/admin/catalog/item/update');

        // remove unwanted elements
        $this->removeElement('Captcha');
        $this->removeDisplayGroup('verification');
        $this->removeElement('images');
        $this->removeDisplayGroup('files');

        // create hidden input for item ID
        $id = new Zend_Form_Element_Hidden('RecordID');
        $id->addValidator('Int')
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim');

        // create select input for item display status
        $display = new Zend_Form_Element_Select('DisplayStatus',
            array('onChange' => "javascript:handleInputDisplayOnSelect('DisplayStatus', 
                                                'divDisplayUntil', new Array('1')); cal.hide()"));
        $display->setLabel('Display status:')
            ->setRequired(true)
            ->addValidator('Int')
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim');
        $display->addMultiOptions(array(
            0 => 'Hidden',
            1 => 'Visible'
        ));

        // create input for item display date
        $displayUntil = new Zend_Form_Element_Text('DisplayUntil');
        $displayUntil->setLabel('Display until (yyyy-mm-dd):')
            ->addValidator('Date', false, array('format' => 'yyyy-MM-dd'))
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim')
            ->addDecorators(array(
                array('HTMLTag', array('tag' => 'div', 'id' => 'divDisplayUntil')),
            ));

        // create container for YUI calendar widget
        $calendar = new Zend_Form_Element_Text('Calendar');
        $calendar->setDecorators(array(
            array('Label', array('tag' => 'dt')),
            array('HTMLTag', array('tag' => 'div', 'id' => 'divCalendar',
                'class' => 'yui-skin-sam yui-calcontainer',
                'style' => 'display:none;')),
        ));

        // attach element to form
        $this->addElement($id)
            ->addElement($display)
            ->addElement($calendar)
            ->addElement($displayUntil);

        // create display group for status
        $this->addDisplayGroup(
            array('DisplayStatus', 'DisplayUntil', 'Calendar'),
            'display');
        $this->getDisplayGroup('display')
            ->setOrder(25)
            ->setLegend('Display Information');
    }
}
