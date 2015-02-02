<?php

/*
 * This file is part of BundleFuSilexServiceProvider.
 *
 * (c) 2015 Jan Sorgalla <jan.sorgalla@dotsunited.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DotsUnited\BundleFu\Tests\Silex;

use DotsUnited\BundleFu\Silex\BundleFuServiceProvider;
use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\HttpFoundation\Request;

class BundleFuServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {

    }

    public function testRegister()
    {
        $app = new Application();

        $cssFilter = $this->getMock('DotsUnited\\BundleFu\\Filter\\FilterInterface');
        $jsFilter = $this->getMock('DotsUnited\\BundleFu\\Filter\\FilterInterface');

        $app->register(new BundleFuServiceProvider(), array(
            'bundlefu.options' => array(
                'bypass' => true,
                'js_filter' => 'js_filter',
                'css_filter' => $cssFilter
            ),
            'bundlefu.filters' => array(
                'js_filter' => $jsFilter,
            )
        ));

        $app->get('/', function ($name) {
            return 'BundleFuServiceProviderTest';
        });

        $request = Request::create('/');
        $app->handle($request);

        $this->assertInstanceOf('DotsUnited\\BundleFu\\Factory', $app['bundlefu.factory']);
        $this->assertInstanceOf('DotsUnited\\BundleFu\\Twig\\BundleFuExtension', $app['bundlefu.twig.extension']);

        $bundle = $app['bundlefu.factory']->createBundle();

        $this->assertSame($cssFilter, $bundle->getCssFilter());
        $this->assertSame($jsFilter, $bundle->getJsFilter());
        $this->assertTrue($bundle->getBypass());
    }

    public function testRegisterTwigExtension()
    {
        $app = new Application();

        $app->register(new TwigServiceProvider());
        $app->register(new BundleFuServiceProvider());

        $app->get('/', function ($name) {
            return 'BundleFuServiceProviderTest';
        });

        $request = Request::create('/');
        $app->handle($request);

        $this->assertInstanceOf('DotsUnited\\BundleFu\\Twig\\BundleFuExtension', $app['twig']->getExtension('bundlefu'));
    }

    public function testPreventRegisterTwigExtension()
    {
        $app = new Application();

        $app->register(new TwigServiceProvider());
        $app->register(new BundleFuServiceProvider(), array(
            'bundlefu.twig.extension' => false
        ));

        $app->get('/', function ($name) {
            return 'BundleFuServiceProviderTest';
        });

        $request = Request::create('/');
        $app->handle($request);

        $this->assertFalse($app['bundlefu.twig.extension']);
        $this->assertFalse($app['twig']->hasExtension('bundlefu'));
    }
}
