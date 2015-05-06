<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    public function _initConfig()
    {
        Zend_Registry::set('config', $this->getOptions());
        $salt = '$6$rounds=5000$';
        
        if (!(defined('PASSWORD_SALT'))) {
            define('PASSWORD_SALT', $salt);
        }
        
        if (!(defined('DOMAIN_NAME'))) {
            define("DOMAIN_NAME", "http://local.asdaone.com/");
        }
        
        if (!defined('DIRECTORY_PATH')) {
            define('DIRECTORY_PATH' , $_SERVER["DOCUMENT_ROOT"]);
        }
        date_default_timezone_set('Europe/London');
        Zend_Session::start();
    }
    
    public function _initAutoloaderNamespaces()
    {
        require_once APPLICATION_PATH .
            '/../library/Doctrine/Common/ClassLoader.php';

        require_once APPLICATION_PATH .
            '/../library/Symfony/Component/Di/sfServiceContainerAutoloader.php';

        sfServiceContainerAutoloader::register();
        $autoloader = \Zend_Loader_Autoloader::getInstance();

        $fmmAutoloader = new \Doctrine\Common\ClassLoader('Bisna');
        $autoloader->pushAutoloader(array($fmmAutoloader, 'loadClass'), 'Bisna');
        
        $fmmAutoloader = new \Doctrine\Common\ClassLoader('App');
        $autoloader->pushAutoloader(array($fmmAutoloader, 'loadClass'), 'App');

        $fmmAutoloader = new \Doctrine\Common\ClassLoader('Boilerplate');
        $autoloader->pushAutoloader(array($fmmAutoloader, 'loadClass'), 'Boilerplate');

        $fmmAutoloader = new \Doctrine\Common\ClassLoader('Doctrine\DBAL\Migrations');
        $autoloader->pushAutoloader(array($fmmAutoloader, 'loadClass'), 'Doctrine\DBAL\Migrations');
        
        /* This loads the Flourish library loader */
        require_once APPLICATION_PATH . '/../library/App/Repository/FlourishLoader.php';
    }

    public function _initModuleLayout()
    {
        $front = Zend_Controller_Front::getInstance();

        $front->registerPlugin(
            new Boilerplate_Controller_Plugin_ModuleLayout()
        );
        
        $front->setParam('prefixDefaultModule', true);
        $eh = new Zend_Controller_Plugin_ErrorHandler();
        $front = Zend_Controller_Front::getInstance()->registerPlugin($eh);
    }

    public function _initServices()
    {
        $sc = new sfServiceContainerBuilder();
        $loader = new sfServiceContainerLoaderFileXml($sc);
        $loader->load(APPLICATION_PATH . "/configs/services.xml");
        Zend_Registry::set('sc', $sc);
    }

    public function _initLocale()
    {
        $config = $this->getOptions();
        
        try{
            $locale = new Zend_Locale(Zend_Locale::BROWSER);
        } catch (Zend_Locale_Exception $e) {
            $locale = new Zend_Locale($config['resources']['locale']['default']);
        }

        Zend_Registry::set('Zend_Locale', $locale);

        $translator = new Zend_Translate(
            array(
                'adapter' => 'Csv',
                'content' => APPLICATION_PATH . '/../data/lang/',
                'scan' => Zend_Translate::LOCALE_DIRECTORY,
                'delimiter' => ',',
                'disableNotices' => true,
            )
        );

        if (!$translator->isAvailable($locale->getLanguage()))
            $translator->setLocale($config['resources']['locale']['default']);

        Zend_Registry::set('Zend_Translate', $translator);
        Zend_Form::setDefaultTranslator($translator);
    }
/*
    public function _initElasticSearch()
    {
        $es = new Elastica_Client();
        Zend_Registry::set('es', $es);
    }
    */
    public function _initDB() {
        $config = $this->getOptions();
        $configs = $config['resources']['doctrine']['dbal']['connections']['default']['parameters'];
    
        $db = Zend_Db :: factory($configs['driver'], array(
                'host'          => $configs['host'],
                'username'      => $configs['user'],
                'password'      => $configs['password'],
                'dbname'        => $configs['dbname'],
        ));
    //echo $configs['driver'];exit;
        Zend_Registry::set('db', $db);
        Zend_Db_Table::setDefaultAdapter($db);
        
        $dbConfig = array ('dbname' => $configs['dbname'] , 'password' => $configs['password'] , 'user' => $configs['user']
                            , 'port' => $configs['port'] , 'host' => $configs['host'] , 'driver' => 'mysql');
        $db = new App_Repository();
        if (! $db->initDB($dbConfig)) {
            throw new Exception('Database initialisation error (_initDB)');
        }
        
        Zend_Registry::set('florishDb', $db);
    }
    
    public function _initSession() {
        $this->bootstrap('db');
        $config = array();
    }

}