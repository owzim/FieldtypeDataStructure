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


    function caseDataTypesOnField() {

        $start = microtime(true);

        $fn = 'fieldtypeObjectTest';
        $pn = 'fieldtype-object-tests';
        $tn = 'fieldtype-object-tests';

        $m = $this->modules->get('FieldtypeDataStructure');

        $f = $this->fields->get($fn);
        if (!$f) {
            $f = new Field();
        }
        $f->type = 'FieldtypeDataStructure';
        $f->name = $fn;
        $f->save();

        $t = $this->templates->get($tn);
        if (!$t) { $t = new Template(); }

        $t->name = $tn;

        $fg = $this->fieldgroups->get($tn);
        if (!$fg) { $fg = new FieldGroup(); }

        $fg->name = $tn;
        $fg->save();
        $fg->add($this->fields->get('title'));
        $fg->add($f);
        $fg->save();

        $t->fields = $fg;

        $t->save();

        $p = $this->pages->get("/$pn/");
        // return;
        if (!$p->id) { $p = new Page(); }

        $p->template = $t;
        $p->name = $pn;
        $p->title = $pn;
        // $p->save();
        $p->parent = $this->pages->get('/');
        $p->save();


        $this->newTest('YAML WireArray');

            $people = $this->getSrc('people.yaml');


            /**
             * YAML WIRE
             */

            $f->inputType = FTDS::INPUT_TYPE_YAML;
            $f->outputAs = FTDS::OUTPUT_AS_WIRE_DATA;
            $f->save();

            $p = $this->pages->get("/$pn/"); $p->of(false);

            $p->$fn = $people; $p->save(); $p->of(true);

            $yamlPeopleWire = $p->$fn;

            $this->assertInstanceOf($yamlPeopleWire, self::T('FTDSArray'));
            $this->assertInstanceOf($yamlPeopleWire[0], self::T('FTDSData'));
            $this->assertSame((string) $yamlPeopleWire, "$fn (2)");
            // ChromePhp::log('$yamlPeopleWire', $yamlPeopleWire);

            $m->uncache($p, $f);


            /**
             * YAML ASSOC
             */

            $f->inputType = FTDS::INPUT_TYPE_YAML;
            $f->outputAs = FTDS::OUTPUT_AS_ASSOC;
            $f->save();

            $p = $this->pages->get("/$pn/"); $p->of(false);
            $p->$fn = $people; $p->save(); $p->of(true);

            $yamlPeopleAssoc = $p->$fn;

            $this->assertArray($yamlPeopleAssoc);
            $this->assertArray($yamlPeopleAssoc[0]);

            $m->uncache($p, $f);


            /**
             * YAML OBJECT
             */

            $f->inputType = FTDS::INPUT_TYPE_YAML;
            $f->outputAs = FTDS::OUTPUT_AS_OBJECT;
            $f->save();

            $p = $this->pages->get("/$pn/"); $p->of(false);
            $p->$fn = $people; $p->save(); $p->of(true);

            $yamlPeopleObject = $p->$fn;

            $this->assertArray($yamlPeopleObject);
            $this->assertObject($yamlPeopleObject[0]);

            $m->uncache($p, $f);


        $this->newTest('Mixed YAML Array');

            /**
             * YAML MIXED
             */

            $people = $this->getSrc('faulty-people.yaml');

            $f->inputType = FTDS::INPUT_TYPE_YAML;
            $f->outputAs = FTDS::OUTPUT_AS_WIRE_DATA;
            $f->save();

            $p = $this->pages->get("/$pn/"); $p->of(false);
            $p->$fn = $people; $p->save(); $p->of(true);

            $yamlPeopleWire = $p->$fn;

            $this->assertArray($yamlPeopleWire);

            $m->uncache($p, $f);


        $this->newTest('Comma Matrix');

            $people = $this->getSrc('matrix.txt');

            $f->inputType = FTDS::INPUT_TYPE_MATRIX;
            $f->outputAs = FTDS::OUTPUT_AS_WIRE_DATA;
            $f->delimiter = FTDS::DEFAULT_DELIMITER;
            $f->save();

            $p = $this->pages->get("/$pn/"); $p->of(false);
            $p->$fn = $people; $p->save(); $p->of(true);

            $matrixWire = $p->$fn;

            $this->assertArray($matrixWire);
            $this->assertIdentical(count($matrixWire), 4);
            $this->assertIdentical(count($matrixWire[0]), 4);

            $m->uncache($p, $f);


        $this->newTest('Pipe Matrix');

            $people = $this->getSrc('pipe-matrix.txt');

            $f->inputType = FTDS::INPUT_TYPE_MATRIX;
            $f->outputAs = FTDS::OUTPUT_AS_WIRE_DATA;
            $f->delimiter = '|';
            $f->save();

            $p = $this->pages->get("/$pn/"); $p->of(false);
            $p->$fn = $people; $p->save(); $p->of(true);

            $pipeMatrixWire = $p->$fn;

            $this->assertArray($pipeMatrixWire);
            $this->assertIdentical(count($pipeMatrixWire), 4);
            $this->assertIdentical(count($pipeMatrixWire[0]), 4);

            $m->uncache($p, $f);


        $this->newTest('Comma Matrix Object');

            $people = $this->getSrc('matrix-object.txt');

            $f->inputType = FTDS::INPUT_TYPE_MATRIX_OBJECT;
            $f->outputAs = FTDS::OUTPUT_AS_WIRE_DATA;
            $f->delimiter = FTDS::DEFAULT_DELIMITER;
            $f->save();

            $p = $this->pages->get("/$pn/"); $p->of(false);
            $p->$fn = $people; $p->save(); $p->of(true);

            $matrixObjectWire = $p->$fn;

            $this->assertInstanceOf($matrixObjectWire, self::T('FTDSArray'));
            $this->assertIdentical(count($matrixObjectWire), 4);
            $this->assertInstanceOf($matrixObjectWire[0], self::T('FTDSData'));
            $this->assertIdentical($matrixObjectWire[0]->name, 'Neo');
            $this->assertIdentical($matrixObjectWire->implode(',', 'name'), 'Neo,Trinity,Morpheus,Agent Smith');

            $m->uncache($p, $f);


        $this->newTest('Pipe Matrix Object');

            $people = $this->getSrc('pipe-matrix-object.txt');

            $f->inputType = FTDS::INPUT_TYPE_MATRIX_OBJECT;
            $f->outputAs = FTDS::OUTPUT_AS_WIRE_DATA;
            $f->delimiter = '|';
            $f->save();

            $p = $this->pages->get("/$pn/"); $p->of(false);
            $p->$fn = $people; $p->save(); $p->of(true);

            $pipeMatrixObjectWire = $p->$fn;

            $this->assertInstanceOf($pipeMatrixObjectWire, self::T('FTDSArray'));
            $this->assertIdentical(count($pipeMatrixObjectWire), 3);
            $this->assertInstanceOf($pipeMatrixObjectWire[0], self::T('FTDSData'));
            $this->assertIdentical($pipeMatrixObjectWire[0]->name, 'Neo');
            $this->assertIdentical($pipeMatrixObjectWire->implode(',', 'name'), 'Neo,Trinity,Morpheus');

            $m->uncache($p, $f);


        $this->newTest('Comma separated');

            $people = $this->getSrc('comma-separated.txt');

            $f->inputType = FTDS::INPUT_TYPE_DELIMITER_SEPARATED;
            $f->outputAs = FTDS::OUTPUT_AS_WIRE_DATA;
            $f->delimiter = FTDS::DEFAULT_DELIMITER;
            $f->save();

            $p = $this->pages->get("/$pn/"); $p->of(false);
            $p->$fn = $people; $p->save(); $p->of(true);

            $commaWire = $p->$fn;

            $this->assertArray($commaWire);
            $this->assertIdentical(count($commaWire), 4);

            $m->uncache($p, $f);


        $this->newTest('Pipe separated');

            $people = $this->getSrc('pipe-separated.txt');

            $f->inputType = FTDS::INPUT_TYPE_DELIMITER_SEPARATED;
            $f->outputAs = FTDS::OUTPUT_AS_WIRE_DATA;
            // $f->delimiter = FTDS::DEFAULT_DELIMITER;
            $f->delimiter = '|';
            $f->save();

            $p = $this->pages->get("/$pn/"); $p->of(false);
            $p->$fn = $people; $p->save(); $p->of(true);

            $pipeWire = $p->$fn;

            $this->assertArray($pipeWire);
            $this->assertIdentical(count($pipeWire), 4);

            $m->uncache($p, $f);


        $this->newTest('Line-break separated');


            $people = $this->getSrc('line-separated.txt');


            $f->inputType = FTDS::INPUT_TYPE_LINE_SEPARATED;
            $f->outputAs = FTDS::OUTPUT_AS_WIRE_DATA;
            // $f->delimiter = FTDS::DEFAULT_DELIMITER;
            // $f->delimiter = '|';
            $f->save();

            $p = $this->pages->get("/$pn/"); $p->of(false);
            $p->$fn = $people; $p->save(); $p->of(true);

            $lineWire = $p->$fn;

            $this->assertArray($lineWire);
            $this->assertIdentical(count($lineWire), 5);

            $m->uncache($p, $f);


        /**
         * delete everything
         */

        $p = $this->pages->get("/$pn/");
        if ($p->id) {
            $this->pages->delete($p);
        }

        if ($t = $this->templates->get($tn)) {
            $this->templates->delete($t);
        }

        if ($fg = $this->fieldgroups->get($tn)) {
            $this->fieldgroups->delete($fg);
        }

        if ($f = $this->fields->get($fn)) {
            $this->fields->delete($f);
        }

        $end = microtime(true);
        $ms = ($end-$start)*1000;

        // ChromePhp::log('$ms', $ms);

    }

    function caseFieldSettings() {
        $name = 'fieldtypeObjectTest';

        $f = $this->fields->get($name);
        if (!$f) {
            $f = new Field();
        }

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
