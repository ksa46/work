<?php
class App_Repository_FlourishLoader {

    public static function autoload($class_name) {
        $file = realpath(dirname(__FILE__) . '/../../Flourish/lib/') . '/' . $class_name . '.php';
        if (file_exists($file)) {

            if (@include($file)) {

                return;
            }
           
        }

        $file = realpath(dirname(__FILE__) . '/../Entity/') . '/' . $class_name . '.php';
        if (file_exists($file)) {
            if (@include($file)) {
                return;
            }
        }
    }

}

$ccc = spl_autoload_register(array('App_Repository_FlourishLoader', 'autoload'));


class App_Repository {

    protected $_dbConnection = null;

    public function __construct() {
    }

    public function initDB($dbConfig) {
        try {

            $db = new fDatabase(
                                $dbConfig['driver'], $dbConfig['dbname'],
                                $dbConfig['user'], $dbConfig['password'],
                                $dbConfig['host'], $dbConfig['port']
                        );
            $db->connect();

            // This makes the database connection
            //
            fORMDatabase::attach($db);

            // This maps the physical database to the object model
            //
            $this->_setupDBMapping();

            // This sets up DB lookup tables if necessary
            //
            $this->_setupDBTypes();

            $this->_dbConnection = $db;

            return $this->_dbConnection;
        } catch (fAuthorizationException $e) {
            throw new Exception('Database initialisation error (_initDB): fAuthorizationException');
        } catch (fConnectivityException $e) {
            throw new Exception('Database initialisation error (_initDB): fConnectivityException');
        } catch (Exception $e) {
            throw $e;
        }

        return false;
    }

    public function getDBConnection() {
        return $this->_dbConnection;
    }

    protected function _setupDBMapping() {
        // App tables
        
        fORM::mapClassToTable('App_Entity_Cards'           , 'asda_cards');
        fORM::mapClassToTable('App_Entity_User'            , 'asda_user');
        fORM::mapClassToTable('App_Entity_NewsSignup'      , 'asda_news_signup');
        fORM::mapClassToTable('App_Entity_PasswordReset'   , 'asda_password_reset');
        fORM::mapClassToTable('App_Entity_Category'        , 'asda_category');
        fORM::mapClassToTable('App_Entity_CardType'        , 'asda_card_type');
        fORM::mapClassToTable('App_Entity_Address'         , 'asda_address');

    }

    protected function _setupDBTypes() {
        /*
         * TODO:: 08/01/2012
         *
         * Need setup DB lookup tables
         *
         */
    }

}
