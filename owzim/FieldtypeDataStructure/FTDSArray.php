<?php
namespace owzim\FieldtypeDataStructure;

class FTDSArray extends \WireArray {
    
    public $toStringString = '';
    
    public function __toString() {
        $c = count($this);
        return "$this->toStringString ($c)";
    }

    public function isValidItem($item) {
    	return $item instanceof FTDSData || $item instanceof stdClass;
    }
}
