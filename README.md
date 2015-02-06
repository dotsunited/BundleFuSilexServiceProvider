Silex service provider for BundleFu
===================================

[![Build Status](https://travis-ci.org/dotsunited/BundleFuSilexServiceProvider.svg?branch=master)](http://travis-ci.org/dotsunited/BundleFuSilexServiceProvider)

The BundleFuSilexServiceProvider integrates [BundleFu](https://github.com/dotsunited/BundleFu) into the [Silex micro-framework](http://silex.sensiolabs.org/).

Installation
------------

BundleFuSilexServiceProvider can be installed using the [Composer](http://packagist.org) tool. You can either add `dotsunited/bundlefu-silex-service-provider` to the dependencies in your composer.json, or if you want to install BundleFuSilexServiceProvider as standalone, go to the main directory and run:

```bash
$ wget http://getcomposer.org/composer.phar 
$ php composer.phar install
```

You can then use the composer-generated autoloader to access the BundleFuSilexServiceProvider classes:

```php
<?php
require 'vendor/autoload.php';
?>
```

Usage
-----

Register the BundleFuServiceProvider to your Silex application:

```php
<?php
$app = new \Silex\Application();

$app->register(new DotsUnited\BundleFu\Silex\BundleFuServiceProvider());
?>
```

You can now use the `bundlefu.factory` service to create bundles in your application:

```php
<?php
$bundle = $app['bundlefu.factory']->createBundle();
?>
```

To configure the factory, you can pass the `bundlefu.options` and `bundlefu.filters` parameters:

```php
<?php
$app->register(new DotsUnited\BundleFu\Silex\BundleFuServiceProvider(), array(
    'bundlefu.options' => array(
        'bypass' => true
    ),
    'bundlefu.filters' => array(
        'js_closure_compiler' => new \DotsUnited\BundleFu\Filter\ClosureCompilerService()
    )
));
?>
```

### Twig ###

The service provider automatically registers the [BundleFu twig extension](https://github.com/dotsunited/BundleFuTwigExtension) if Twig is available (ensure that you register the BundleFuServiceProvider **after** the TwigServiceProvider in your application).

If do not want the extension to be registered, set `bundlefu.twig.extension` with the value `false` as a parameter:

```php
<?php
$app->register(new DotsUnited\BundleFu\Silex\BundleFuServiceProvider(), array(
    'bundlefu.twig.extension' => false
));
?>
```

License
-------

BundleFuSilexServiceProvider is released under the [MIT License](https://github.com/dotsunited/BundleFuSilexServiceProvider/blob/master/LICENSE).
