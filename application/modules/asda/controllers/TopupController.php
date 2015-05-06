<?php

class Asda_TopupController extends Zend_Controller_Action
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
    protected $userId = null;

    public function init()
    {
        $this->_service = Zend_Registry::get('sc');
        
        $this->_card = $this->_service->getService('card');
        $this->_user = $this->_service->getService('user');
        
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
            
            $this->view->error                    = false;
            $this->view->errorMessages            = array();
            $this->view->postVariables            = array();
            $this->view->topupError               = null;
            
            $cardId = $request->getParam('cid' , 0);
            
            $validate = true;

            if (empty ($cardId)){
                $validate = false;
                $this->view->error                    = true;
                $this->view->errorMessages            = 'Invalid card.Go to ypur accout and try again';
            } else {
                $isValid = $this->_card->validateCard( $cardId , Zend_Auth::getInstance()->getIdentity()->id );
                
                if ($isValid === false) {
                    $validate = false;
                    $this->view->error                    = true;
                    $this->view->errorMessages            = 'We cannot find the card to topup';
                
                }
            }
            
            if ($this->_request->isPost() && $validate) {
                $amount = $request->getParam('amount' , '');
                if (empty ($amount)|| !(is_numeric($amount))) {
                    $this->view->topupError            = "Please enter a valid amount in GBP";
                    
                } else {
                    
                    if(!isset($_SESSION))
                    {
                        session_start();
                    }
                    
                    $topupSession = new Zend_Session_Namespace('topup');
                    $topupSession->amount = $amount;
                    $topupSession->card   = $cardId;

                    
                    $this->_redirect('/topup/payment');
                }
            }
            
            $this->view->cardId = $cardId;

        }
        catch (Exception $e) {
            var_dump($e);exit;
        }
 
    }
    
    public function paymentAction()
    {
    
        $request = $this->getRequest();
        try {
    
            $this->view->error                    = false;
            $this->view->errorMessages            = array();
            $this->view->postVariables            = array();
            $this->view->topupError               = null;
            
            if(!isset($_SESSION))
            {
                session_start();
            }
            $topupSession = new Zend_Session_Namespace('topup');
            $this->view->topupAmount = $topupSession->amount;
            $this->view->topupCardId = $topupSession->card;
            
            $this->view->cardInfo = $this->_card->validateCard( $this->view->topupCardId , Zend_Auth::getInstance()->getIdentity()->id );
        }
        catch (Exception $e) {
            var_dump($e);exit;
        }
    
    }

    public function scheduleAction()
    {
        $request = $this->getRequest();
        try {
            
            $this->view->error                    = false;
            $this->view->errorMessages            = array();
            $this->view->topupError               = array();
            $this->view->frequency                = null;
            
            $cardId = $request->getParam('cid' , 0);
            
            $validate = true;

            if (empty ($cardId)){
                $validate = false;
                $this->view->error                    = true;
                $this->view->errorMessages            = 'Invalid card.Go to ypur accout and try again';
            } else {
                $isValid = $this->_card->validateCard( $cardId , Zend_Auth::getInstance()->getIdentity()->id );
                
                if ($isValid === false) {
                    $validate = false;
                    $this->view->error                    = true;
                    $this->view->errorMessages            = 'We cannot find the card to topup';
                
                }
            }
            
            if ($this->_request->isPost() && $validate) {
                
                $amount    = $request->getParam('amount' , '');
                $this->view->frequency = $request->getParam('frequency' , '');
                $validPost = true; 
                if (empty ($amount)|| !(is_numeric($amount))) {
                    
                    $this->view->topupError['amount']  = "Please enter a valid amount in GBP";
                    $validPost = false;
                    
                }
                if ($this->view->frequency == 'Select') {
                    $this->errors['frequency'] = "Please select the duration of your topup";
                    $validaetd = false;
                }

                if ($validPost) {
                    
                    if(!isset($_SESSION))
                    {
                        session_start();
                    }
                    $topupSession = new Zend_Session_Namespace('topup');
                    $topupSession->amount = $amount;
                    $topupSession->card = $cardId;
                    $topupSession->frequency = $this->view->frequency;

                    $this->_redirect('/topup/schedule-payment');
                }
            }
            
            $this->view->cardId = $cardId;
    
        }
        catch (Exception $e) {
            var_dump($e);exit;
        }
         
    }
    public function schedulePaymentAction()
    {
    
        $request = $this->getRequest();
        try {
    
            $this->view->error                    = false;
            $this->view->errorMessages            = array();
            $this->view->postVariables            = array();
            $this->view->topupError               = null;
            $this->view->frequency                = null;
    
            if(!isset($_SESSION))
            {
                session_start();
            }

            $topupSession = new Zend_Session_Namespace('topup');
            $this->view->topupAmount = $topupSession->amount;
            $this->view->topupCardId = $topupSession->card;
            $this->view->frequency   = $topupSession->frequency;
    
            $this->view->cardInfo = $this->_card->validateCard( $this->view->topupCardId , Zend_Auth::getInstance()->getIdentity()->id );
    
    
            $validate = true;
        }
        catch (Exception $e) {
            var_dump($e);exit;
        }
    
    }
}
