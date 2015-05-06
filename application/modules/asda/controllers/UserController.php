<?php

class Asda_UserController extends Zend_Controller_Action
{

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $_em = null;

    /**
     * @var \sfServiceContainer
     */
    protected $_sc = null;

    /**
     * @var \App\Service\RandomQuote
     * @InjectService RandomQuote
     */
    protected $_randomQuote = null;

    public function init()
    {
        $this->_service = Zend_Registry::get('sc');
        
        $this->_user = $this->_service->getService('user');
    }
        
    public function registerAction()
    {

        $request = $this->getRequest();
        try {
            
            $this->view->error                    = '';
            $this->view->errorMessages            = array();
            $this->view->postVariables            = array();
            if ($request->isPost()) {

                $result = $this->_user->createUser ($request->getParams());
                if (! isset ($result->error)) {
                    $email = $request->getParam('email');
                    $registerSession = new Zend_Session_Namespace('register');
                    $registerSession->message = "Your account has been created successfully. You can now log in.
                                                 <br />Successfully sent the mail to asasfff@sss.com
                                                                        ";
                    $this->_redirect('/login');
                }
                $this->view->error                    = $result->error;
                $this->view->errorMessages            = $result->userRegisterMessage;
                $this->view->postVariables            = $request->getParams();
            }

        }
        catch (Exception $e) {
            var_dump($e);exit;
        }
             
             
        
    } public function generateCaptchaImageAction() {
             // Set the enviroment variable for GD
        putenv('GDFONTPATH=' . realpath('.'));
        
        if(!isset($_SESSION)) {
            session_start();
        }
        
        header("Expires: Tue, 01 Jan 2013 00:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        
        for ($i = 0; $i < 5; $i++)
        {
            $randomString .= $chars[rand(0, strlen($chars)-1)];
        }
        
        $_SESSION['captcha'] = $randomString;
       // echo DIRECTORY_PATH.'/images/captcha_bg.png';exit;
        $im = @imagecreatefrompng(DIRECTORY_PATH.'/img/captcha_bg.png');
        $fontLocation = DIRECTORY_PATH . '/fonts/gothambook.ttf';
        imagettftext($im, 30, 0, 10, 38, imagecolorallocate ($im, 0, 0, 0), $fontLocation, $randomString);
        
        header ('Content-type: image/png');
        imagepng($im, NULL, 0);
        imagedestroy($im);
        exit;
    }

}
