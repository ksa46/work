<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Michael
 * Date: 07.10.11
 * Time: 04:43
 * To change this template use File | Settings | File Templates.
 */

namespace App\Form;

class Captcha extends \Zend_Form
{
    public function init()
    {
        // $this->setDefaultTranslator(\Zend_Registry::get('Zend_Translate')); ???
       // $this->setMethod('POST');
       // $this->setAction($this->getView()->baseUrl('/index/add-custom'));
       // $this->setAttrib('id', 'addQuote');

        $captchaimg = New \Zend_Captcha_Image('captchaimg');
        $captchaimg->setFont('./captcha/fonts/tahoma.ttf');
        $captchaimg->setImgDir('./captcha/images');
        $captchaimg->setImgUrl('/captcha/images');
        $captchaimg->setWordlen(5);
        $captchaimg->setFontSize(38);
        $captchaimg->setLineNoiseLevel(3);
        $captchaimg->setWidth(220);
        $captchaimg->setHeight(80);
        $captchaimg->setOptions(array('class' => 'input-text' , 'name' => 'user_captcha' ));
        
        //create user input for captcha and include the captchaimg in form
        $adcaptcha = New \Zend_Form_Element_Captcha('adcaptcha', array(
            'captcha' => $captchaimg));
        //$adcaptcha->setLabel('Please enter the 5 letters displayed below:');
        $adcaptcha->setRequired(true);
        
        $this->addElements(array( $adcaptcha));
    }

    public function isValid($data)
    {
        if (!is_array($data)) {
            require_once 'Zend/Form/Exception.php';
            throw new \Zend_Form_Exception(__METHOD__ . ' expects an array');
        }
        return parent::isValid($data);
    }
}