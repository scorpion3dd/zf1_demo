<?php
/**
 * Class Zf1_Form_Configure
 */
class Zf1_Form_Configure extends Zend_Form
{
    /**
     * @throws Zend_Form_Exception
     */
    public function init()
    {
        $action = '/admin/config';
        if ($this->isLinux()) {
            $action = "/index.php$action";
        }
        // initialize form
        $this->setAction($action)
            ->setMethod('post');

        // create text input for default email
        $default = new Zend_Form_Element_Text('defaultEmailAddress');
        $default->setLabel('Fallback email address for all operations:')
            ->setOptions(array('size' => '40'))
            ->setRequired(true)
            ->addValidator('EmailAddress')
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim');

        // create text input for sales email
        $sales = new Zend_Form_Element_Text('salesEmailAddress');
        $sales->setLabel('Default email address for sales enquiries:')
            ->setOptions(array('size' => '40'))
            ->addValidator('EmailAddress')
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim');

        // create text input for number of items per page in admin summary
        $items = new Zend_Form_Element_Text('itemsPerPage');
        $items->setLabel('Number of items per page in administrative views:')
            ->setOptions(array('size' => '4'))
            ->setRequired(true)
            ->addValidator('Int')
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim');

        // create radio button for display of seller name and address
        $seller = new Zend_Form_Element_Radio('displaySellerInfo');
        $seller->setLabel('Seller name and address visible in public catalog:')
            ->setRequired(true)
            ->setMultiOptions(array(
                '1'    => 'Yes',
                '0'    => 'No'
            ));


        // create radio button for exception logging
        $log = new Zend_Form_Element_Radio('logExceptionsToFile');
        $log->setLabel('Exceptions logged to file:')
            ->setRequired(true)
            ->setMultiOptions(array(
                '1'    => 'Yes',
                '0'    => 'No'
            ));

        // create submit button
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Save configuration')
            ->setOptions(array('class' => 'submit'));

        // attach elements to form
        $this->addElement($sales)
            ->addElement($default)
            ->addElement($items)
            ->addElement($seller)
            ->addElement($log)
            ->addElement($submit);
    }
}
