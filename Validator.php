<?php

namespace library;


Class Validator {

    /**
     * @var string
     */
    private $message;

    /**
     * @var int
     */
    private $len;

    final private function __construct(){}

    /**
     * @param array $data
     * @param array $rules
     * @return array|string
     */
    public static function execute(array $data, array $rules) {
        $validator = new static();

        foreach ($rules as $var => $rule) {
            if (!array_key_exists($var, $data)) throw new \LogicException("The variable $var is not present in the request!");
            $result = $validator->validate($data[$var], $rule);

            if (!$result) return $validator->message;
        }

        return $data;
    }

    /**
     * @param $variable
     * @param array $rules
     * @return bool
     */
    private function validate($variable, array $rules): bool {
        foreach ($rules as $rule) {
            if (preg_match('/\d+/', $rule, $matches)) {
                $rule = preg_replace('/\d+/', '', $rule);
                $this->len = (int) $matches[0];
            }

            $result = $this->$rule($variable);

            if (!$result) {
                $this->message = $this->getMessage($variable, $rule);
                return false;
            }
        }
        return true;
    }

    /**
     * @param $variable
     * @param string $rule
     * @return string
     */
    private function getMessage($variable, string $rule): string {
        switch ($rule) {
            case 'exists':
                return "The variable $variable is required!";
            case 'string':
                return "The variable $variable must be a string!";
            case 'minLen':
                return "The variable $variable must be at least $this->len characters long!";
            case 'maxLen':
                return "The variable $variable cannot be longer than $this->len characters!";
            case 'exactLen':
                return "The variable $variable must be exactly $this->len characters long!";
            case 'symbols':
                return "The variable $variable must contain only numbers or letters!";
            case 'integer':
                return "The variable $variable must be an integer!";
            case 'digits':
                return "The variable $variable must contain only digits!";
            case 'alpha':
                return "The variable $variable must contain only alphabetic characters!";
            case 'float':
                return "The variable $variable must be a float!";
            case 'array':
                return 'One of the variables passed must be an array!';
            case 'arrayCount':
                return "One of the arrays passed must contain $this->len elements!";
            case 'boolean':
                return "The variable $variable must be a boolean!";
            case 'decimal':
                return "The variable $variable must be a decimal!";
            case 'decimalLen':
                return "The variable $variable must have exactly $this->len characters after the dot!";
            case 'decimalLenMin':
                return "The variable $variable must have minimum $this->len characters after the dot!";
            case 'decimalLenMax':
                return "The variable $variable must have maximum $this->len characters after the dot!";
            default:
                throw new \LogicException("Invalid rule $rule passed for validation!");
                break;
        }
    }


    /**
     * @param $variable
     * @return bool
     */
    protected function exists($variable): bool {
        if ($variable !== '' && $variable !== null && $variable !== []) return true;
        return false;
    }

    /**
     * @param $variable
     * @return bool
     */
    protected function string($variable): bool {
        if (is_string($variable) && strlen($variable) > 0
            && gettype($variable) === 'string' && $variable !== '')
            return true;
        return false;
    }

    /**
     * @param $variable
     * @return bool
     */
    protected function maxLen(string $variable): bool {
        if (strlen($variable) < $this->len) return true;
        return false;
    }

    /**
     * @param $variable
     * @return bool
     */
    protected function minLen(string $variable): bool {
        if (strlen($variable) > $this->len) return true;
        return false;
    }

    /**
     * @param $variable
     * @return bool
     */
    protected function exactLen(string $variable): bool {
        if (strlen($variable) === $this->len) return true;
        return false;
    }

    /**
     * @param string $variable
     * @return bool
     */
    protected function symbols(string $variable): bool {
        if (!preg_match('/[<>/\\,\.&*()\[\]#$%^=\'"\?!\-+;:`]/', $variable)) return true;
        return false;
    }

    /**
     * @param $variable
     * @return bool
     */
    protected function integer($variable): bool {
        if (is_int($variable)) return true;
        return false;
    }

    /**
     * @param string $variable
     * @return bool
     */
    protected function digits(string $variable): bool {
        if (ctype_digit($variable)) return true;
        return false;
    }

    /**
     * @param string $variable
     * @return bool
     */
    protected function alpha(string $variable): bool {
        if (ctype_alpha($variable)) return true;
        return false;
    }

    /**
     * @param $variable
     * @return bool
     */
    protected function float($variable): bool {
        if (is_float($variable)) return true;
        return false;
    }

    /**
     * @param $variable
     * @return bool
     */
    protected function array($variable): bool {
        if (is_array($variable)) return true;
        return false;
    }

    /**
     * @param array $variable
     * @return bool
     */
    protected function arrayCount(array $variable): bool {
        if (\count($variable) === $this->len) return true;
        return false;
    }

    /**
     * @param $variable
     * @return bool
     */
    protected function boolean($variable): bool {
        if (is_bool($variable)) return true;
        return false;
    }

    /**
     * @param $variable
     * @return bool
     */
    protected function decimal($variable): bool {
        if (is_numeric($variable)) return true;
        return false;
    }

    /**
     * @param $variable
     * @return bool
     */
    protected function decimalLen($variable): bool {
        $variable = substr($variable, strpos($variable, '.') + 1);
        if (strlen($variable) === $this->len) return true;
        return false;
    }

    /**
     * @param $variable
     * @return bool
     */
    protected function decimalLenMin($variable): bool {
        $variable = substr($variable, strpos($variable, '.') +1 );
        if (strlen($variable) < $this->len) return true;
        return false;
    }

    /**
     * @param $variable
     * @return bool
     */
    protected function decimalLenMax($variable): bool {
        $variable = substr($variable, strpos($variable, '.') + 1);
        if (strlen($variable) > $this->len) return true;
        return false;
    }


}