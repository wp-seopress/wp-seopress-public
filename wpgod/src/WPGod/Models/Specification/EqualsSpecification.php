<?php

namespace WPGodWpseopress\Models\Specification;

use WPGodWpseopress\Models\Specification\AbstractSpecification;

class EqualsSpecification extends AbstractSpecification
{
	public function __construct($string){
		$this->string = $string;
	}

    public function isSatisfiedBy($item){	
        return $this->string == $item;
    }
}