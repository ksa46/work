<?php

class Asda_CardController extends Zend_Controller_Action
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
        
    public function registerAction()
    {

        $request = $this->getRequest();
        try {
            
            $this->view->error                    = '';
            $this->view->errorMessages            = array();
            $this->view->postVariables            = array();
            
            if ($request->isPost()) {

                $result = $this->_card->createCard ($request->getParams());
                if (! isset ($result->error)) {
                    $accountSession = new Zend_Session_Namespace('account');
                    $accountSession->message = 'Your card has been added';
                    $this->_redirect('/account');
                }
                $this->view->error                    = $result->error;
                $this->view->errorMessages            = $result->cardRegisterMessage;
                $this->view->postVariables            = $request->getParams();
            }

        }
        catch (Exception $e) {
            var_dump($e);exit;
        }
 
    }

    public function cancelAction()
    {
        $request = $this->getRequest();
        try {
            $this->view->error                    = false;
            $this->view->errorMessages            = array();
            $this->view->postVariables            = array();
            
            $cardId = $request->getParam('cid',false);
            $cancel = true;
            if ( !empty($cardId) ) {
                $cardDetails  = $this->_card->getCardDetails($cardId);
                
                if ( empty ($cardDetails)) {
                    $this->view->errorMessages['card'] = 'The card not found';
                    $cancel = false;
                } else if ($cardDetails->user_id != Zend_Auth::getInstance()->getIdentity()->id) {
                   // echo $this->userId;exit;
                    $this->view->errorMessages['card'] = 'You cannot cancel the card that is not yours';
                    $cancel = false;
                } else {
                    $this->view->cardDetails = $cardDetails;
                }
            } else {
                $this->view->errorMessages['card'] = 'The card not found';
                $cancel = false;
            }
            $this->view->cancel = $cancel;
            if ($request->isPost() && $cancel) {
    
                $userPassword = $request->getParam('password' , null);
                if (empty ($userPassword) ) {
                    $this->view->error                     = true;
                    $this->view->errorMessages['password'] = 'Please enter password';
                } else {
                    $passwordValidation = $this->_user->validateUserPassword(Zend_Auth::getInstance()->getIdentity()->id , $userPassword);
                    
                    if ($passwordValidation === true) {
                        
                        $result = $this->_card->cancelCard ($cardId , Zend_Auth::getInstance()->getIdentity()->id);
                        
                        if (! isset ($result->error)) {
                            $accountSession = new Zend_Session_Namespace('account');
                            $accountSession->message = 'Your card has been cancelled';
                            $this->_redirect('/account');
                        }
                        
                        $this->view->error                     = true;
                        $this->view->errorMessages['card']     = 'Cancel failed';
                        $this->view->postVariables             = $request->getParams();
                    } else {
                        $this->view->error                     = true;
                        $this->view->errorMessages['password'] = 'Please enter correct password';
                    }

                   

                }
                

            }
    
        }
        catch (Exception $e) {
            var_dump($e);exit;
        }
         
         
    
    }
}
