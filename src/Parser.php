<?php

namespace gitstream\parser\nginx;

class Parser
{
    public function load($filename)
    {
        $text = file_get_contents($filename);
        $text = str_replace(["\r\n", "\n\r", "\r"], "\n", $text);
        $text = preg_replace('/[[:blank:]]+/', ' ', $text);

        $lines = explode("\n", $text);

        return $this->parseBlock($lines);
    }

    private function stripComment($line)
    {
        /**
         * @todo Support # in string
         */
        if (strpos($line, '#') === false) {
            return $line;
        }
        return rtrim(substr($line, 0, strpos($line, '#')));
    }

    protected function parseBlock($lines, $current = 0)
    {
        //printf("parseBlock: line %s\n", $current);

        $rootBlock = new Block('[root]');

        for ($i = $current, $count = count($lines); $i < $count; $i++) {
            $line = trim($lines[$i]);

            $line = $this->stripComment($line);

            if ($line === '' || $line[0] === '#') {
                //printf("parseBlock: skip line %s\n", $i);
                continue;
            }

            if ($line[strlen($line) - 1] === '{') {
                list($block, $i) = $this->parseBlock($lines, ++$i);

                if ($block !== false) {
                    list($name, $value) = $this->parseLine($line);
                    $block->name = $name;
                    $block->value = $value;
                    $rootBlock->addBlock($block);
                }
                continue;
            }

            if ($line === '}') {
                return [$rootBlock, $i];
            }

            list($property, $i) = $this->parseProperty($lines, $i);

            if ($property !== false) {
                $rootBlock->addProperty($property);
            }
        }

        return $rootBlock;
    }

    protected function parseProperty($lines, $current)
    {
        //printf("parseProperty: line %s\n", $current);

        $string = '';

        // Multi line properties
        for ($i = $current, $count = count($lines); $i < $count; $i++) {
            $line = trim($lines[$i]);
            $line = $this->stripComment($line);
            $string .= trim($line, '\'') . ' ';
            if ($line[strlen($line) - 1] === ';') {
                break;
            }
        }

        list($name, $value) = $this->parseLine(rtrim($string));

        /**
         * @todo Handle include property? (include sites-enabled/*.conf)
         */

        return [new Property($name, $value), $i];
    }

    protected function parseLine($line)
    {
        //printf('parseLine: line %s', $line);

        $line = $this->stripComment($line);
        $line = rtrim($line, ';{');
            //$items = explode(' ', $line);
        $items = preg_split(
            "/[\s,]*\\\"([^\\\"]+)\\\"[\s,]*|" . "[\s,]*'([^']+)'[\s,]*|" . "[\s,]+/",
            $line,
            0,
            PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
        );

        $name = array_shift($items);
        if (empty($items)) {
            $value = '';
        } else {
            $value = count($items) === 1 ? $items[0] : array_map(function ($item) {
                $item = preg_replace('/[[:blank:]]+/', ' ', $item);
                return trim($item);
            }, $items);
        }

        return [$name, $value];
    }
}

class Property
{
    public $name;
    public $value;

    public function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }
}

class Block
{
    public $name;
    public $value;

    public function __construct($name, $value = '')
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @var Block[]|Property[]
     */
    public $children = [];

    public function addProperty(Property $property)
    {
        $this->children[] = $property;
    }

    public function addBlock(Block $block)
    {
        $this->children[] = $block;
    }
}
