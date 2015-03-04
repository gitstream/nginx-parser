<?php

namespace gitstream\parser\nginx\tests;

use gitstream\parser\nginx\Block;
use gitstream\parser\nginx\Property;

class NginxLogFormatTest extends BaseTest
{
    public $filename = 'nginx/custom/log_format.conf';

    public function testBase()
    {
        $conf = $this->getData();

        $this->assertInternalType('object', $conf);

        $this->assertInternalType('array', $conf->children);

        $this->assertEquals(1, count($conf->children));

        $this->assertInstanceOf('gitstream\parser\nginx\Block', $conf->children[0]);

        $this->assertEquals('server',$conf->children[0]->name );
        $this->assertEquals('', $conf->children[0]->value);

        $this->assertEquals(6, count($conf->children[0]->children));

        $this->assertInternalType('array', $conf->children[0]->children);

        $this->assertInstanceOf('gitstream\parser\nginx\Property', $conf->children[0]->children[0]);

        $this->assertEquals('log_format', $conf->children[0]->children[0]->name);
        $this->assertEquals([
            'compression',
            '$remote_addr - $remote_user [$time_local] "$request" $status $bytes_sent "$http_referer" "$http_user_agent" "$gzip_ratio"'
        ], $conf->children[0]->children[0]->value);

        $this->assertEquals('access_log', $conf->children[0]->children[1]->name);
        $this->assertEquals([
            '/spool/logs/nginx-access.log',
            'compression',
            'buffer=32k'
        ], $conf->children[0]->children[1]->value);

        $this->assertEquals('access_log', $conf->children[0]->children[2]->name);
        $this->assertEquals([
            '/path/to/log.gz',
            'combined',
            'gzip',
            'flush=5m'
        ], $conf->children[0]->children[2]->value);

        $this->assertEquals('access_log', $conf->children[0]->children[3]->name);
        $this->assertEquals('/spool/vhost/logs/$host', $conf->children[0]->children[3]->value);

        $this->assertEquals('log_format', $conf->children[0]->children[4]->name);
        $this->assertEquals([
            'combined',
            '$remote_addr - $remote_user [$time_local] "$request" $status $body_bytes_sent "$http_referer" "$http_user_agent"'
        ], $conf->children[0]->children[4]->value);

        $this->assertEquals('open_log_file_cache', $conf->children[0]->children[5]->name);
        $this->assertEquals([
            'max=1000',
            'inactive=20s',
            'valid=1m',
            'min_uses=2'
        ], $conf->children[0]->children[5]->value);
    }
}
