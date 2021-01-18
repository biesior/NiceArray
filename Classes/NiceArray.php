<?php

namespace Biesior\Utility;

/**
 * Class NiceArray
 *
 * Based on this topic {@link https://stackoverflow.com/a/57162076/1066240}
 *
 * @author Unknown
 * OOP implemented: (c) 2020 Marcus Biesioroff biesior@gmail.com
 */
class  NiceArray
{

    const SYNTAX_SHORT = 1;
    const SYNTAX_TRADITIONAL = 2;

    const DISPLAYMODE_MINIMAL = 1;
    const MODE_COMPACT = 2;
    const DISPLAYMODE_NORMAL = 3;

    private $output = '';
    private $syntax = NiceArray::SYNTAX_TRADITIONAL;
    private $displayMode = NiceArray::DISPLAYMODE_NORMAL;
    private $data = null;
    private $resolveObjects = false;
    private $resolveBooleans = true;
    private $useAnsiColors = false;

    private $eol = '<br>';
    private $space = "&nbsp;";
    private $arrayStart = 'array(';
    private $arrayEnd = ')';

    /**
     * NiceArray constructor.
     */
    public function __construct()
    {

        if (version_compare(phpversion(), '5.4', '>=')) {
            $this->syntax = NiceArray::SYNTAX_SHORT;
        } else {
            $this->syntax = NiceArray::SYNTAX_TRADITIONAL;
        }
        $this->setDefaults();
    }


    private function addOutput($output)
    {
        $this->output .= $output;
    }

    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Render array as it was PHP variable.
     * If pre-formatting is required ie. to display on HTML page wrap it with ```<pre>``` tag
     *
     * @param string $as Optional array name
     * @param bool   $resolveObjects if true objets will be resolved with print_r, however it will be probably NOT valid PHP code
     */
    public function renderArray($as = 'array', $resolveObjects = false)
    {
        $this->setDefaults();
        if (is_null($this->data)) {
            $this->data = array('Opppssss... you definitely forgot to add some data before render, use setData() method before rendering  and make sure the variable is not itself');
        }
        $this->addOutput($this->wrapCyan('$' . $as) . ' = ');
        $this->traverseChildren($this->data, 1);
        $this->addOutput(';');

        echo($this->output);
    }

    private function setDefaults()
    {
        $isCli = self::isCLI();
        $this->eol = ($isCli) ? PHP_EOL : "<br>";
        $this->space = ($isCli) ? ' ' : "&nbsp;";
        if ($this->displayMode == NiceArray::DISPLAYMODE_MINIMAL) {
            $this->eol = '';
            $this->space = '';
        }
        $array = $this->wrapYellow('array');
        $this->arrayStart = ($this->syntax == NiceArray::SYNTAX_SHORT) ? '[' : '' . $array . '(';
        $this->arrayEnd = ($this->syntax == NiceArray::SYNTAX_SHORT) ? ']' : ')';
    }

    /**
     * Renders children values depending if it's an array or value.
     *
     * @param mixed $array
     * @param int   $deep
     *
     * @param bool  $isParentAssoc
     *
     * @internal
     */
    private function traverseChildren($array, $deep = 1, $isParentAssoc = false)
    {


        $isAssoc = self::isAssoc($array);
        $indent = '';
        $indent_close = '';
        $this->addOutput($this->arrayStart);
        for ($i = 0; $i < $deep; $i++) {
            $indent .= $this->space . $this->space;
        }
        for ($i = 1; $i < $deep; $i++) {
            $indent_close .= $this->space . $this->space;
        }
        foreach ($array as $key => $value) {

            $this->addOutput($this->eol);
            $this->addOutput($indent);
            if ($isAssoc) {
                if (intval($key) === $key) {
                    $key = $this->wrapRed($key);
                    $this->addOutput("$key => ");
                } else {
                    $this->addOutput("{$this->wrapGreen("'$key'")} => ");
                }
            }
            if (is_string($value)) {
                $value = str_replace("'", "\\'", $value);
                $this->addOutput($this->wrapGreen("'$value'"));
            } elseif (is_array($value)) {
                $this->traverseChildren($value, ($deep + 1));
            } elseif (is_null($value)) {
                $this->addOutput('null');
            } elseif (is_bool($value)) {
                if ($this->resolveBooleans) {
                    $this->addOutput(($value)
                        ? $this->wrapYellow('true', 1)
                        : $this->wrapYellow('false'));
                } else {
                    $this->addOutput($this->wrapBlue($value));

                }
            } elseif (is_object($value)) {
                if (!$this->resolveObjects) {
                    $clazz = get_class($value);
                    $this->addOutput($this->wrapGreen("'$clazz Object (not resolved)'"));
                } else {
                    $lines = explode(PHP_EOL, print_r($value, true));
                    $newLines = array();
                    $linesCount = count($lines);
                    if ($linesCount > 0) {
                        $i = 1;
                        foreach ($lines as $n => $line) {
                            if ($n == 0) {
                                $newLines[] = $line;
                            } else {
                                $isLast = $i < ($linesCount);
                                if ($isLast) {
                                    $newLines[] = $indent . str_repeat($this->space, 9) . $line;
                                } elseif ($isLast && strlen($line) != 0) {
                                    $newLines[] = $line;
                                }
                            }
                            $i++;
                        }
                    }
                    $this->addOutput($this->wrapBlue("'" . implode($this->eol, $newLines) . "'"));
                }
            } elseif (is_numeric($value)) {
                $this->addOutput($this->wrapBlue($value));
            } else {
                $this->addOutput($value);
            }
            $this->addOutput(',');
        }
        if ($isParentAssoc) {
            $this->addOutput($this->arrayEnd);
        }

        $this->addOutput($this->eol . $indent_close . $this->arrayEnd);
    }

