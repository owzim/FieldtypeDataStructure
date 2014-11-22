<?php
namespace owzim\FieldtypeObject;

class FTOData extends \WireData {

	public $toStringString = '';

    public function __toString() {
        return $this->toStringString;
    }
}
