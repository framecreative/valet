<?php

use Illuminate\Container\Container;
use Valet\Ngrok;
use function Valet\user;

class NgrokTest extends Yoast\PHPUnitPolyfills\TestCases\TestCase
{
    public function set_up()
    {
        $_SERVER['SUDO_USER'] = user();

        Container::setInstance(new Container);
    }

    public function tear_down()
    {
        Mockery::close();
    }

    public function test_it_matches_correct_share_tunnel()
    {
        $tunnels = [
            (object) [
                'proto' => 'https',
                'config' => (object) [
                    'addr' => 'http://mysite.test:80',
                ],
                'public_url' => 'http://bad-proto.ngrok.io/',
            ],
            (object) [
                'proto' => 'http',
                'config' => (object) [
                    'addr' => 'http://nottherightone.test:80',
                ],
                'public_url' => 'http://bad-site.ngrok.io/',
            ],
            (object) [
                'proto' => 'http',
                'config' => (object) [
                    'addr' => 'http://mysite.test:80',
                ],
                'public_url' => 'http://right-one.ngrok.io/',
            ],
        ];

        $ngrok = new Ngrok;
        $this->assertEquals('http://right-one.ngrok.io/', $ngrok->findHttpTunnelUrl($tunnels, 'mysite'));
    }

    public function test_it_checks_against_lowercased_domain()
    {
        $tunnels = [
            (object) [
                'proto' => 'http',
                'config' => (object) [
                    'addr' => 'http://mysite.test:80',
                ],
                'public_url' => 'http://right-one.ngrok.io/',
            ],
        ];

        $ngrok = new Ngrok;
        $this->assertEquals('http://right-one.ngrok.io/', $ngrok->findHttpTunnelUrl($tunnels, 'MySite'));
    }
}
