<?php

class Asda_AddressController extends Zend_Controller_Action
{

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $_em = null;

    /**
     * @var \sfServiceContainer
     */
    protected $_sc = null;
    
    protected $userId = null;

    /**
     * @var \App\Service\RandomQuote
     * @InjectService RandomQuote
     */
    protected $_randomQuote = null;

    public function init()
    {
        $this->_service = Zend_Registry::get('sc');

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

            
            $this->view->addressType =  $this->_request->getParam('t', 1);
            
            $this->view->typeError = true;
            $arrAddressTypes = array(1,2,3);
            if (in_array($this->view->addressType, $arrAddressTypes)) {
                $this->view->typeError = false;
            }

            
            if ( $this->_request->isPost()) {
                    //var_dump($this->_request->getParams());exit;
                $result     = $this->_address->addAddress($this->_request->getParams());

                if (! isset ($result->error)) {
                    $accountSession = new Zend_Session_Namespace('account');
                    $accountSession->message = 'Your address has been updated';

                    $this->_redirect('/account');
                }
                $this->view->error                    = $result->error;
                $this->view->errorMessages            = $result->addressMessage;

                
                $this->view->address1 = $_POST['address1'];
                $this->view->address2 = $_POST['address2'];
                $this->view->city     = $_POST['city'];
                $this->view->county   = $_POST['county'];
                $this->view->postcode = $_POST['post_code'];

            } else {
                $address     = $this->_address->getUserAddress($this->userId , $this->view->addressType);
                if (is_object ($address) ) {
                    $this->view->address1 = $address->address1;
                    $this->view->address2 = $address->address2;
                    $this->view->city     = $address->city;
                    $this->view->county   = $address->county;
                    $this->view->postcode = $address->post_code;
                }
            }
            

        }
        catch (Exception $e) {
            var_dump($e);exit;
        }
             
             
        
    } 
}
