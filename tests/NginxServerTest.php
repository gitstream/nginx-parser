<?php

namespace gitstream\parser\nginx\tests;

use gitstream\parser\nginx\Block;
use gitstream\parser\nginx\Property;

class NginxServerTest extends BaseTest
{
    public $filename = 'nginx/sites-available/default';

    public function testBase()
    {
        $conf = $this->getData();

        $this->assertInternalType('object', $conf);

        $this->assertInternalType('array', $conf->children);

        $this->assertEquals(count($conf->children), 1);

        $this->assertInstanceOf('gitstream\parser\nginx\Block', $conf->children[0]);

        $this->assertEquals('server',$conf->children[0]->name );
        $this->assertEquals('', $conf->children[0]->value);

        $blockServer = $conf->children[0]->children;

        $this->assertInternalType('array', $blockServer);
        $this->assertEquals(6, count($blockServer));

        $this->assertInstanceOf('gitstream\parser\nginx\Property', $blockServer[0]);

        $this->assertEquals('root', $blockServer[0]->name);
        $this->assertEquals('/usr/share/nginx/www', $blockServer[0]->value);

        $this->assertEquals('index', $blockServer[1]->name);
        $this->assertEquals([
            'index.html',
            'index.htm'
        ], $blockServer[1]->value);

        $this->assertEquals('server_name', $blockServer[2]->name);
        $this->assertEquals('localhost', $blockServer[2]->value);

        $this->assertEquals('location', $blockServer[3]->name);
        $this->assertEquals('/', $blockServer[3]->value);

        $this->assertEquals('location', $blockServer[4]->name);
        $this->assertEquals('/doc/', $blockServer[4]->value);

        $this->assertEquals('location', $blockServer[5]->name);
        $this->assertEquals([
            '~',
            '\.php$'
        ], $blockServer[5]->value);

        $blockLocationPHP = $blockServer[5]->children;
        $this->assertEquals(5, count($blockLocationPHP));

        $this->assertEquals('fastcgi_split_path_info', $blockLocationPHP[0]->name);
        $this->assertEquals('^(.+\.php)(/.+)$', $blockLocationPHP[0]->value);

        $this->assertEquals('fastcgi_pass', $blockLocationPHP[1]->name);
        $this->assertEquals('127.0.0.1:9000', $blockLocationPHP[1]->value);

        $this->assertEquals('fastcgi_pass', $blockLocationPHP[2]->name);
        $this->assertEquals('unix:/var/run/php5-fpm.sock', $blockLocationPHP[2]->value);

        $this->assertEquals('fastcgi_index', $blockLocationPHP[3]->name);
        $this->assertEquals('index.php', $blockLocationPHP[3]->value);

        $this->assertEquals('include', $blockLocationPHP[4]->name);
        $this->assertEquals('fastcgi_params', $blockLocationPHP[4]->value);
    }
}
