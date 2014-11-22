<?php
namespace owzim\FieldtypeObject;

class FTOArray extends \WireArray {
    
    public $toStringString = '';
    
    public function __toString() {
        $c = count($this);
        return "$this->toStringString ($c)";
    }

    public function isValidItem($item) {
    	return $item instanceof FTOData || $item instanceof stdClass;
    }
}
