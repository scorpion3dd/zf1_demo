<?php
/**
 * Class Zf1_Form_Contact2
 */
class Zf1_Form_Contact2 extends Zend_Form
{
    /**
     * @throws Zend_Form_Exception
     */
    public function init()
    {
        $action = '/contact';
        if (Zf1_Helper_Server::isApache() && ! Zf1_Helper_Server::isNginx()) {
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
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim');

        // create text input for email address
        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('contact-email-address');
        $email->setOptions(array('size' => '50'))
            ->setRequired(true)
            ->addValidator('NotEmpty', true)
            ->addValidator('EmailAddress', true)
            ->addFilter('HtmlEntities')
            ->addFilter('StringToLower')
            ->addFilter('StringTrim');

        // create autocomplete input for country
        $country = new Zend_Dojo_Form_Element_ComboBox('country');
        $country->setLabel('contact-country');
        $country->setOptions(array(
            'autocomplete' => false,
            'storeId'   => 'countryStore',
            'storeType' => 'dojo.data.ItemFileReadStore',
            'storeParams' => array('url' => "/default/contact/autocomplete"),
            'dijitParams' => array('searchAttr' => 'name')))
            ->setRequired(true)
            ->addValidator('NotEmpty', true)
            ->addFilter('HtmlEntities')
            ->addFilter('StringToLower')
            ->addFilter('StringTrim');

        // create text input for message body
        $message = new Zend_Form_Element_Textarea('message');
        $message->setLabel('contact-message')
            ->setOptions(array('rows' => '8','cols' => '40'))
            ->setRequired(true)
            ->addValidator('NotEmpty', true)
            ->addFilter('HtmlEntities')
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
        $submit->setLabel('contact-send-message')
            ->setOptions(array('class' => 'submit'));

        // attach elements to form
        $this->addElement($name)
            ->addElement($email)
            ->addElement($country)
            ->addElement($message)
            ->addElement($captcha)
            ->addElement($submit);
    }
}
