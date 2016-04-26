<?php
/**
 * This file is part of DbDiff, a simple database-diff tool.
 *
 * MIT License
 * Copyright (c) 2012-2016 Julien Ballestracci
 */

// composer <3
require_once __DIR__ .'/vendor/autoload.php';

use Fwk\Core\Application;
use Fwk\Core\Plugins\RequestMatcherPlugin;
use Fwk\Core\Plugins\ViewHelperPlugin;
use Nitronet\Fwk\Twig\TwigPlugin;
use Fwk\Core\Action\ProxyFactory;

// create application
$app = new Application('DbDiff');

// add default plugins
$app->plugin(new RequestMatcherPlugin())
    ->plugin(new ViewHelperPlugin())
    ->plugin(new TwigPlugin(array(
        'directory' => __DIR__ .'/templates',
        'twig' => array(
            'debug' => true
        )
    )
))
// register our single controller
->register('Diff', ProxyFactory::factory('DbDiff\Controller:show'))
->setDefaultAction('Diff');

// execute !
$response = $app->run();
if ($response instanceof \Symfony\Component\HttpFoundation\Response) {
    $response->send();
}