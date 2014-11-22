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
        $yamlPeopleWire = FTY::parseYAML($people, FTY::PARSE_AS_WIRE_DATA, 'people');
        $this->assertInstanceOf($yamlPeopleWire, self::T('FTYArray'));
        $this->assertInstanceOf($yamlPeopleWire[0], self::T('FTYData'));
        $this->assertTrue($yamlPeopleWire == 'people');

        $yamlPeopleAssoc = FTY::parseYAML($people, FTY::PARSE_AS_ASSOC);
        $this->assertArray($yamlPeopleAssoc);
        $this->assertArray($yamlPeopleAssoc[0]);

        $yamlPeopleObject = FTY::parseYAML($people, FTY::PARSE_AS_OBJECT);
        $this->assertArray($yamlPeopleObject);
        $this->assertObject($yamlPeopleObject[0]);

        $people = $this->getSrc('faulty-people.yaml');
        $yamlPeopleWire = FTY::parseYAML($people, FTY::PARSE_AS_WIRE_DATA, 'people');
        $this->assertArray($yamlPeopleWire);
    }
}
