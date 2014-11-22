<?php

use \owzim\FieldtypeYaml\FTY;
use \owzim\FieldtypeYaml\Vendor\Spyc;

require_once(__DIR__ . '/../owzim/FieldtypeYaml/Autoloader.php');
spl_autoload_register('\owzim\FieldtypeYaml\Autoloader::autoload');

class FieldtypeYamlTests extends \TestFest\TestFestSuite {

    protected static function T($name) {
        return "\\owzim\\FieldtypeYaml\\{$name}";
    }

    function init() {
        $this->src = __DIR__ . '/src';
    }

    function getSrc($filename) {
        return file_get_contents("$this->src/{$filename}");
    }

    function caseDataTypes() {

        $people = $this->getSrc('people.yaml');
        $yamlPeopleWire = FTY::parseInput($people, FTY::INPUT_TYPE_YAML, FTY::OUTPUT_AS_WIRE_DATA, 'People');
        $this->assertInstanceOf($yamlPeopleWire, self::T('FTYArray'));
        $this->assertInstanceOf($yamlPeopleWire[0], self::T('FTYData'));
        $this->assertSame((string) $yamlPeopleWire, 'People (2)');

        $yamlPeopleAssoc = FTY::parseInput($people, FTY::INPUT_TYPE_YAML, FTY::OUTPUT_AS_ASSOC);
        $this->assertArray($yamlPeopleAssoc);
        $this->assertArray($yamlPeopleAssoc[0]);

        $yamlPeopleObject = FTY::parseInput($people, FTY::INPUT_TYPE_YAML, FTY::OUTPUT_AS_OBJECT);
        $this->assertArray($yamlPeopleObject);
        $this->assertObject($yamlPeopleObject[0]);

        $people = $this->getSrc('faulty-people.yaml');
        $yamlPeopleWire = FTY::parseInput($people, FTY::INPUT_TYPE_YAML, FTY::OUTPUT_AS_WIRE_DATA, 'people');
        $this->assertArray($yamlPeopleWire);
        
        $matrix = $this->getSrc('matrix.txt');
        $matrixWire = FTY::parseInput($matrix, FTY::INPUT_TYPE_MATRIX, FTY::OUTPUT_AS_WIRE_DATA, 'people');
        $this->assertArray($matrixWire);
        $this->assertIdentical(count($matrixWire), 4);
        $this->assertIdentical(count($matrixWire[0]), 4);
        ChromePhp::log('$matrixWire', $matrixWire);
        
        $comma = $this->getSrc('comma-separated.txt');
        $commaWire = FTY::parseInput($comma, FTY::INPUT_TYPE_COMMA_SEPARATED, FTY::OUTPUT_AS_WIRE_DATA, 'people');
        $this->assertArray($commaWire);
        $this->assertIdentical(count($commaWire), 4);
        ChromePhp::log('$commaWire', $commaWire);
        
        $line = $this->getSrc('line-separated.txt');
        $lineWire = FTY::parseInput($line, FTY::INPUT_TYPE_LINE_SEPARATED, FTY::OUTPUT_AS_WIRE_DATA, 'people');
        $this->assertArray($lineWire);
        $this->assertIdentical(count($lineWire), 5);
        ChromePhp::log('$lineWire', $lineWire);

    }
}
