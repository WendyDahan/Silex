<?php

use Silex\Application;
use Silex\Provider\AssetServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\LocaleServiceProvider;
use Silex\Provider\TranslationServiceProvider;

$app = new Application();

$app->register(new ServiceControllerServiceProvider());
$app->register(new AssetServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new TwigServiceProvider());
$app->register(new HttpFragmentServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new Silex\Provider\LocaleServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallbacks' => array('en')
));
return $app;
