<?php

namespace WPGodWpseopress\Helpers;

use WPGodWpseopress\Models\HelperInterface;

/**
 * GodError
 *
 * @author Thomas DENEULIN <contact@wp-god.com>
 * @version 2.0.0
 * @since 1.0.0
 */
class GodError implements HelperInterface {

    protected $errors; 

    protected $froms; 

    public function __construct(){
        $this->errors = apply_filters("_god_list_errors",
            array(
                0     => 'E_UNKNOWN',
                1     => 'E_ERROR',
                2     => 'E_WARNING',
                4     => 'E_PARSE',
                8     => 'E_NOTICE',
                16    => 'E_CORE_ERROR',
                32    => 'E_CORE_WARNING', 
                64    => 'E_COMPILE_ERROR',
                128   => 'E_COMPILE_WARNING', 
                256   => 'E_USER_ERROR',
                512   => 'E_USER_WARNING', 
                1024  => 'E_USER_NOTICE', 
                2048  => 'E_STRICT', 
                4096  => 'E_RECOVERABLE_ERROR',
                8192  => 'E_DEPRECATED', 
                16384 => 'E_USER_DEPRECATED',
                32767 => 'E_ALL'
            )
        );        
       
    }

    public function getErrors(){
        return $this->errors;        
    }

    public function getFroms(){
        return $this->froms;        
    }


    public function getStringError($errno){
        $errors = self::getErrors();

        if(array_key_exists($errno, $errors)){
            return $errors[$errno];
        }

        return 'E_UNKNOWN'; 
    }
    
}









