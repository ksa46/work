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

class Identity extends Base {

    /**
     *
     * @var Doctrine\ORM\EntityManager
     */
    protected $_em = null;

    /**
     *
     * @var \sfServiceContainer
     */
    protected $_service = null;

    protected $_serverName = null;
    protected $_activity = null;

    public function  __construct()
    {
        parent::__construct();

        $config = \Zend_Registry::get('config');
        $this->_serverName = '';
        

        $this->_service = \Zend_Registry::get('sc');
        $this->_em = \Zend_Registry::get('em');
        



    }

    /**
     *
     * @param str $userName
     * @param str $password
     * @return object
     */
    public function authenticate($userName, $password)
    {

        try {
            $db = \Zend_Registry::get('db');
            $sql = <<<EOF
SELECT id , user_email, password
FROM asda_user
WHERE user_email = '$userName';
EOF;
            $result = $db->fetchAll($sql);

            
            if (count($result) > 0) {
                    $userDetails = $result[0];

                    $userPassword = $this->passwordCreator($password);
                    if ($userDetails['password'] == $userPassword) {

                        $userIdentity = (object) array(
                            'username' => $userName,
                            'password' => $userPassword,
                        );
                        if ($this->writeAuthentication($userIdentity))
                        {
                            $authInstance = \Zend_Auth::getInstance()->getIdentity();

                            $userId = $authInstance->id;
                            
                        } else {
                            $userIdentity = (object) array(
                            'username' => $userName,
                            'error'    => 1,    
                            'userErrorMessage' => 'Authentication failed',
                        );
                        }
                        return $userIdentity;
                    
                } else {
                        $userIdentity = (object) array(
                            'username' => $userName,
                            'error'    => 1,    
                            'passwordErrorMessage' => 'Wrong password. Try again',
                        );
                    return $userIdentity;
                }

            } else {
                $userIdentity = (object) array(
                            'username' => $userName,
                            'error'    => 1,    
                            'userErrorMessage' => 'This user does not exist',
                        );
                 return $userIdentity;
            }

        } catch (\Exception $e) {
            echo "<pre>";
            var_dump($e);exit;
        }
    }
    
    public function passwordCreator ($passwordString) {
        $password = sha1($passwordString.PASSWORD_SALT);
        return $password;
    }

    /**
     *
     * check the email address validation
     * @param str $emailaddress
     * @return object
     *
     */


    /**
     *
     * @return boolean
     */
    public function getRole()
    {
        $userIdentity = $this->getIdentity();
        if ($userIdentity === false) {
            return false;
        }
        return $userIdentity->role;
    }

    /**
     *
     * @param object $values
     * @return boolean
     *
     */
    protected function writeAuthentication($values)
    {
        $adapter = $this->getAuthAdapter();

        $adapter->setIdentity($values->username);
        $adapter->setCredential($values->password);

        $auth = \Zend_Auth::getInstance();
        $result = $auth->authenticate($adapter);

        if ($result->isValid()) {
            $user = $adapter->getResultRowObject();
            $auth->getStorage()->write($user);
            return true;
        }
        return false;
    }

    /**
     *
     * @return \Zend_Auth_Adapter_DbTable
     */
    protected function getAuthAdapter()
    {
        $dbAdapter = \Zend_Db_Table::getDefaultAdapter();

        $authAdapter = new \Zend_Auth_Adapter_DbTable($dbAdapter);

        $authAdapter->setTableName('asda_user');
        $authAdapter->setIdentityColumn('user_email');
        $authAdapter->setCredentialColumn('password');

        return $authAdapter;
    }

    /**
     *
     * @param int $userId
     * @param int $companyId
     * @return \App\Service\Exception
     *
     */
    public function userLogout($userId, $companyId)
    {
        try {
            $actiInfo = $this->_em->getRepository("\App\Entity\Activities")->getOneById($userId);
            $lastlogin = $actiInfo[0]['_activityCreatedOn'];

            $userInfo = $this->_em->getRepository("\App\Entity\Users")->getOneById($userId);
            $userInfo->setUserLastLogin($lastlogin);
            $userInfo->setUserLoginStatus(0);
            $this->_em->merge($userInfo);
            $this->_em->flush();

            //save activity in activities table
            $uInfo = \Zend_Auth::getInstance()->getIdentity();
            $this->userId = $uInfo->user_id;
            $this->companyId = $uInfo->user_company_id;
            $this->activityDesc = 'user logout';

            $this->_activity->addActivity($activityType = 2,
                    $this->userId,
                    $this->companyId,
                    $activityPredicate = 2,
                    $activityTemplate = 2,
                    $this->activityDesc,
                    $aoei = null,
                    $aoeci = null,
                    $aoai = null,
                    $aoaci = null,
                    $activitySubaccount = null,
                    $activityGroup = null,
                    $acmn = null,
                    $acmdes = null,
                    $acmi = null,
                    $retailerId = $companyId
              );

        } catch (Exception $e) {
            return $e;
        }

    }

    /**
     *
     * @param int $user_id
     * @return array
     *
     */
    public function getUserDetailById($user_id)
    {
        try {
            $userCompanyInfo = array();
            $userInfo = $this->_em->getRepository("\App\Entity\Users")->getOneById($user_id);
            array_push($userCompanyInfo, array("user" => $userInfo));
            $userProInfo = $this->_em->getRepository("\App\Entity\UserProfiles")->getUserProfileById($user_id);
            array_push($userCompanyInfo, array("userprofile" => $userProInfo));

            $userCompany = $this->_em->getRepository("\App\Entity\Companys")->getComanyNameId($userInfo->getUserCompanyId());

            array_push($userCompanyInfo, array("company" => $userCompany));

            $userMessage = $this->_em->getRepository("\App\Entity\MessageDetails")->getOneById($userInfo->getId(), $rstatus = 0);
            array_push($userCompanyInfo, array("totusermessage" => $userMessage));
            $comMember = $this->_em->getRepository("\App\Entity\Memberships")->getRetailerInfo($userInfo->getUserCompanyId());
            $comM = array();
            foreach ($comMember as $v) {
                $comName = $this->_em->getRepository("\App\Entity\Companys")->getComanyNameId($v->getMembershipObjectId());
                array_push($comM, $comName);
            }
            array_push($userCompanyInfo, array("comRetailer" => $comM));
            $userRole = $this->_em->getRepository("\App\Entity\UserTypes")->getOneById($userInfo->getUserUrId());
            array_push($userCompanyInfo, array("usrrole" => $userRole));

            return $userCompanyInfo;
        } catch (Exception $e) {
            var_dump($e);
        }
    }

    /**
     *
     * @param int $userId
     * @param int $password
     * @return boolean
     *
     */
    public function validatePassword($userId, $password)
    {
        $this->_em = \Zend_Registry::get('em');

        try {
            //check the user status
            $user = $this->_em->getRepository("\App\Entity\Users")->find($userId);

            if (count($user) > 0) {
                if ($user->getUserStatus() == 1)
                {
                    $userPassword = md5(JMPASSWORDSALT.$password);
                    if ($user->getUserPassword() == $userPassword) {
                        return true;
                    }
                }
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
