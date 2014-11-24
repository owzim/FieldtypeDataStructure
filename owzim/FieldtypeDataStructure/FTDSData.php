<?php
namespace owzim\FieldtypeDataStructure;

class FTDSData extends \WireData {

	public $toStringString = '';

    public function __toString() {
        return $this->toStringString;
    }
}
