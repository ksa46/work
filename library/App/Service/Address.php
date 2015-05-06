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

class Address extends Base {

    /**
     *
     * @var Doctrine\ORM\EntityManager
     */
    protected $_em = null;

    /**
     *
     * @var \sfServiceContainer
     */
    protected $_sc = null;
    
    protected $_db = null;

    protected $_identity = null;
    protected $_arrAddressTypes = null;

    public function  __construct()
    {
        parent::__construct();

        $config = \Zend_Registry::get('config');
        $this->_serverName = '';
        

        $this->_service       = \Zend_Registry::get('sc');
        $this->_entityManager = \Zend_Registry::get('em');
        
        $this->_db = \Zend_Registry::get('db');
        
        $this->_identity      = $this->_service->getService('identity');
        
        $this->arrAddressTypes = array (1,2,3);


    }
    
    public function getUserAddress ($userId , $type =1) {
        try {
            
            if ( empty($userId) ) {
                $error['user_address'] = 'Invalid user id';
                return false;
            }
            $returnResult = false;
            $query  =  "SELECT
                            address1,address2,post_code,city,county
                        FROM
                            asda_address
                        WHERE
                            user_id = $userId AND active=1 AND address_type = ".$this->_db->quote($type);
            $result = $this->_db->query($query);
            if (count ($result) == 0) {
                $error['user'] = 'Address not found';;
                return false;
            }
            $returnResult = $result->fetchObject(); 
            return $returnResult; 
        } catch (Exception $e) {
        
        }
        
    }
    
    public function addAddress($params = array())
    {
    
        try {
            $db = \Zend_Registry::get('db');
    
            $validationError = $this->validateAddress ($params) ;
            //var_dump($validationError);exit;
            
            $userId = \Zend_Auth::getInstance()->getIdentity()->id;
            
            $addressType = $this->_db->quote($params['address_type']);
    
            if (empty ($validationError) ) {

                $data = array( 
                        'post_code' => $params['post_code'],
                        'address1' => $params['address1'],
                        'address2' => $params['address2'],
                        'county' => $params['county'],
                        'city' => $params['city'],
                        'modified_on' => new \Zend_Db_Expr('NOW()')
                
                );
                
                
                $query = "SELECT
                id
                FROM
                asda_address
                WHERE
                user_id = $userId AND  address_type = $addressType AND active=1"; 
                $veryfyAddress = $this->_db->query($query);

                    $result = $veryfyAddress->fetchObject();

                if ( $result) {

                    $where = array();
                    $where[] = "user_id = $userId";
                    $where[] = "address_type = $addressType";
                    $result =  $this->_db->update('asda_address', $data, $where);

                    if ($result) {

                        return true;

                    } else {
                        return $addressAdd = (object) array(
                                'error'    => 1,
                                'addressMessage' =>'Update failed'
                        );
                    }
                
                } else {
                    $data['user_id']      = $userId;
                    $data['address_type'] = $params['address_type'];
                    $data['active']       = 1;

                    // write new users data into database
                    $result = $this->_db->insert('asda_address', $data);

                    if ($result) {
                        return true;

                    } else {
                            return $addressAdd = (object) array(
                                    'error'    => 1,
                                    'addressMessage' => 'Insert failed'
                            );
                
                    }
                }
                
                return false;
                //
            } else {
                return $addressAdd = (object) array(
                        'error'    => 1,
                        'addressMessage' =>             $validationError
                );
            }

    
    
            return true;
    
    
        } catch (\Exception $e) {
            echo "<pre>";
            var_dump($e);exit;
        }
    }
    
    private function validateAddress ($params) {
        
        $validateResult = true;
        $errors = array();
        if (empty($params['address1'])){ // address 1
        
            $errors['address1'] = "Enter 1st Line of Address";
            $validateResult = false;
        
        }
        
        if (!empty($params['address1']) && is_numeric($params['address1'])){ // address 1
        
            $errors['address1'] = "This is not a valid address";
            $validateResult = false;
        
        }
        
        if (! empty($params['address2']) && is_numeric($params['address2'])){ // address 2
        
            $errors['address2'] = "This is not a valid address";
            $validateResult = false;
        
        }
        
        if (empty($params['post_code'])){ // address 2
        
            $errors['postcode'] = "Please Enter a postcode";
            $validateResult = false;
             
        }
        
        if (!empty($params['post_code'])
                && (is_numeric($params['post_code'])
                        || strlen($params['post_code']) < 5
                        || strlen($params['post_code']) > 9)){ // address 1
        
            $errors['postcode'] = "This is not a valid address";
            $validateResult = false;
        
        }
        
        if (empty($params['city'])) {
        
            $errors['city'] = "Please enter a City/Town";
            $validateResult = false;
        
        }
        
        if (!empty($params['city']) && is_numeric($params['city'])){ // address 1
        
            $errors['city'] = "This is not a valid address";
            $validateResult = false;
        
        }
        
        $addressType = isset($params['address_type']) ? (int) $params['address_type'] : 1;
        if (! in_array($addressType , $this->arrAddressTypes)) {
            $errors['postcode'] = "Can not add address this time";
            $validateResult = false;
        }
        
        return $errors;
    }
    
}
