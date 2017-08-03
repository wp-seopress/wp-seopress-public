<?php

namespace WPGodWpseopress\Services;

use WPGodWpseopress\Models\ServiceInterface;
use WPGodWpseopress\Models\Specification\OrX;
use WPGodWpseopress\Models\Specification\AndX;
use WPGodWpseopress\Models\Specification\NotX;
use WPGodWpseopress\Models\Specification\ContainsSpecification;
use WPGodWpseopress\Models\Specification\EqualsSpecification;


/**
 * GodFilesRules
 *
 * @author Thomas DENEULIN <contact@wp-god.com>
 * @version 1.0.0
 * @since 1.0.0
 */
class GodFilesRules  implements ServiceInterface {
    public function __construct($params = array()){
        $this->rulesIgnore = (isset($params["rules_ignore"])) ? $params["rules_ignore"] : array();

        $this->typeErrorRules = array(
            "code_error",
            "file_dir"
        );  

        $this->conditionTest = array(
            "equals",   
            "contains", 
            "not-equals",
            "not-contains"
        );
    }

    public function getTypeErrorRules(){
        return $this->typeErrorRules;
    }

    public function getConditionTest(){
        return $this->conditionTest;
    }

    public function isInformationIgnore($type, $valueIgnore){
        $arraySpec = array();
        $result    = false;

        if (!in_array($type, $this->typeErrorRules)) {
            return false;
        }

        if(!empty($this->rulesIgnore)){
            foreach ($this->rulesIgnore as $keyOr => $valueOr) {
                foreach ($valueOr as $keyAnd => $valueAnd) {
                    if($valueAnd['type_error'] == $type){
                        $conditionTest = "";
                        $value         = "";

                        switch ($type) {
                            case 'file_dir':
                                $conditionTest = "contains";
                                $value         = $valueAnd["file"];
                                break;
                            case 'code_error':
                                $conditionTest = "equals";
                                $value         = $valueAnd["code_error"];
                                break;
                        }

                        $arraySpec[$keyOr][] = $this->getSpecificationFromConditionTest($conditionTest, $value);
                    }
                }
            }
        }


        if(!empty($arraySpec)){
            $allSpecs = null;
            if(count($arraySpec) > 1){
                foreach ($arraySpec as $key => $value) {
                    if (array_key_exists($key+1, $arraySpec)) {
                        $allSpecs = ($allSpecs === null) ? new OrX($this->constructAndSpecification($value), $this->constructAndSpecification($arraySpec[$key+1])) : new OrX($allSpecs, $this->constructAndSpecification($arraySpec[$key+1]));
                    }
                }
            }
            else{
                $arraySpec = array_values($arraySpec);
                $allSpecs  = $this->constructAndSpecification($arraySpec[0]);
            }

            if ($allSpecs !== null) {
                $result = $allSpecs->isSatisfiedBy($valueIgnore);
            }
        }

        return $result;


    }

    private function constructAndSpecification($andSpecs){
        $andSpecification = null;
        if(!empty($andSpecs) && count($andSpecs) === 1){
            return $andSpecs[0];
        }
        else{
            foreach ($andSpecs as $key => $value) {
                if (array_key_exists($key+1, $andSpecs)) {
                    $andSpecification = ($andSpecification === null) ? new AndX($value, $andSpecs[$key+1]) : new AndX($andSpecification, $andSpecs[$key+1]);       
                }
            }
        }

        return $andSpecification;
    }


    public function getSpecificationFromConditionTest($conditionTest, $value){
        $stringChoice = new ContainsSpecification($conditionTest);
        $spec         = "";

        if($stringChoice->isSatisfiedBy("not")){
            if($stringChoice->isSatisfiedBy("equals")){
                $spec = new NotX(new EqualsSpecification($value));
            }
            else{
                $spec = new NotX(new ContainsSpecification($value));
            }
        }
        else{
            if($stringChoice->isSatisfiedBy("equals")){
                $spec = new EqualsSpecification($value);
            }
            else{
                $spec = new ContainsSpecification($value);
            }
        }

        return $spec;
    }

    

}









