<?php

use \owzim\FieldtypeDataStructure\FTDS;
use \owzim\FieldtypeDataStructure\Vendor\Spyc;

require_once(__DIR__ . '/../owzim/FieldtypeDataStructure/Autoloader.php');
spl_autoload_register('\owzim\FieldtypeDataStructure\Autoloader::autoload');

class FieldtypeDataStructureTests extends \TestFest\TestFestSuite {

    protected static function T($name) {
        return "\\owzim\\FieldtypeDataStructure\\{$name}";
    }

    function init() {
        $this->src = __DIR__ . '/src';
    }

    function getSrc($filename) {
        return file_get_contents("$this->src/{$filename}");
    }

    function caseDataTypes() {
        
        $this->newTest('YAML WireArray');
        
            $people = $this->getSrc('people.yaml');
            $yamlPeopleWire = FTDS::parseInput($people, FTDS::INPUT_TYPE_YAML, FTDS::OUTPUT_AS_WIRE_DATA, 'People');
            $this->assertInstanceOf($yamlPeopleWire, self::T('FTDSArray'));
            $this->assertInstanceOf($yamlPeopleWire[0], self::T('FTDSData'));
            $this->assertSame((string) $yamlPeopleWire, 'People (2)');

            $yamlPeopleAssoc = FTDS::parseInput($people, FTDS::INPUT_TYPE_YAML, FTDS::OUTPUT_AS_ASSOC);
            $this->assertArray($yamlPeopleAssoc);
            $this->assertArray($yamlPeopleAssoc[0]);

            $yamlPeopleObject = FTDS::parseInput($people, FTDS::INPUT_TYPE_YAML, FTDS::OUTPUT_AS_OBJECT);
            $this->assertArray($yamlPeopleObject);
            $this->assertObject($yamlPeopleObject[0]);
        
        $this->newTest('Mixed YAML Array');
        
            $people = $this->getSrc('faulty-people.yaml');
            $yamlPeopleWire = FTDS::parseInput($people, FTDS::INPUT_TYPE_YAML, FTDS::OUTPUT_AS_WIRE_DATA, 'people');
            $this->assertArray($yamlPeopleWire);
        
        $this->newTest('Matrix');
        
            $matrix = $this->getSrc('matrix.txt');
            $matrixWire = FTDS::parseInput($matrix, FTDS::INPUT_TYPE_MATRIX, FTDS::OUTPUT_AS_WIRE_DATA, 'people');
            $this->assertArray($matrixWire);
            $this->assertIdentical(count($matrixWire), 4);
            $this->assertIdentical(count($matrixWire[0]), 4);
        
        $this->newTest('Matrix Object');
        
            $matrixObject = $this->getSrc('matrix-object.txt');
            $matrixObjectWire = FTDS::parseInput(
                $matrixObject,
                FTDS::INPUT_TYPE_MATRIX_OBJECT,
                FTDS::OUTPUT_AS_WIRE_DATA,
                'people'
            );
            
            $this->assertInstanceOf($matrixObjectWire, self::T('FTDSArray'));
            $this->assertIdentical(count($matrixObjectWire), 3);
            $this->assertInstanceOf($matrixObjectWire[0], self::T('FTDSData'));
            $this->assertIdentical($matrixObjectWire[0]->name, 'Neo');
            $this->assertIdentical($matrixObjectWire->implode(',', 'name'), 'Neo,Trinity,Morpheus');

        $this->newTest('Comma separated');
        
            $comma = $this->getSrc('comma-separated.txt');
            $commaWire = FTDS::parseInput($comma, FTDS::INPUT_TYPE_COMMA_SEPARATED, FTDS::OUTPUT_AS_WIRE_DATA, 'people');
            $this->assertArray($commaWire);
            $this->assertIdentical(count($commaWire), 4);
        
        $this->newTest('Line-break separated');
        
            $line = $this->getSrc('line-separated.txt');
            $lineWire = FTDS::parseInput($line, FTDS::INPUT_TYPE_LINE_SEPARATED, FTDS::OUTPUT_AS_WIRE_DATA, 'people');
            $this->assertArray($lineWire);
            $this->assertIdentical(count($lineWire), 5);


    }
    
    function caseFieldSettings() {
        $name = 'fieldtypeObjectTest';
        
        $f = new Field();
        $f->type = 'FieldtypeDataStructure';
        $f->name = $name;
        $f->save();
        
        $this->newTest('Defaults');
        
            $this->assertIdentical($f->inputType, FTDS::DEFAULT_INPUT_TYPE, 'inputType DEFAULT');
            $this->assertIdentical($f->fontFamily, FieldtypeDataStructure::DEFAULT_FONT_FAMILY, 'fontFamily DEFAULT');
            $this->assertIdentical($f->outputAs, FTDS::DEFAULT_OUTPUT_AS, 'outputAs DEFAULT');
        
        $this->newTest('inputType');
        
            $f->inputType = FTDS::INPUT_TYPE_YAML;
            $f->save();
            $fs = $this->fields->get($name);
            $this->assertIdentical($fs->inputType, FTDS::INPUT_TYPE_YAML, 'inputType YAML');
            
            $f->inputType = FTDS::INPUT_TYPE_MATRIX;
            $f->save();
            $fs = $this->fields->get($name);
            $this->assertIdentical($fs->inputType, FTDS::INPUT_TYPE_MATRIX, 'inputType MATRIX');
            
            $f->inputType = FTDS::INPUT_TYPE_COMMA_SEPARATED;
            $f->save();
            $fs = $this->fields->get($name);
            $this->assertIdentical($fs->inputType, FTDS::INPUT_TYPE_COMMA_SEPARATED, 'inputType COMMA_SEPARATED');
            
            $f->inputType = FTDS::INPUT_TYPE_LINE_SEPARATED;
            $f->save();
            $fs = $this->fields->get($name);
            $this->assertIdentical($fs->inputType, FTDS::INPUT_TYPE_LINE_SEPARATED, 'inputType LINE_SEPARATED');
            
            $f->inputType = FTDS::INPUT_TYPE_JSON;
            $f->save();
            $fs = $this->fields->get($name);
            $this->assertIdentical($fs->inputType, FTDS::INPUT_TYPE_JSON, 'inputType JSON');
            
        $this->newTest('outputAs');
            
            $f->outputAs = FTDS::OUTPUT_AS_ASSOC;
            $f->save();
            $fs = $this->fields->get($name);
            $this->assertIdentical($fs->outputAs, FTDS::OUTPUT_AS_ASSOC, 'outputAs ASSOC');
            
            $f->outputAs = FTDS::OUTPUT_AS_OBJECT;
            $f->save();
            $fs = $this->fields->get($name);
            $this->assertIdentical($fs->outputAs, FTDS::OUTPUT_AS_OBJECT, 'outputAs OBJECT');
            
            $f->outputAs = FTDS::OUTPUT_AS_WIRE_DATA;
            $f->save();
            $fs = $this->fields->get($name);
            $this->assertIdentical($fs->outputAs, FTDS::OUTPUT_AS_WIRE_DATA, 'outputAs WIRE_DATA');

        $this->fields->delete($f);
    }
}
