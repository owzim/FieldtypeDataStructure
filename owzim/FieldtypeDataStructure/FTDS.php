<?php
namespace owzim\FieldtypeDataStructure;

use \owzim\FieldtypeDataStructure\Vendor\Spyc;

class FTDS {

    const OUTPUT_AS_ASSOC = 0;
    const OUTPUT_AS_OBJECT = 1;
    const OUTPUT_AS_WIRE_DATA = 2;

    const DEFAULT_OUTPUT_AS = 2;

    const INPUT_TYPE_YAML = 0;
    const INPUT_TYPE_MATRIX = 1;
    const INPUT_TYPE_DELIMITER_SEPARATED = 2;
    const INPUT_TYPE_LINE_SEPARATED = 3;
    const INPUT_TYPE_JSON = 4;
    const INPUT_TYPE_MATRIX_OBJECT = 5;

    const DEFAULT_INPUT_TYPE = 0;

    const DEFAULT_DELIMITER = ',';
    const DEFAULT_NEW_LINE_DELIMITER = "\n";


    /**
     * $defaultOptions
     *
     * @var array
     */
    protected static $defaultOptions = array(
        'inputType' => self::DEFAULT_INPUT_TYPE,
        'outputAs' => self::DEFAULT_OUTPUT_AS,
        'delimiter' => self::DEFAULT_DELIMITER,
        'toStringString' => '',
    );


    /**
     * check if an array is a numeric array
     *
     * @param  array  $array
     * @return boolean
     */
    public static function isArray(array $array) {
        if (!is_array($array)) return false;
        $len = count($array);
        $iterator = 0;
        foreach ($array as $key => $value) {
            if (!is_numeric($key)) return false;
            if ((int) $key !== $iterator++) { return false; }
        }
        return true;
    }


    /**
     * check if an array is an associative array
     *
     * @param  array   $array
     * @return boolean
     */
    public static function isAssoc(array $array) {
        if (!is_array($array)) return false;
        return !self::isArray($array);
    }


    /**
     * merge two objects
     *
     * @param  object $obj1
     * @param  object $obj2
     * @return object the object resulting from merge
     */
    public static function objectMerge($obj1, $obj2) {
        return (object) array_merge((array) $obj1, (array) $obj2);
    }


    /**
     * convert an assoc array to an object recursively
     * 
     * TODO: make this prettier
     * 
     * @param  array  $array
     * @return stdClass
     */
    public static function array2object(array $array) {
        $resultObj = new \stdClass;
        $resultArr = array();
        $hasIntKeys = false;
        $hasStrKeys = false;
        foreach ($array as $key => $value) {
            if (!$hasIntKeys) {
                $hasIntKeys = is_int($key);
            }
            if (!$hasStrKeys) {
                $hasStrKeys = is_string($key);
            }
            if ($hasIntKeys && $hasStrKeys) {
                $e = new \Exception(
                    'Current level has both int and str keys, thus it\'s impossible to keep arr or convert to obj');
                $e->vars = array('level' => $array);
                throw $e;
            }
            if ($hasStrKeys) {
                $resultObj->{$key} = is_array($value) ? self::array2object($value) : $value;
            } else {
                $resultArr[$key] = is_array($value) ? self::array2object($value) : $value;
            }
        }
        return ($hasStrKeys) ? $resultObj : $resultArr;
    }


    /**
     * convert an assoc array to a WireData/WireArray structure
     * 
     * TODO: make this prettier
     * 
     * @param  array  $array
     * @return WireData|WireArray
     */
    public static function array2wire(array $array) {
        $resultObj = new FTDSData;
        $resultArr = array();
        $resultWireArr = new FTDSArray;
        $hasIntKeys = false;
        $hasStrKeys = false;
        $wireArrAllowed = true;
        foreach ($array as $key => $value) {
            if (!$hasIntKeys) {
                $hasIntKeys = is_int($key);
            }
            if (!$hasStrKeys) {
                $hasStrKeys = is_string($key);
            }
            if ($hasIntKeys && $hasStrKeys) {
                $e = new \Exception(
                    'Current level has both int and str keys, thus it\'s impossible to keep arr or convert to obj');
                $e->vars = array('level' => $array);
                throw $e;
            }
            if ($hasStrKeys) {
                $resultObj->{$key} = is_array($value) || is_object($value) ? self::array2wire($value) : $value;
            } else {
                $result = is_array($value) || is_object($value) ? self::array2wire($value) : $value;
                if ($wireArrAllowed && is_object($result)) {
                    $resultWireArr->add($result);
                } else {
                    $wireArrAllowed = false;
                    $resultArr[$key] = $result;
                }
            }
        }
        return ($hasStrKeys) ? $resultObj : ($wireArrAllowed ? $resultWireArr : $resultArr);
    }


