<?php
namespace owzim\FieldtypeYaml;

class FTYData extends \WireData {

	public $toStringString = '';

    public function __toString() {
        return $this->toStringString;
    }
}
