<?php
/**
 * @version JrodanMedia 3.0
 * @author mohammad.mohsin <mohammad.mohsin@jordanmedia.co.uk>
 * @copyright Copyright (c) 2012-2013, Jordan Media Limited
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Service;

/**
 * @package service
 * @subpackage Company
 */

class User extends Base {

    /**
     *
     * @var Doctrine\ORM\EntityManager
     */
    protected $_entityManager = null;

    /**
     *
     * @var \sfServiceContainer
     */
    protected $_service = null;

    protected $_db = null;
    protected $_identity = null;

    public function  __construct()
    {
        parent::__construct();

        $config = \Zend_Registry::get('config');
        $this->_serverName = '';
        

        $this->_service       = \Zend_Registry::get('sc');
        $this->_entityManager = \Zend_Registry::get('em');
        
        $this->_db = \Zend_Registry::get('db');
        
        $this->_identity      = $this->_service->getService('identity');


    }

    /**
     *
     * @param str $userName
     * @param str $password
     * @return object
     */
    public function createUser($params = array())
    {

        try {
 
            $result = $this->validateUserParams ($params);


            if (empty($result)) {
                $data = array( 'title' => $params['title'],
                    		    'first_name' => $params['first_name'],
                        		'last_name' => $params['last_name'],
                        		'dob' => $params['date-dropdown_dateLists_year_list'] ."-".$params['date-dropdown_dateLists_month_list']."-".$params['date-dropdown_dateLists_day_list'] .' 00:00:00',
                                'gender' => $params['gender'],
                                'address1' => $params['address1'],
                                'address2' => $params['address2'],
                                'post_code' => $params['post_code'],
                                'phone_number' => $params['phone_number'],
                                'security_answer' => $params['security_answer'],
                                'user_email' => $params['user_email'],
                                'password' => $this->_identity->passwordCreator($params['user_password_new']),
                                'city' => $params['city'],
                                'county' => $params['county'],
                                'security_question' =>$params['security_question'],
                                'created_on' => new \Zend_Db_Expr('NOW()'),
                                'modified_on' => new \Zend_Db_Expr('NOW()')
                        
                		    );
                $result = $this->_db->insert('asda_user', $data);
                
                if ($result && isset($params['is_subscribed'])) {
                    $userId = $this->_db->lastInsertId();
                    
                    $entity = new \App_Entity_NewsSignup();
                    $entity->setUserId($userId);
                    $entity->setActive(1);
                    $entity->store();
                }
                
                //
                //Mail function goes here
                //
                //
                
            } else {
               return $userRegister = (object) array(
                                                'error'    => 1,    
                                                'userRegisterMessage' => $result
                                        );
            }
            
            return true;
            

        } catch (\Exception $e) {
            echo "<pre>";
            var_dump($e);exit;
        }
    }
    
