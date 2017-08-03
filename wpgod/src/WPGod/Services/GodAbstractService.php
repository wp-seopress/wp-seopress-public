<?php
namespace WPGodWpseopress\Services;

use WPGodWpseopress\Models\ServiceInterface;
use WPGodWpseopress\Models\HelperInterface;

/**
 * @version 2.0.0
 * @since 2.0.0
 * 
 * @author Thomas DENEULIN <contact@wp-god.com> 
 */
abstract class GodAbstractService
{

    protected $services;

    protected $helpers;

    public function __construct($services = array(), $helpers = array()){
        $this->setObjects($services);
        $this->setObjects($helpers);
    }

    public function setObjects($objects){

      foreach ($objects as $key => $value) {
            $nameClass = join('', array_slice(explode('\\', get_class($value)), -1));

            if($value instanceOf ServiceInterface){
                $this->services[$nameClass] = $value;
            }
            else if($value instanceOf HelperInterface){
                $this->helpers[$nameClass] = $value;   
            }
        }

        return $this;
    }

    public function getServices(){
        return $this->services;
    }

   
    public function getObject($key, $type = "service"){
        if($type === "service"){
            if(!empty($this->services) && array_key_exists($key, $this->services) && $this->services[$key] instanceOf ServiceInterface){
                return $this->services[$key];
            }
        }
        else if($type === "helper"){
            if(!empty($this->helpers) && array_key_exists($key, $this->helpers) && $this->helpers[$key] instanceOf HelperInterface){
                return $this->helpers[$key];
            }
        }

        return null;
    }

    public function getService($key){
        return $this->getObject($key);
    }

    public function getHelper($key){
        return $this->getObject($key, "helper");
    }



}