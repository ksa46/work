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

class Card extends Base {

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

    protected $_serverName = null;
    protected $_activity = null;

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
    public function createCard($params = array())
    {

        try {
            $db = \Zend_Registry::get('db');
            
            $validateResult = true;
            $errors = array();
            if (!preg_match('/^\d{4,4}$/i', $params['card_code']))  {
                $errors['cardpin'] = "Please enter a valid 4 digit long security code.";
                $validateResult = false;
    
            }
    
            if (!preg_match('/^\d{16,16}$/i', $params['card_number']))  {
                $errors['cardnumber'] = "Please enter a valid 16 digit long card number.";
                $validateResult = false;
    
            }

            if ($validateResult) {

                
                $results = \fRecordSet::build("App_Entity_Cards", array('cardnumber=' => $params['card_number'] , 'active=' => 1));
                
                if ($results->count() > 0) {

                    $errors['cardnumber'] = "Sorry, this card number is already registered in our systems!";
                    return $cardRegister = (object) array(
                            'error'    => 1,
                            'cardRegisterMessage' => $errors
                    );
                    
                } else {
                    /*
                    $data = array( 'user_id' =>  \Zend_Auth::getInstance()->getIdentity()->id,
                            'cardnumber' => $params['card_number'],
                            'cardpin' => $params['card_code'],
                            'cardname' => $params['card_name'],
                            'cardfor' => $params['cardowner'],
                            'balance' => 0,
                            'active' => 1,
                            'created_on' => new \Zend_Db_Expr('NOW()'),
                            'modified_on' => new \Zend_Db_Expr('NOW()')
                    
                    );
                    $result = $db->insert('asda_cards', $data);
                    */
                    $entity = new \App_Entity_Cards();
                    $entity->setUserId(\Zend_Auth::getInstance()->getIdentity()->id);
                    $entity->setCardnumber($params['card_number']);
                    $entity->setCardpin($params['card_code']);
                    $entity->setCardname($params['card_name']);
                    $entity->setCardfor($params['cardowner']);
                    $entity->setBalance(0);
                    $entity->setActive(1);
                    $entity->store();

                }
                //
            } else {
                return $cardRegister = (object) array(
                        'error'    => 1,
                        'cardRegisterMessage' => $errors
                );
            }

            
            return true;
            

        } catch (\Exception $e) {
            echo "<pre>";
            var_dump($e);exit;
        }
    }
    
    public function getCardDetails ( $cardId ) {
        try {
            if ( empty ($cardId) ) {
                $this->errors['cardInfo'] = 'User id is empty';
                return false;
            }
            $query  = 'SELECT id, user_id, cardnumber,cardpin,cardname,cardfor,balance,active
                        FROM asda_cards where id ='.$this->_db->quote($cardId);
            
            $execute = $this->_db->query($query);
            $result  = $execute->fetchObject();
            
            return $result;
    
        } catch (Exception $e) {
    
        }
    }
    public function getUserCardDetails ($userId = null) {
        try {
            if ( empty ($userId) ) {
                $error = array();
                $error['cardInfo'] = 'User id is empty';
                return false;
            }
            $returnArray = array();
            $results = \fRecordSet::build("App_Entity_Cards", array('user_id=' => $userId , 'active=' => 1));
            if (count ($result) == 0) {
                foreach ($results as $row) {
                    $object = new \stdClass();
                    $object->cardId     = $row->getId();
                    $object->cardnumber = $row->getCardnumber();
                    $object->cardpin    = $row->getCardpin();
                    $object->cardname   = $row->getCardname();
                    $object->cardfor    = $row->getCardfor();
                    $object->balance    = $row->getBalance();
                    $object->active     = $row->getActive();
                    
                    $returnArray[] = $object;
    
                }
            }

            return $returnArray;
        
        
        } catch (Exception $e) {
        
        }
    }
    
    public function cancelCard ($cardId , $userId ) {
        try {
    
            $cardId = $this->_db->quote($cardId);
            
            
            $data = array ('active' => 0 , 'modified_on' =>  new \Zend_Db_Expr('NOW()'));
            
            $where = array();
            $where[] = "id = $cardId";
            $where[] = "user_id = $userId";
            $result =  $this->_db->update('asda_cards', $data, $where);
            
            if ($result) {

                return true;

            } else {
                return $addressAdd = (object) array(
                        'error'    => 1,
                        'cardMessage' =>'Update failed'
                );
            }
    
        } catch (Exception $e) {
    
        }
    }
    
    public function validateCard ( $cardId , $userId = null ) {
        try {

            $cardObject = $this->getCardDetails($cardId);

            if (is_object($cardObject)) {

                if ( !empty ($userId) && $userId != $cardObject->user_id ) {
                    return false;
                }

                return $cardObject;
            }
            return false;
    
    
        } catch (Exception $e) {
    
        }
    }
    
}