    /**
     * parseInput
     *
     * @param  string $string
     * @param  array  $options
     * @return mixed
     */
    public static function parseInput($string, $options = array()) {

        $options = (object) array_merge(self::$defaultOptions, $options);

        switch (true) {
            case $options->inputType === self::INPUT_TYPE_YAML:
                $value = Spyc::YAMLLoadString($string);
                break;
            case $options->inputType === self::INPUT_TYPE_MATRIX:
                $value = self::parseMatrix($string, $options->delimiter);
                break;
            case $options->inputType === self::INPUT_TYPE_MATRIX_OBJECT:
                $value = self::parseMatrixObject($string, $options->delimiter);
                break;
            case $options->inputType === self::INPUT_TYPE_DELIMITER_SEPARATED:
                $value = self::explodeAndTrim($string, $options->delimiter);
                break;
            case $options->inputType === self::INPUT_TYPE_LINE_SEPARATED:
                $value = self::explodeAndTrim($string, self::DEFAULT_NEW_LINE_DELIMITER);
                break;
            case $options->inputType === self::INPUT_TYPE_JSON:
                $value = json_decode($string);
                break;
            default:
                return $string;
                break;
        }

        return self::convert($value, $options->outputAs, $options->toStringString);
    }


    /**
     * convert into a WireData/WireArray or a stdClass/array structrue if
     * $outputAs says so
     *
     * @param  mixed  $value
     * @param  int $outputAs
     * @param  string $toStringString
     * @return mixed
     */
    public static function convert(
        $value,
        $outputAs = self::DEFAULT_OUTPUT_AS,
        $toStringString = ''
    ) {

        if (!$value) return $value;

        switch (true) {

            case $outputAs === self::OUTPUT_AS_ASSOC:
                return $value;

            case $outputAs === self::OUTPUT_AS_OBJECT:
                if(!is_array($value)) return $value;
                return self::array2object($value);

            case $outputAs === self::OUTPUT_AS_WIRE_DATA:
                if(!is_array($value)) return $value;
                $wire = self::array2wire($value);
                if (!is_array($wire)) {
                    $wire->toStringString = $toStringString;
                }
                return $wire;
        }
    }


    /**
     * split a string into an array and trim each item
     *
     * @param  string $string
     * @param  string $delimiter
     * @return array
     */
    public static function explodeAndTrim($string, $delimiter = self::DEFAULT_DELIMITER) {

        $arr = explode($delimiter, $string);
        $rtn = array();
        foreach ($arr as $key => $value) {
            if ($trimmed = trim($value)) {
                $rtn[] = $trimmed;
            }
        }
        return $rtn;
    }


    /**
     * parseMatrix
     *
     * @param  string $string
     * @param  string $colDelimiter
     * @param  string $lineDelimiter
     * @return array
     */
    public static function parseMatrix(
        $string,
        $colDelimiter,
        $lineDelimiter = self::DEFAULT_NEW_LINE_DELIMITER
    ) {

        $lines = self::explodeAndTrim($string, $lineDelimiter);
        $rtn = array();
        foreach ($lines as $line) {
            $cols = self::explodeAndTrim($line, $colDelimiter);
            if (count($cols) > 0) {
                $rtn[] = $cols;
            }
        }
        return $rtn;
    }


    /**
     * parse a matrix into an assoc array and use the first row as keys
     *
     * @param  string $string
     * @param  string $colDelimiter
     * @param  string $lineDelimiter
     * @return array                 associative array
     */
    public static function parseMatrixObject(
        $string,
        $colDelimiter,
        $lineDelimiter = self::DEFAULT_NEW_LINE_DELIMITER
    ) {

        $matrix = self::parseMatrix($string, $colDelimiter, $lineDelimiter);

        $keys = array_shift($matrix);

        $rtn = array();

        foreach ($matrix as $line) {
            $lineAssoc = array();
            $c = 0;
            foreach ($keys as $key) {
                $lineAssoc[$key] = $line[$c];
                $c++;
            }
            $rtn[] = $lineAssoc;
        }
        return $rtn;
    }
}
