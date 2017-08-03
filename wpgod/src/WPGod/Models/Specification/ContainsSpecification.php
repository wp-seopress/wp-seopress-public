<?php

namespace WPGodWpseopress\Models\Specification;

use WPGodWpseopress\Models\Specification\AbstractSpecification;

class ContainsSpecification extends AbstractSpecification
{
	public function __construct($string){
		$this->string = $string;
	}

    public function isSatisfiedBy($item){	
    	$pos = strpos($item, $this->string);

		if ($pos === false) {
        	return false;
		} else {
		    return true;
		}
    }
}