    private function validateUserParams ($params) {

        $validateResult = true;
        $error = array();

        if (empty($params['title']) || $params['title'] == 'selected'){ // gender
        
            $error['title'] = "Please select title";
            $validateResult = false;
        
        }
        if (empty($params['first_name']) 
                || strlen($params['first_name']) > 32
                || preg_match("/^[a-zA-Z]*$/", $params['first_name']) ==false
           ){ // first name

            $error['firstname'] = "Please enter a valid first name";
            $validateResult = false;

        } 

        
        if (empty($params['last_name']) 
                || strlen($params['last_name']) > 32 
                || preg_match("/^[a-zA-Z]*$/", $params['last_name']) == false 
           ){ // last name

            $error['lastname'] = "Please enter a valid last name";
            $validateResult = false;

        } 
        
        $params['phone_number'] = str_replace(' ' ,'' ,$params['phone_number']);
        $params['phone_number'] = str_replace('-' ,'' ,$params['phone_number']);
        if (empty($params['phone_number'])){ // phone number

            $error['phone'] = "Enter a phone number";
            $validateResult = false;

        }
        else  if (!preg_match('/^\d{10,14}$/i',$params['phone_number'])) { // phone # validity

            $error['phone'] = "Please enter a valid phone number";
            $validateResult = false;

        }
        $dateValidation = true;
        if (empty($params['date-dropdown_dateLists_day_list'])){ // dob

            $error['dob1'] = "Please select the day of your birthday";
            $validateResult = false;
            $dateValidation = false;

        }
        
        if (empty($params['date-dropdown_dateLists_month_list'])) {

            $error['dob2'] = "Please select the month of your birthday";
            $validateResult = false;
            $dateValidation = false;

        }
        
        if (empty($params['date-dropdown_dateLists_year_list'])) {

            $error['dob3'] = "Please select the year of your birthday";
            $validateResult = false;
            $dateValidation = false;

        }
        
        if ($dateValidation === true) {
            $dob          = strtotime($params['date-dropdown_dateLists_year_list'] ."-".$params['date-dropdown_dateLists_month_list']."-".$params['date-dropdown_dateLists_day_list'] .' 00:00:00');
            $validateDate = strtotime((date('Y')-8) .'-' . date('m') .'-'. date('d') .' 00:00:00');
            //echo "$dob > $validateDate";exit;
            if ($dob > $validateDate) {
                $error['dob'] = "You should be 8 years old to register with asda card";
                //echo 'asdasd';exit;
            }
        }
        
        if (empty($params['gender'])){ // gender

            $error['gender'] = "Please select gender";
            $validateResult = false;

        }
        
        if (empty($params['address1'])){ // address 1

            $error['address1'] = "Please enter 1st line of address";
            $validateResult = false;

        }
        else if (!empty($params['address1']) && preg_match("/^[0-9a-zA-Z ,]*$/", $params['address1']) == false){ // address 1

            $error['address1'] = "This is not a valid address";
            $validateResult = false;

        }

        if (! empty($params['address2']) 
                && (preg_match("/^[0-9a-zA-Z ,]*$/", $params['address2']) == false 
                    || is_numeric($params['address2']))) {// address 2

            $error['address2'] = "This is not a valid address";
            $validateResult = false;

        }

        if (empty($params['post_code'])){ // address 2

            $error['postcode'] = "Please enter a postcode";
            $validateResult = false;
             
        } else if (!empty($params['postcode']) 
                && (preg_match("/^[0-9a-zA-Z ]*$/", $params['postcode']) == false 
                        || is_numeric($params['postcode']))) {
            
            $error['postcode'] = "Please enter a valid postcode";
            $validateResult = false;
        }
        
        if (empty($params['city'])) {

            $error['city'] = "Please enter a city/town";
            $validateResult = false;

        } else if (!empty($params['city']) 
                &&( (preg_match("/^[0-9a-zA-Z ]*$/", $params['city']) == false) || is_numeric($params['city']))) {
            $error['city'] = "Please enter a valid city";
            $validateResult = false;
        }
        
        if (! empty($params['county']) && 
                (preg_match("/^[0-9a-zA-Z ]*$/", $params['county']) == false )  
                             || is_numeric($params['county'])){ // address 2
        
            $error['county'] = "This is not a valid address";
            $validateResult = false;
        
        }
        

        if (empty($params['security_answer']) || is_numeric($params['security_answer']) ){ // security answer

            $error['securityanswer'] = "Please enter a valid answer";
            $validateResult = false;

        }


        $password        = $params['user_password_new'];
        $confirmPassword = $params['user_password_repeat'];
        if (empty($password)) {

            $error['password'] = "please empty password";
            $validateResult = false;

        }
        else if (strlen($password) < 6) {
            $error['password'] = "Password should be minimum of 6 characters!";
            $validateResult = false;
        }

        else if ((!preg_match("#[0-9]+#", $password)) || (!preg_match("#[a-zA-Z]+#", $password)) ) {
            $error['password'] = "Password must should be a mixture of letters and numbers!";
            $validateResult = false;
        }

        if ($confirmPassword != $password) {

            $error['password_not_same'] = "Password and password repeat are not the same";
            $validateResult = false;

        }


        $user_email         = trim($params['user_email']);
        $user_email_confirm = trim($params['user_email_confirm']);

        if (empty($params['user_email'])) {

            $error['email1'] = "Please enter your email address";
            $validateResult = false;

        } else if (strlen($params['user_email']) > 64) {

            //$error['email2'] = "Email cannot be longer than 64 characters";
            $error['email1'] = "Email cannot be longer than 64 characters";
            $validateResult = false;

        } else if(!filter_var($params['user_email'], FILTER_VALIDATE_EMAIL)) {

            //$error['email3'] = "Your email adress is not in a valid email format";
            $error['email1'] = "Your email adress is not in a valid email format";
            $validateResult = false;

        } else if ($user_email != $user_email_confirm){
            //echo "$user_email != $user_email_confirm";exit;
            $error['email1'] = 'Email and confirm emails are not matching' ;
            $validateResult = false;
        }

        $userCaptcha = $params['user_captcha'];
        if ( empty($userCaptcha ) || ( $userCaptcha != $_SESSION['captcha'] )) {
            $error['captcha_error'] = 'Your validation code is not matching' ;
            $validateResult = false;
        }
        
        if ($validateResult) {
            $this->_db = \Zend_Registry::get('db');
            $sql = " SELECT id  FROM asda_user WHERE user_email = ' $user_email'";
            $result = $this->_db->fetchAll($sql);
            
            if (count ($result) > 0 ) {
                $error['email1'] = 'Sorry, this email address is already in our systems.<br/>Please choose another one' ;
                $validateResult = false;
            }
            
        }
        

        
         
        return $error;
    }
    
