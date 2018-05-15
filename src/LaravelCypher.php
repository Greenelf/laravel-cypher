<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 24.06.17
 * Time: 19:46
 */

namespace Greenelf\LaravelCypher;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Greenelf\LaravelCypher\Exceptions\CypherException;

class LaravelCypher
{
    const DEFOULT_CYPHER_DIR = __DIR__."/cypher/";
    const DEFOULT_DIR = 'defoult_dir';
    private $cypherFilesDir;

    public function __construct($resourcePath = '')
    {
        if ($resourcePath === self::DEFOULT_DIR) {
            $this->cypherFilesDir = self::DEFOULT_CYPHER_DIR;
        } else {
            $this->_setCyphePath();
        }
    }

    public function createQuery($cypherFile, $variables = [], $rawData = [])
    {
        if ($this->_validateQueryParameters($cypherFile, $variables)) {
            return false;
        }

        return $this->_render($cypherFile, $variables, $rawData);
    }

    private function _render($cypherFile, $variables, $rawData)
    {
        $fileContent = file_get_contents(
            $this->cypherFilesDir.$cypherFile.'.cypher'
        );

        $variableRegexp = [];

        foreach (array_keys($variables) as $item) {
            array_push($variableRegexp, "/[$]".$item."/");
        }

        $prepareVariablesValues = $this->_prepareVariablesValues(
            array_values($variables)
        );

	if (count($variableRegexp) !== count($prepareVariablesValues)) {
            throw new \Exception('Variables have empty values!!');
        }

        if (count($rawData) > 0) {
            foreach ($rawData as $key => $value) {
                array_push($variableRegexp, "/[$]".$key."/");
                array_push($prepareVariablesValues, $value);
            }
        }
        $str = preg_replace(
            $variableRegexp,
            $prepareVariablesValues,
            $fileContent
        );

        return trim($str);
    }

    private function _prepareVariablesValues($variables)
    {
        $prepareVariables = [];
        foreach ($variables as $item) {
            if (is_string($item)) {
                $this->_createFloatValue($item);
            }

            if (is_string($item)) {
                $this->_formatString($item);
                array_push($prepareVariables, "'$item'");
            }
            if (is_int($item)) {
                array_push($prepareVariables, $item);
            }
            if (is_float($item) || is_double($item)) {
                array_push(
                    $prepareVariables,
                    number_format($item, 14, '.', '')
                );
            }
            if ($item === true) {
                array_push($prepareVariables, "true");
            }
            if ($item === false) {
                array_push($prepareVariables, "false");
            }
            if (is_object($item)) {
                throw new CypherException('typeError');
            }
        }

        return $prepareVariables;
    }

    private function _formatString(&$value)
    {
        $trashSymbols = [
            "'",
            '\\'
        ];

        foreach ($trashSymbols as $simbol) {
            $value = str_replace($simbol, '\\'.$simbol, $value);
        }
    }

    private function _setCyphePath()
    {
        $this->cypherFilesDir = resource_path().config('cypher.dir').'/';

        return $this->cypherFilesDir;
    }

    private function _validateQueryParameters($cypherFile, $variables)
    {
        $cypherFile = $this->cypherFilesDir.$cypherFile.'.cypher';

        if (!file_exists($cypherFile)) {
            Log::error($cypherFile.' - Not found!!!');

            return true;
        }

        return false;
    }

    private function _createFloatValue(&$string)
    {
        $re = '/^\d*[,.]\d*$/';

        preg_match_all($re, $string, $matches, PREG_SET_ORDER, 0);

        if ($matches === []) {
            return false;
        }

        $string = preg_replace('/,/', '.', $string);

        $string = floatval($string);

        return true;
    }
}

