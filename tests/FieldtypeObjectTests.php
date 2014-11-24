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
        
        $this->newTest('YAML WireArray');
        
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
        
        $this->newTest('Mixed YAML Array');
        
            $people = $this->getSrc('faulty-people.yaml');
            $yamlPeopleWire = FTO::parseInput($people, FTO::INPUT_TYPE_YAML, FTO::OUTPUT_AS_WIRE_DATA, 'people');
            $this->assertArray($yamlPeopleWire);
        
        $this->newTest('Matrix');
        
            $matrix = $this->getSrc('matrix.txt');
            $matrixWire = FTO::parseInput($matrix, FTO::INPUT_TYPE_MATRIX, FTO::OUTPUT_AS_WIRE_DATA, 'people');
            $this->assertArray($matrixWire);
            $this->assertIdentical(count($matrixWire), 4);
            $this->assertIdentical(count($matrixWire[0]), 4);
        
        $this->newTest('Matrix Object');
        
            $matrixObject = $this->getSrc('matrix-object.txt');
            $matrixObjectWire = FTO::parseInput(
                $matrixObject,
                FTO::INPUT_TYPE_MATRIX_OBJECT,
                FTO::OUTPUT_AS_WIRE_DATA,
                'people'
            );
            
            $this->assertInstanceOf($matrixObjectWire, self::T('FTOArray'));
            $this->assertIdentical(count($matrixObjectWire), 3);
            $this->assertInstanceOf($matrixObjectWire[0], self::T('FTOData'));
            $this->assertIdentical($matrixObjectWire[0]->name, 'Neo');
            $this->assertIdentical($matrixObjectWire->implode(',', 'name'), 'Neo,Trinity,Morpheus');

        $this->newTest('Comma separated');
        
            $comma = $this->getSrc('comma-separated.txt');
            $commaWire = FTO::parseInput($comma, FTO::INPUT_TYPE_COMMA_SEPARATED, FTO::OUTPUT_AS_WIRE_DATA, 'people');
            $this->assertArray($commaWire);
            $this->assertIdentical(count($commaWire), 4);
        
        $this->newTest('Line-break separated');
        
            $line = $this->getSrc('line-separated.txt');
            $lineWire = FTO::parseInput($line, FTO::INPUT_TYPE_LINE_SEPARATED, FTO::OUTPUT_AS_WIRE_DATA, 'people');
            $this->assertArray($lineWire);
            $this->assertIdentical(count($lineWire), 5);


    }
    
    function caseFieldSettings() {
        $name = 'fieldtypeObjectTest';
        
        $f = new Field();
        $f->type = 'FieldtypeObject';
        $f->name = $name;
        $f->save();
        
        $this->newTest('Defaults');
        
            $this->assertIdentical($f->inputType, FTO::DEFAULT_INPUT_TYPE, 'inputType DEFAULT');
            $this->assertIdentical($f->fontFamily, FieldtypeObject::DEFAULT_FONT_FAMILY, 'fontFamily DEFAULT');
            $this->assertIdentical($f->outputAs, FTO::DEFAULT_OUTPUT_AS, 'outputAs DEFAULT');
        
        $this->newTest('inputType');
        
            $f->inputType = FTO::INPUT_TYPE_YAML;
            $f->save();
            $fs = $this->fields->get($name);
            $this->assertIdentical($fs->inputType, FTO::INPUT_TYPE_YAML, 'inputType YAML');
            
            $f->inputType = FTO::INPUT_TYPE_MATRIX;
            $f->save();
            $fs = $this->fields->get($name);
            $this->assertIdentical($fs->inputType, FTO::INPUT_TYPE_MATRIX, 'inputType MATRIX');
            
            $f->inputType = FTO::INPUT_TYPE_COMMA_SEPARATED;
            $f->save();
            $fs = $this->fields->get($name);
            $this->assertIdentical($fs->inputType, FTO::INPUT_TYPE_COMMA_SEPARATED, 'inputType COMMA_SEPARATED');
            
            $f->inputType = FTO::INPUT_TYPE_LINE_SEPARATED;
            $f->save();
            $fs = $this->fields->get($name);
            $this->assertIdentical($fs->inputType, FTO::INPUT_TYPE_LINE_SEPARATED, 'inputType LINE_SEPARATED');
            
            $f->inputType = FTO::INPUT_TYPE_JSON;
            $f->save();
            $fs = $this->fields->get($name);
            $this->assertIdentical($fs->inputType, FTO::INPUT_TYPE_JSON, 'inputType JSON');
            
        $this->newTest('outputAs');
            
            $f->outputAs = FTO::OUTPUT_AS_ASSOC;
            $f->save();
            $fs = $this->fields->get($name);
            $this->assertIdentical($fs->outputAs, FTO::OUTPUT_AS_ASSOC, 'outputAs ASSOC');
            
            $f->outputAs = FTO::OUTPUT_AS_OBJECT;
            $f->save();
            $fs = $this->fields->get($name);
            $this->assertIdentical($fs->outputAs, FTO::OUTPUT_AS_OBJECT, 'outputAs OBJECT');
            
            $f->outputAs = FTO::OUTPUT_AS_WIRE_DATA;
            $f->save();
            $fs = $this->fields->get($name);
            $this->assertIdentical($fs->outputAs, FTO::OUTPUT_AS_WIRE_DATA, 'outputAs WIRE_DATA');

        $this->fields->delete($f);
    }
}
