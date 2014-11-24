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
            $yamlPeopleWire = FTDS::parseInput($people, array(
                'inputType' => FTDS::INPUT_TYPE_YAML,
                'outputAs' => FTDS::OUTPUT_AS_WIRE_DATA,
                'toStringString' => 'People',
            ));
            $this->assertInstanceOf($yamlPeopleWire, self::T('FTDSArray'));
            $this->assertInstanceOf($yamlPeopleWire[0], self::T('FTDSData'));
            $this->assertSame((string) $yamlPeopleWire, 'People (2)');

            $yamlPeopleAssoc = FTDS::parseInput($people, array(
                'inputType' => FTDS::INPUT_TYPE_YAML,
                'outputAs' => FTDS::OUTPUT_AS_ASSOC,
            ));


            $this->assertArray($yamlPeopleAssoc);
            $this->assertArray($yamlPeopleAssoc[0]);

            $yamlPeopleObject = FTDS::parseInput($people, array(
                'inputType' => FTDS::INPUT_TYPE_YAML,
                'outputAs' => FTDS::OUTPUT_AS_OBJECT,
            ));

            $this->assertArray($yamlPeopleObject);
            $this->assertObject($yamlPeopleObject[0]);

        $this->newTest('Mixed YAML Array');

            $people = $this->getSrc('faulty-people.yaml');
            $yamlPeopleWire = FTDS::parseInput($people, array(
                'inputType' => FTDS::INPUT_TYPE_YAML,
                'outputAs' => FTDS::OUTPUT_AS_WIRE_DATA,
                'toStringString' => 'People',
            ));
            $this->assertArray($yamlPeopleWire);

        $this->newTest('Comma Matrix');

            $matrix = $this->getSrc('matrix.txt');
            $matrixWire = FTDS::parseInput($matrix, array(
                'inputType' => FTDS::INPUT_TYPE_MATRIX,
                'outputAs' => FTDS::OUTPUT_AS_WIRE_DATA,
                'toStringString' => 'People',
            ));
            $this->assertArray($matrixWire);
            $this->assertIdentical(count($matrixWire), 4);
            $this->assertIdentical(count($matrixWire[0]), 4);

        $this->newTest('Pipe Matrix');

            $pipeMatrix = $this->getSrc('pipe-matrix.txt');
            $pipeMatrixWire = FTDS::parseInput($pipeMatrix, array(
                'inputType' => FTDS::INPUT_TYPE_MATRIX,
                'outputAs' => FTDS::OUTPUT_AS_WIRE_DATA,
                'delimiter' => '|',
                'toStringString' => 'People',
            ));
            $this->assertArray($pipeMatrixWire);
            $this->assertIdentical(count($pipeMatrixWire), 4);
            $this->assertIdentical(count($pipeMatrixWire[0]), 4);

        $this->newTest('Comma Matrix Object');

            $matrixObject = $this->getSrc('matrix-object.txt');
            $matrixObjectWire = FTDS::parseInput($matrixObject, array(
                'inputType' => FTDS::INPUT_TYPE_MATRIX_OBJECT,
                'outputAs' => FTDS::OUTPUT_AS_WIRE_DATA,
                'toStringString' => 'People',
            ));

            $this->assertInstanceOf($matrixObjectWire, self::T('FTDSArray'));
            $this->assertIdentical(count($matrixObjectWire), 4);
            $this->assertInstanceOf($matrixObjectWire[0], self::T('FTDSData'));
            $this->assertIdentical($matrixObjectWire[0]->name, 'Neo');
            $this->assertIdentical($matrixObjectWire->implode(',', 'name'), 'Neo,Trinity,Morpheus,Agent Smith');


        $this->newTest('Pipe Matrix Object');

            $pipeMatrixObject = $this->getSrc('pipe-matrix-object.txt');
            $pipeMatrixObjectWire = FTDS::parseInput($pipeMatrixObject, array(
                'inputType' => FTDS::INPUT_TYPE_MATRIX_OBJECT,
                'outputAs' => FTDS::OUTPUT_AS_WIRE_DATA,
                'delimiter' => '|',
                'toStringString' => 'People',
            ));

            $this->assertInstanceOf($pipeMatrixObjectWire, self::T('FTDSArray'));
            $this->assertIdentical(count($pipeMatrixObjectWire), 3);
            $this->assertInstanceOf($pipeMatrixObjectWire[0], self::T('FTDSData'));
            $this->assertIdentical($pipeMatrixObjectWire[0]->name, 'Neo');
            $this->assertIdentical($pipeMatrixObjectWire->implode(',', 'name'), 'Neo,Trinity,Morpheus');

        $this->newTest('Comma separated');

            $comma = $this->getSrc('comma-separated.txt');
            $commaWire = FTDS::parseInput($comma, array(
                'inputType' => FTDS::INPUT_TYPE_DELIMITER_SEPARATED,
                'outputAs' => FTDS::OUTPUT_AS_WIRE_DATA,
                'toStringString' => 'People',
            ));
            $this->assertArray($commaWire);
            $this->assertIdentical(count($commaWire), 4);

        $this->newTest('Pipe separated');

            $pipe = $this->getSrc('pipe-separated.txt');
            $pipeWire = FTDS::parseInput($pipe, array(
                'inputType' => FTDS::INPUT_TYPE_DELIMITER_SEPARATED,
                'outputAs' => FTDS::OUTPUT_AS_WIRE_DATA,
                'delimiter' => '|',
                'toStringString' => 'People',
            ));
            $this->assertArray($pipeWire);
            $this->assertIdentical(count($pipeWire), 4);

        $this->newTest('Line-break separated');

            $line = $this->getSrc('line-separated.txt');
            $lineWire = FTDS::parseInput($line, array(
                'inputType' => FTDS::INPUT_TYPE_LINE_SEPARATED,
                'outputAs' => FTDS::OUTPUT_AS_WIRE_DATA,
                'toStringString' => 'People',
            ));
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
            $this->assertIdentical($f->delimiter, FTDS::DEFAULT_DELIMITER, 'delimiter DEFAULT');

        $this->newTest('inputType');

            $f->inputType = FTDS::INPUT_TYPE_YAML;
            $f->save();
            $fs = $this->fields->get($name);
            $this->assertIdentical($fs->inputType, FTDS::INPUT_TYPE_YAML, 'inputType YAML');

            $f->inputType = FTDS::INPUT_TYPE_MATRIX;
            $f->save();
            $fs = $this->fields->get($name);
            $this->assertIdentical($fs->inputType, FTDS::INPUT_TYPE_MATRIX, 'inputType MATRIX');

            $f->inputType = FTDS::INPUT_TYPE_DELIMITER_SEPARATED;
            $f->save();
            $fs = $this->fields->get($name);
            $this->assertIdentical($fs->inputType, FTDS::INPUT_TYPE_DELIMITER_SEPARATED, 'inputType COMMA_SEPARATED');

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
