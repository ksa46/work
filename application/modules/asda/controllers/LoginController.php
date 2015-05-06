<?php

class Asda_LoginController extends Zend_Controller_Action
{



    /**
     * @var \sfServiceContainer
     */
    protected $_service = null;



    public function init()
    {
        $this->_service = Zend_Registry::get('sc');
        
        $this->_identity = $this->_service->getService('identity');
        
        $this->_user = $this->_service->getService('user');
    }
        
    public function indexAction()
    {

        $request = $this->getRequest();
        try {
            $this->view->userEmail = '' ;
            if ($request->isPost()) {
                $userMail     = $request->getParam('user_email', '');
                $userPassword = $request->getParam('password', '');
                if (empty ($userMail) || empty ($userPassword) ) {
                    throw new Exception('Fields are not matching');
                } else {
                $result = $this->_identity->authenticate ($userMail,$userPassword);
                if (! isset ($result->error)) {
                    $this->_redirect('/account');
                }
                
                    $this->view->error = $result->error;
                    $this->view->UserError = $result->userErrorMessage;
                    $this->view->passwordError = $result->passwordErrorMessage;
                    $this->view->userEmail = $userMail ;
                }
                
            }
            $registerSession     = new Zend_Session_Namespace('register');
            $this->view->message = $registerSession->message;
            $registerSession->message = null;

        }
        catch (Exception $e) {
            
        }
             
             
        
    }
    
    public function logoutAction()
    {

        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect('/index');
    }
    
    public function forgotPasswordAction()
    {
    
        $request = $this->getRequest();
        try {
            $this->view->linkSend = false ;
            $this->view->error = '';
            $this->view->errorMessage = '';
            
            if ($request->isPost()) {

                 $email = $request->getParam('email' , null);

                if (empty ($email)  ) {
                    $this->view->errorMessage = 'Please enter your email address';
                } else {

                    $result = $this->_user->forgotPassword ($email);

                    if (! isset ($result->error)) {
                        $this->view->linkSend = true ;
                        $this->view->email = $email;
                    }

                    $this->view->error = $result->error;
                    $this->view->errorMessage = $result->errorMessage;

                }
    
            }
    
        }
        catch (Exception $e) {
            $this->view->errorMessage = $e->getMessage();
        }

    }
    
    public function resetPasswordAction()
    {
    
        $request = $this->getRequest();
        try {
            $this->view->linkSend          = false ;
            $this->view->error             = '';
            $this->view->errorMessage      = '';
            $this->view->keyErrorMessage   = '';
            $this->view->securityQuestion  = '';
            $this->view->success           = false;
    
            $this->view->resetKey = $request->getParam('k' , null);
            
            if ( empty ($this->view->resetKey ) ) {
                
                $this->view->keyErrorMessage = 'Invalid request';
                
            } else {
                
                $validate = $this->_user->validateRestPasswordLink($this->view->resetKey );
                
                if (isset ($validate->error)) {
                    $this->view->error = $validate->error;
                    $this->view->keyErrorMessage = $validate->errorMessage;
                } else {
                    $userId = $validate;
                    $this->view->securityQuestion = $this->_user->getSecurityQuestion($userId);
                    
                    if ($request->isPost()) {

                        $result = $this->_user->resetPassword ($userId , $this->view->resetKey , $request->getParams());
                        

                        if ( isset ($result->error)) {
                            $this->view->error = $result->error;
                            $this->view->errorMessage = $result->errorMessage;
                        } else {
                            $this->view->success = true;
                        }
                    
                    }
                }

    
            }
    
        }
        catch (Exception $e) {
             $this->view->errorMessage = $e->getMessage();
        }
    
    }

}
