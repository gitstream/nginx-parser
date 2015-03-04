<?php

namespace gitstream\parser\nginx\tests;

use gitstream\parser\nginx\Parser;

abstract class BaseTest extends \PHPUnit_Framework_TestCase
{
    private $data;

    public $filename;

    public function getData()
    {
        if ($this->data === null) {
            $parser = new Parser();
            $this->data = $parser->load(__DIR__ . '/fixtures/' . $this->filename);
        }

        return $this->data;
    }
}
