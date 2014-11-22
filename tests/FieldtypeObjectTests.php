<?php

use \owzim\FieldtypeObject\FTO;
use \owzim\FieldtypeObject\Vendor\Spyc;

require_once(__DIR__ . '/../owzim/FieldtypeObject/Autoloader.php');
spl_autoload_register('\owzim\FieldtypeObject\Autoloader::autoload');

class FieldtypeObjectTests extends \TestFest\TestFestSuite {

    protected static function T($name) {
        return "\\owzim\\FieldtypeObject\\{$name}";
    }

    function init() {
        $this->src = __DIR__ . '/src';
    }

    function getSrc($filename) {
        return file_get_contents("$this->src/{$filename}");
    }

    function caseDataTypes() {

        $people = $this->getSrc('people.yaml');
        $yamlPeopleWire = FTO::parseInput($people, FTO::INPUT_TYPE_YAML, FTO::OUTPUT_AS_WIRE_DATA, 'People');
        $this->assertInstanceOf($yamlPeopleWire, self::T('FTOArray'));
        $this->assertInstanceOf($yamlPeopleWire[0], self::T('FTOData'));
        $this->assertSame((string) $yamlPeopleWire, 'People (2)');

        $yamlPeopleAssoc = FTO::parseInput($people, FTO::INPUT_TYPE_YAML, FTO::OUTPUT_AS_ASSOC);
        $this->assertArray($yamlPeopleAssoc);
        $this->assertArray($yamlPeopleAssoc[0]);

        $yamlPeopleObject = FTO::parseInput($people, FTO::INPUT_TYPE_YAML, FTO::OUTPUT_AS_OBJECT);
        $this->assertArray($yamlPeopleObject);
        $this->assertObject($yamlPeopleObject[0]);

        $people = $this->getSrc('faulty-people.yaml');
        $yamlPeopleWire = FTO::parseInput($people, FTO::INPUT_TYPE_YAML, FTO::OUTPUT_AS_WIRE_DATA, 'people');
        $this->assertArray($yamlPeopleWire);
        
        $matrix = $this->getSrc('matrix.txt');
        $matrixWire = FTO::parseInput($matrix, FTO::INPUT_TYPE_MATRIX, FTO::OUTPUT_AS_WIRE_DATA, 'people');
        $this->assertArray($matrixWire);
        $this->assertIdentical(count($matrixWire), 4);
        $this->assertIdentical(count($matrixWire[0]), 4);
        ChromePhp::log('$matrixWire', $matrixWire);
        
        $comma = $this->getSrc('comma-separated.txt');
        $commaWire = FTO::parseInput($comma, FTO::INPUT_TYPE_COMMA_SEPARATED, FTO::OUTPUT_AS_WIRE_DATA, 'people');
        $this->assertArray($commaWire);
        $this->assertIdentical(count($commaWire), 4);
        ChromePhp::log('$commaWire', $commaWire);
        
        $line = $this->getSrc('line-separated.txt');
        $lineWire = FTO::parseInput($line, FTO::INPUT_TYPE_LINE_SEPARATED, FTO::OUTPUT_AS_WIRE_DATA, 'people');
        $this->assertArray($lineWire);
        $this->assertIdentical(count($lineWire), 5);
        ChromePhp::log('$lineWire', $lineWire);

    }
}
