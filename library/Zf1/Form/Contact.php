<?php
/**
 * Class Zf1_Form_Contact
 */
class Zf1_Form_Contact extends Zend_Form
{
    /**
     * @throws Zend_Form_Exception
     */
    public function init()
    {
        $action = '/contact/index';
        if ($this->isLinux()) {
            $action = "/index.php$action";
        }
        // initialize form
        $this->setAction($action)
            ->setMethod('post');

        // create text input for name
        $name = new Zend_Form_Element_Text('name');
        $name->setLabel('contact-name')
            ->setOptions(array('size' => '35'))
            ->setRequired(true)
            ->addValidator('NotEmpty', true)
            ->addValidator('Alpha', true)
            ->addFilter('HTMLEntities')
            ->addFilter('StringTrim');

        // create text input for email address
        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('contact-email-address');
        $email->setOptions(array('size' => '50'))
            ->setRequired(true)
            ->addValidator('NotEmpty', true)
            ->addValidator('EmailAddress', true)
            ->addFilter('HTMLEntities')
            ->addFilter('StringToLower')
            ->addFilter('StringTrim');

        // create text input for message body
        $message = new Zend_Form_Element_TextArea('message');
        $message->setLabel('contact-message')
            ->setOptions(array('rows' => '8','cols' => '40'))
            ->setRequired(true)
            ->addValidator('NotEmpty', true)
            ->addFilter('HTMLEntities')
            ->addFilter('StringTrim');

        // create captcha
        $captcha = new Zend_Form_Element_Captcha('captcha', array(
            'captcha' => array(
                'captcha' => 'Image',
                'wordLen' => 6,
                'timeout' => 300,
                'width'   => 300,
                'height'  => 100,
                'imgUrl'  => '/captcha',
                'imgDir'  => APPLICATION_PATH . '/../public/captcha',
                'font'    => APPLICATION_PATH . '/../public/fonts/LiberationSansRegular.ttf',
            )
        ));
        $captcha->setLabel('contact-verification');

        // create submit button
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Send Message')
            ->setOptions(array('class' => 'submit'));

        // attach elements to form
        $this->addElement($name)
            ->addElement($email)
            ->addElement($message)
            ->addElement($captcha);

        $this->addDisplayGroup(
            array('name', 'email', 'message', 'captcha'),
            'contact'
        );
        $this->getDisplayGroup('contact')->setLegend('Contact Information');

        $this->addElement($submit);
    }
}
