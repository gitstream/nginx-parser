<?php

namespace gitstream\parser\nginx\tests;

use gitstream\parser\nginx\Block;
use gitstream\parser\nginx\Property;

class NginxMimmeTypesTest extends BaseTest
{
    public $filename = 'nginx/mime.types';

    public function testParser_Base()
    {
        $conf = $this->getData();

        $this->assertInternalType('object', $conf);

        $this->assertInternalType('array', $conf->children);

        $this->assertEquals(count($conf->children), 1);

        $this->assertInstanceOf('gitstream\parser\nginx\Block', $conf->children[0]);

        $this->assertEquals($conf->children[0]->name, 'types');
        $this->assertEquals($conf->children[0]->value, '');

        $this->assertInternalType('array', $conf->children[0]->children);

        $this->assertInstanceOf('gitstream\parser\nginx\Property', $conf->children[0]->children[0]);

        $this->assertEquals($conf->children[0]->children[0]->name, 'text/html');
        $this->assertEquals($conf->children[0]->children[0]->value, ['html', 'htm', 'shtml']);

        $this->assertEquals($conf->children[0]->children[1]->name, 'text/css');
        $this->assertEquals($conf->children[0]->children[1]->value, 'css');
    }
}
