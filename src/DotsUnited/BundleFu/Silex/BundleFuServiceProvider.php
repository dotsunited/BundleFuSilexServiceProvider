<?php

/*
 * This file is part of BundleFuSilexServiceProvider.
 *
 * (c) 2015 Jan Sorgalla <jan.sorgalla@dotsunited.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DotsUnited\BundleFu\Silex;

use DotsUnited\BundleFu\Factory;
use DotsUnited\BundleFu\Twig\BundleFuExtension;
use Silex\Application;
use Silex\ServiceProviderInterface;

class BundleFuServiceProvider implements ServiceProviderInterface
{
    public function boot(Application $app) {}
    
    public function register(Application $app)
    {
        $app['bundlefu.factory'] = $app->share(function() use ($app) {
            $options = isset($app['bundlefu.options']) ? $app['bundlefu.options'] : array();
            $filters = isset($app['bundlefu.filters']) ? $app['bundlefu.filters'] : array();

            return new Factory($options, $filters);
        });

        if (!isset($app['bundlefu.twig.extension'])) {
            $app['bundlefu.twig.extension'] = $app->share(function() use ($app) {
                return new BundleFuExtension($app['bundlefu.factory']);
            });
        }

        if (isset($app['twig'])) {
            $app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
                $extension = $app['bundlefu.twig.extension'];

                if ($extension) {
                    $twig->addExtension($extension);
                }

                return $twig;
            }));
        }

        if (isset($app['bundlefu.class_path'])) {
            $app['autoloader']->registerNamespace('DotsUnited\\BundleFu', $app['bundlefu.class_path']);
        }

        if (isset($app['bundlefu.twig.class_path'])) {
            $app['autoloader']->registerNamespace('DotsUnited\\BundleFu\\Twig', $app['bundlefu.twig.class_path']);
        }
    }
}
