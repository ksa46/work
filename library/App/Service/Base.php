<?php
/**
 * @version JrodanMedia 3.0
 * @author mohammad.mohsin <mohammad.mohsin@jordanmedia.co.uk>
 * @copyright Copyright (c) 2012-2013, Jordan Media Limited
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */


namespace App\Service;

class Base {

    const GLOBAL_ERROR_NAMESPACE = 'global';

    // Error Stack Array
    static protected $_errorStack = array(self::GLOBAL_ERROR_NAMESPACE => array());
    static protected $_errorCodes = array(
        '001' => 'Cannot read ::read',
        '002' => 'Cannot read ::update',
        '101' => 'Permission denied to read',
        '102' => 'Permission denied to update',
        '103' => 'Permission denied to delete'
    );

    /* Do not remove this line */
    public function __construct() {
    }

    /* Do not remove this line */
    public function __destruct() {
    }

    
}
