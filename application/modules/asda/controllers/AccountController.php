<?php

class Asda_AccountController extends Zend_Controller_Action
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
    protected $_userId = null;

    public function init()
    {
        $this->_service = Zend_Registry::get('sc');
        
        $this->_card       = $this->_service->getService('card');
        $this->_user       = $this->_service->getService('user');
        $this->_address    = $this->_service->getService('address');
        
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $this->userId      = Zend_Auth::getInstance()->getIdentity()->id;
        } 
    }
    
    public function preDispatch()
    {
        parent::preDispatch();
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            //if ($this->getRequest()->isXmlHttpRequest()) {
              //  die('You have been logout. Please click <a href="/">here</a> to login.');
            //} else {
                $this->_redirect('/login');
            //}
        }
        
    }
        
    public function indexAction()
    {

        $request = $this->getRequest();
        try {
            
            $this->view->error                    = '';
            $this->view->errorMessages            = array();
            $this->view->postVariables            = array();
            
            $this->view->userDetails      = $this->_user->getUserDetails($this->userId);
            $this->view->userCardDetails  = $this->_card->getUserCardDetails($this->userId);
            $this->view->billingAddress   = $this->_address->getUserAddress($this->userId , 2);
            $this->view->shippingAddress  = $this->_address->getUserAddress($this->userId , 3);
            
            //$this->_user->testFlorish();
            
            $accountSession = new Zend_Session_Namespace('account');
            if (! empty ($accountSession->message)) {
                $this->view->message = $accountSession->message;
                $accountSession->message = null;
            } 

        }
        catch (Exception $e) {
            var_dump($e);exit;
        }
             
             
        
    } 
}
