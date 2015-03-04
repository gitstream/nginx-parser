<?php

namespace gitstream\parser\nginx\tests;

use gitstream\parser\nginx\Block;
use gitstream\parser\nginx\Property;

class NginxTest extends BaseTest
{
    public $filename = 'nginx/nginx.conf';

    public function testBase()
    {
        $conf = $this->getData();

        $this->assertInternalType('object', $conf);

        $this->assertObjectHasAttribute('name', $conf);

        $this->assertEquals('[root]', $conf->name);

        $this->assertInternalType('array', $conf->children);

        $this->assertEquals(5, count($conf->children));

        $this->assertInstanceOf('gitstream\parser\nginx\Property', $conf->children[0]);

        $this->assertEquals('user', $conf->children[0]->name);
        $this->assertEquals('www-data', $conf->children[0]->value);
    }

    public function testBlocks()
    {
        $conf = $this->getData();

        $blockEvents = new Block('events');
        $blockEvents->addProperty(new Property('worker_connections', 768));

        $this->assertInstanceOf('gitstream\parser\nginx\Block', $conf->children[3]);
        $this->assertEquals($blockEvents, $conf->children[3]);
    }
}
