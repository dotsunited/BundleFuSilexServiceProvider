<?php

/*
 * This file is part of BundleFuSilexServiceProvider.
 *
 * (c) 2011 Jan Sorgalla <jan.sorgalla@dotsunited.de>
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
            $oldTwig = $app->raw('twig');
            $app['twig'] = $app->share(function($c) use ($oldTwig, $app) {
                $twig = $oldTwig($c);

                $extension = $app['bundlefu.twig.extension'];

                if ($extension) {
                    $twig->addExtension($extension);
                }

                return $twig;
            });
        }

        if (isset($app['bundlefu.class_path'])) {
            $app['autoloader']->registerNamespace('DotsUnited\\BundleFu', $app['bundlefu.class_path']);
        }

        if (isset($app['bundlefu.twig.class_path'])) {
            $app['autoloader']->registerNamespace('DotsUnited\\BundleFu\\Twig', $app['bundlefu.twig.class_path']);
        }
    }
}