    public function getUserDetails ($userId = null) {
        try {
            if ( empty($userId) ) {
                $userId = \Zend_Auth::getInstance()->getIdentity()->id;
            }
            $query  =  'SELECT
                        id,title,first_name,last_name,phone_number,dob,gender,
                        address1,address2,post_code,security_answer,user_email,PASSWORD,
                        city,county,security_question,active
                        FROM
                        asda_user
                        WHERE
                        id = '.$userId;
            $execute = $this->_db->query($query);
            $result = $execute->fetchObject();
            if (count ($result) == 0) {
                $this->error['user'] = 'User not found';;
                return false;
            }
            return $result;
        } catch (Exception $e) {
    
        }
    
    }
    
    public function validateUserPassword ($userId , $passwordString) {
        try {

            $passwordString = $this->_identity->passwordCreator($passwordString);
    
            $query  =  'SELECT password FROM asda_user WHERE  id = '.$userId;
            $execute = $this->_db->query($query);
            $userObj = $execute->fetchObject();
            if ($userObj) {
                
                //echo "$userObj->password == $passwordString";exit;
                if ($userObj->password == $passwordString) {

                    return true;
                }
            }
    
            return false;
    
    
        } catch (Exception $e) {
    
        }
    }
    
    public function forgotPassword($emailAddress)
    {
    
        try {
            
            $error    = '' ;
            $validate = true;

           
            //echo 'dddddddddddd22';exit;
            if( !filter_var($emailAddress, FILTER_VALIDATE_EMAIL) ) {
    
                $validate = false;
                $error = "Your email adress is not in a valid email format";

    
            }
            $emailId   =  $this->_db->quote($emailAddress);
            // check if user already exists
            $query   = 'SELECT id FROM asda_user WHERE user_email ='. $emailId;
            $execute = $this->_db->query($query);
            $result = $execute->fetchObject();
    
            if ($result === false) {
    
                $error = "Sorry, this email address does not exist in our system.";
                $validate = false;
    
            }
            
            if ($validate === true) {
            
                $userId = $result->id;
    
        
                $passwordKey =$this->generateRandomString(20);
        
                //$query = "INSERT INTO asda_password_reset (user_id, reset_key, created_on) VALUES('$userId', '$passwordKey',NOW())";
                
               $data = array( 'user_id' =>  $userId,
                        'reset_key' => $passwordKey,
                        'created_on' =>  new \Zend_Db_Expr('NOW()'),

                        'modified_on' => new \Zend_Db_Expr('NOW()')
                
                );

                
                // write new users data into database
                $result = $this->_db->insert('asda_password_reset', $data);
                $passwordResetId = $this->_db->lastInsertId();
                if ($result) {
                    //send mail function goes here
                    //$mailService = new EmailService();
                    //$result = $mailService->forgotPassword($emailId, $passwordKey);
                    $result = true;//hardcoding now. need to change when mail function ready
                    if ($result === true) {
                        return true;
                    } else {

                        $data = array ('active' => 0, 'modified_on' => new \Zend_Db_Expr('NOW()')) ;

                        $where = array();
                        $where[] = "id = $passwordResetId";

                        $result =  $this->_db->update('asda_password_reset', $data, $where);
                        
                        $error = "Unable to send reset password link please try again later .";
                    }

                }
                else {
                    $error = "Sorry there are some system problem please try again later .";
                }
            }
            
            return $error = (object) array(
                        'error'    => 1,
                        'errorMessage' => $error
                );
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
    
    public function validateRestPasswordLink ($key) {
        try {
            
            $error    = null;
            $validate = true;
            
            $query   = "SELECT user_id,created_on FROM asda_password_reset WHERE reset_key=" . $this->_db->quote($key)." AND expired = 0 AND active=1";
            $execute = $this->_db->query($query);
            $resultObject  = $execute->fetchObject();  
            if ($resultObject === false) {
                $error = 'Your request has been expired';
                $validate = false;
    
            } else {
                $createdOn = strtotime($resultObject->created_on);
                $date      = strtotime('now');
                
                if (($createdOn + 86400) <  $date) { // checking 24 hrs
                    $error = "Your request has been expired";
                    $validate = false;

                }
                return $resultObject->user_id;
            }
    
            if ($validate === false) {
                return $error = (object) array(
                        'error'    => 1,
                        'errorMessage' => $error
                );
            }
    
    
        } catch (Exception $e) {
    
        }

    }
    
    
    public function getSecurityQuestion ($userId) {
        try {
            if (empty($userId)) {
                return false;
            }
            $query   = "SELECT security_question FROM asda_user WHERE id =  ".$this->_db->quote($userId);
            
            $execute      = $this->_db->query($query);
            $resultObject = $execute->fetchObject();
            
            if ($resultObject === false) {
                $this->errors[] = 'User not found';
            }
            return $resultObject->security_question;
        
        } catch (Exception $e) {
        
        }
    }
    
    public function resetPassword ($userId , $resetKey , $params) {
        try {
            $validateResult   = true;
            $error            = array();
            $password         = $params['user_password_new'];
            $confirmPassword  = $params['user_password_repeat'];
            $securityAnswer   = $params['security_answer'];
            
            if (empty($securityAnswer)){ // security answer
        
                $error['securityanswer'] = "Enter a Security Answer";
                $validateResult = false;
        
            }
        
            if (empty($password)) {
        
                $error['password'] = "Empty Password";
                $validateResult = false;
        
            }
            else if (strlen($password) < 6) {
                $error['password'] = "Password should be minimum of 6 characters!";
                $validateResult = false;
            }
        
            else if ((!preg_match("#[0-9]+#", $password)) || (!preg_match("#[a-zA-Z]+#", $password)) ) {
                $error['password'] = "Password must should be a mixture of letters and numbers!";
                $validateResult = false;
            }
        
            if ($confirmPassword != $password) {
        
                $error['password_not_same'] = "Password and password repeat are not the same";
                $validateResult = false;
        
            }
            //echo 'adasd';exit;
            if ($validateResult) {
    
                $query   = "SELECT security_answer FROM asda_user WHERE id =  ".$this->_db->quote($userId)." AND active=1";
                $execute = $this->_db->query($query);
                $resultObject  = $execute->fetchObject (); 
                if ($resultObject === false) {
                    $error['user'] = 'User does not exit';
                     $validateResult = false;
                }

                if ($resultObject->security_answer != $securityAnswer) {
                    $error['securityanswer'] = "Your security answer is not correct. Please type the currect answer";
                    $validateResult = false;
                }
                
                if ($validateResult) {

                    $passwordString = $this->_identity->passwordCreator($password);

                    $data = array ('password' => $passwordString, 'modified_on' => new \Zend_Db_Expr('NOW()')) ;
                    
                    $where = array();
                    $where[] = "id = $userId";
                    
                    $result =  $this->_db->update('asda_user', $data, $where);
            
                    if ($result) {
                        
                        $data = array ('expired' => 1, 'modified_on' => new \Zend_Db_Expr('NOW()')) ;
                        
                        $where = array();
                        $where[] = "reset_key = ".$this->_db->quote($resetKey);
                        $where[] = "user_id   = user_id";
                        
                        $result =  $this->_db->update('asda_password_reset', $data, $where);
                        
                        if ($result == 1) {
                            return  true;
                        }
                    }
                }

            }
            
            if ($validateResult === false ) {
                return $error = (object) array(
                        'error'    => 1,
                        'errorMessage' => $error
                );
            }
        } catch (Exception $e) {
            var_dump($e);exit;
        }
    }
    
    public function testFlorish () {
        $results = \fRecordSet::build("App_Entity_Cards",
                array('cardpin=' => 1111));
        
        $entity = new \App_Entity_Cards();
        $entity->setUserId(23);
        $entity->setCardnumber('6333904562321345');
        $entity->setCardpin('3213');
        $entity->setCardname('test me 123');
        $entity->setCardfor(1);
        $entity->setBalance('10');
        $entity->setActive(1);
        $entity->store();
        
        
        var_dump($results);exit;
    }

}
