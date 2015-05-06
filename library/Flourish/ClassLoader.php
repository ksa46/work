<?php
/**
 * Automatically includes classes
 *
 * @throws Exception
 *
 * @param  string $class_name  Name of the class to load
 * @return void
 */

class FlourishLoader {

    public static function autoload($class_name) {
        $file = dirname(__FILE__) . '/lib/' . $class_name . '.php';
        if (@include($file)) {
            return;
        }

        $file = dirname(__FILE__) . '/../app/Entity/' . $class_name . '.php';
        if (@include($file)) {
            return;
        }
    }

}

spl_autoload_register(array('FlourishLoader', 'autoload'));
