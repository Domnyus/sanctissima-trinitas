<?php
namespace Domnyus;

use Exception;

class Validation
{
    public function must_be_not_empty($value, $key, $label = null, $message = null) : Exception | bool
    {
        if (empty($value)){
            return $this->throw_invalidation($key, "must not be empty", $label, $message);
        }
        return true;
    }

    public function must_be_integer($value, $key, $label = null, $message = null) : Exception | bool
    {
        if (is_int($value)){
            return $this->throw_invalidation($key, "must be an integer", $label, $message);
        }
        return true;
    }

    public function must_be_size_of($value, $key, $label = null, $message = null, $extras = null) : Exception | bool
    {
        if (!isset($extras[1])) {
            throw new Exception("The checked value must be between ()!", Constants::INTERNAL_SERVER_ERROR);
        }

        $result = eval("return (". strlen($value) ." {$extras[1]});");

        $evaluation = "";

        $pattern = "/\d+/";
        preg_match($pattern, $extras[1], $eval_value);

        if (strpos($extras[1], ">=") !== false) {
            $evaluation = "greater than or equals to $eval_value[0]";
        } else if (strpos($extras[1],"<=") !== false) {
            $evaluation = "lesser than or equals to $eval_value[0]";
        } else if (strpos($extras[1],">") !== false) {
            $evaluation = "greater than $eval_value[0]";
        } else if (strpos($extras[1],"<") !== false) {
            $evaluation = "less than $eval_value[0]";
        } else {
            $evaluation = "exactly $eval_value[0]";
        }

        if (!$result) {
            return $this->throw_invalidation($key, "must have $evaluation of length", $label, $message);
        }
        return true;
    }

    private function throw_invalidation($key, $end_message, $label = null, $message = null) : Exception
    {
        $exception_string = $message ?? "The field " . (!empty($label) ? $label : $key) . " $end_message!";
        return new Exception($exception_string, Constants::BAD_REQUEST);
    }
}