<?php
namespace owzim\FieldtypeObject;

use \owzim\FieldtypeObject\Vendor\Spyc;
class FTO {

    const OUTPUT_AS_ASSOC = 0;
    const OUTPUT_AS_OBJECT = 1;
    const OUTPUT_AS_WIRE_DATA = 2;
    const DEFAULT_OUTPUT_AS = 2;
    
    const INPUT_TYPE_YAML = 0;
    const INPUT_TYPE_MATRIX = 1;
    const INPUT_TYPE_COMMA_SEPARATED = 2;
    const INPUT_TYPE_LINE_SEPARATED = 3;
    const INPUT_TYPE_JSON = 4;
    const DEFAULT_INPUT_TYPE = 0;

    public static function isArray($array) {
        if (!is_array($array)) return false;
        $len = count($array);
        $iterator = 0;
        foreach ($array as $key => $value) {
            if (!is_numeric($key)) return false;
            if ((int) $key !== $iterator++) { return false; }
        }
        return true;
    }

    public static function isAssoc($array) {
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
     * convert an assoc array to an object recursively
     *
     * @param  array  $array
     * @return stdClass
     */
    public static function array2wire(array $array) {
        $resultObj = new FTOData;
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
                $resultObj->{$key} = is_array($value) ? self::array2wire($value) : $value;
            } else {
                $resultArr[$key] = is_array($value) ? self::array2wire($value) : $value;
            }
        }
        return ($hasStrKeys) ? $resultObj : $resultArr;
    }

    public static function array2wireExt(array $array) {
        $resultObj = new FTOData;
        $resultArr = array();
        $resultWireArr = new FTOArray;
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
                $resultObj->{$key} = is_array($value) || is_object($value) ? self::array2wireExt($value) : $value;
            } else {
                $result = is_array($value) || is_object($value) ? self::array2wireExt($value) : $value;
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

    
    public static function parseInput($string, $inputType, $outputAs, $toStringString = '') {

        switch (true) {
            case $inputType === self::INPUT_TYPE_YAML:
                $string = Spyc::YAMLLoadString($string);
                break;
            case $inputType === self::INPUT_TYPE_MATRIX:
                $string = self::parseMatrix($string);
                break;
            case $inputType === self::INPUT_TYPE_COMMA_SEPARATED:
                $string = self::parseCols($string);
                break;
            case $inputType === self::INPUT_TYPE_LINE_SEPARATED:
                $string = self::parseLines($string);
                break;
            case $inputType === self::INPUT_TYPE_JSON:
                $string = json_decode($string);
                break;
            default:
                return $string;
                break;
        }
        
        return self::convert($string, $outputAs, $toStringString);
    }
    
    
    
    public static function convert($value, $outputAs = self::DEFAULT_OUTPUT_AS, $toStringString = '') {

        if (!$value) return $value;

        switch (true) {

            case $outputAs === self::OUTPUT_AS_ASSOC:
                return $value;

            case $outputAs === self::OUTPUT_AS_OBJECT:
                if(!is_array($value)) return $value;
                return self::array2object($value);

            case $outputAs === self::OUTPUT_AS_WIRE_DATA:
                if(!is_array($value)) return $value;
                $wire = self::array2wireExt($value);
                if (!is_array($wire)) {
                    $wire->toStringString = $toStringString;
                }                
                return $wire;
        }
    }
    

    public static function parseLines($string, $separator = "/\n/") {
        $arr = preg_split($separator, $string);
        $rtn = array();
        foreach ($arr as $key => $value) {
            if ($trimmed = trim($value)) {
                $rtn[] = $trimmed;
            }
        }
        return $rtn;
    }
    
    public static function parseCols($string, $separator = ',') {
        $arr = explode($separator, $string);
        $rtn = array();
        foreach ($arr as $key => $value) {
            if ($trimmed = trim($value)) {
                $rtn[] = $trimmed;
            }
        }
        return $rtn;
    }
    
    public static function parseMatrix($string) {
        $lines = self::parseLines($string);
        $rtn = array();
        foreach ($lines as $line) {
            $cols = self::parseCols($line);
            if (count($cols) > 0) {
                $rtn[] = $cols;
            }
        }
        return $rtn;
    }
}