    /**
     * Try to determine if an array is associative
     *
     * @param array $arr Array to check
     *
     * @return bool
     */
    private static function isAssoc(array $arr)
    {
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    /**
     * @return bool
     */
    private static function isCLI()
    {
        return (php_sapi_name() === 'cli');
    }

    /**
     * @param bool $resolveObjects
     *
     * @return NiceArray
     */
    public function setResolveObjects($resolveObjects)
    {
        $this->resolveObjects = $resolveObjects;
        return $this;
    }

    /**
     * @param bool $resolveBooleans
     *
     * @return NiceArray
     */
    public function setResolveBooleans($resolveBooleans)
    {
        $this->resolveBooleans = $resolveBooleans;
        return $this;
    }

    public function ansiEscape($orTick = false)
    {
        return $this->wrapColor(null, "\e[0m");
    }

    public function wrapBlack($value, $effect = 1)
    {
        $effect = (!is_null($effect)) ? intval($effect) . ';' : null;
        return $this->wrapColor($value, "\e[{$effect}30m");
    }

    public function wrapRed($value, $effect = 1)
    {
        $effect = (!is_null($effect)) ? intval($effect) . ';' : null;
        return $this->wrapColor($value, "\e[{$effect}31m");
    }

    public function wrapGreen($value, $effect = 1)
    {
        $effect = (!is_null($effect)) ? intval($effect) . ';' : null;
        return $this->wrapColor($value, "\e[{$effect}32m");
    }

    public function wrapYellow($value, $effect = 1)
    {
        $effect = (!is_null($effect)) ? intval($effect) . ';' : null;
        return $this->wrapColor($value, "\e[{$effect}33m");
    }

    public function wrapBlue($value, $effect = 1)
    {
        $effect = (!is_null($effect)) ? intval($effect) . ';' : null;
        return $this->wrapColor($value, "\e[{$effect}34m");
    }

    public function wrapMagenta($value, $effect = 1)
    {
        $effect = (!is_null($effect)) ? intval($effect) . ';' : null;
        return $this->wrapColor($value, "\e[{$effect}35m");
    }

    public function wrapCyan($value, $effect = 1)
    {
        $effect = (!is_null($effect)) ? intval($effect) . ';' : null;
        return $this->wrapColor($value, "\e[{$effect}36m");
    }

    public function wrapWhite($value, $effect = 1)
    {
        $effect = (!is_null($effect)) ? intval($effect) . ';' : null;
        return $this->wrapColor($value, "\e[{$effect}37m");
    }

    protected function wrapColor($value, $color = null)
    {
        if (is_null($value) && is_null($color)) {
            return $color;
        }

        if ($this->useAnsiColors && !is_null($color)) {
            $value = $color . $value . "\e[0m";
        }

        return $value;
    }

    /**
     * Define how your output will be created
     *
     * {@see NiceArray::SYNTAX_SHORT} for short `[]` syntax
     * {@see NiceArray::SYNTAX_TRADITIONAL} for short `array()` syntax
     *
     *
     * @param int $syntax
     *
     * @return NiceArray
     */
    public function setSyntax($syntax)
    {
        $this->syntax = $syntax;
        $this->setDefaults();
        return $this;
    }

    /**
     * @param bool $useAnsiColors
     *
     * @return NiceArray
     */
    public function setUseAnsiColors($useAnsiColors)
    {
        $this->useAnsiColors = $useAnsiColors;
        return $this;
    }

    /**
     * @param int $displayMode
     *
     * @return NiceArray
     */
    public function setDisplayMode($displayMode)
    {
        $this->displayMode = $displayMode;
        return $this;
    }


}