<?php
namespace owzim\FieldtypeYaml;

class FTYArray extends \WireArray {

    public function __toString() {
        return '';
    }

    public function isValidItem($item) {
    	return $item instanceof FTYData || $item instanceof stdClass;
    }
}
